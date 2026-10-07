<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ItSupportTicket;
use App\Models\User;
use App\Services\ItSupportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ItSupportController extends Controller
{
    public function __construct(
        private readonly ItSupportService $itSupportService
    ) {}

    public function index(Request $request): View
    {
        $query = ItSupportTicket::query()
            ->with(['employee.department', 'assignedTo'])
            ->latest();

        foreach (['status', 'priority', 'category', 'department_id', 'employee_id', 'assigned_to_user_id'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('employee', fn ($employeeQuery) => $employeeQuery->where('full_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        return view('admin.it-support.index', [
            'pageTitle' => 'IT Support - All Tickets',
            'tickets' => $query->paginate(20)->withQueryString(),
            'employees' => Employee::orderBy('full_name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'itEmployees' => $this->itSupportService->itEmployees(),
            'totalCount' => ItSupportTicket::count(),
            'openCount' => ItSupportTicket::where('status', 'Open')->count(),
            'assignedCount' => ItSupportTicket::where('status', 'Assigned')->count(),
            'inProgressCount' => ItSupportTicket::where('status', 'In Progress')->count(),
            'resolvedCount' => ItSupportTicket::where('status', 'Resolved')->count(),
            'criticalCount' => ItSupportTicket::where('priority', 'Critical')
                ->whereNotIn('status', ['Resolved', 'Closed'])->count(),
        ]);
    }

    public function show(ItSupportTicket $ticket): View
    {
        $ticket->load(['employee.department', 'assignedTo.employee', 'resolvedBy', 'comments.user']);

        return view('admin.it-support.show', [
            'pageTitle' => $ticket->ticket_number,
            'ticket' => $ticket,
            'itEmployees' => $this->itSupportService->itEmployees(),
        ]);
    }

    public function assign(Request $request, ItSupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'assigned_to_user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $assignedUser = User::findOrFail($data['assigned_to_user_id']);

        if (!$this->itSupportService->isItEmployee($assignedUser)) {
            throw ValidationException::withMessages([
                'assigned_to_user_id' => 'Tickets can only be assigned to active IT department employees.',
            ]);
        }

        abort_if(in_array($ticket->status, ['Resolved', 'Closed'], true), 422, 'Resolve/reopen workflow must be used for completed tickets.');

        $ticket->update([
            'assigned_to_user_id' => $assignedUser->id,
            'status' => 'Assigned',
        ]);

        $this->itSupportService->activity($ticket, $request->user(), 'Ticket assigned to ' . $assignedUser->name . '.');

        $this->itSupportService->notify(
            $assignedUser,
            'IT ticket assigned to you',
            $ticket->ticket_number . ': ' . $ticket->subject,
            route('it-support.show', $ticket),
            'info'
        );

        $this->notifyEmployee($ticket, 'Your IT ticket has been assigned', 'Assigned');

        return back()->with('success', 'Ticket assigned successfully.');
    }

    public function update(Request $request, ItSupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'priority' => ['required', 'in:' . implode(',', ItSupportTicket::PRIORITIES)],
            'status' => ['required', 'in:Open,Assigned,In Progress,Closed'],
        ]);

        if ($data['status'] === 'Assigned' && !$ticket->assigned_to_user_id) {
            throw ValidationException::withMessages([
                'status' => 'Assign the ticket to an IT employee before setting status to Assigned.',
            ]);
        }

        $ticket->update([
            'priority' => $data['priority'],
            'status' => $data['status'],
        ]);

        $this->itSupportService->activity(
            $ticket,
            $request->user(),
            'Admin updated priority to ' . $data['priority'] . ' and status to ' . $data['status'] . '.',
            true
        );

        $this->notifyEmployee($ticket, 'IT ticket updated', $data['status']);

        return back()->with('success', 'Ticket updated.');
    }

    public function resolve(Request $request, ItSupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'resolution' => ['required', 'string', 'max:10000'],
        ]);

        $ticket->update([
            'status' => 'Resolved',
            'resolution' => $data['resolution'],
            'resolved_by_user_id' => $request->user()->id,
            'resolved_at' => now(),
        ]);

        $this->itSupportService->activity($ticket, $request->user(), 'Ticket resolved: ' . $data['resolution']);
        $this->notifyEmployee($ticket, 'Your IT ticket has been resolved', 'Resolved');

        return back()->with('success', 'Ticket resolved successfully.');
    }

    public function reopen(Request $request, ItSupportTicket $ticket): RedirectResponse
    {
        abort_unless(in_array($ticket->status, ['Resolved', 'Closed'], true), 422, 'Only resolved or closed tickets can be reopened.');

        $ticket->update([
            'status' => 'Reopened',
        ]);

        $this->itSupportService->activity($ticket, $request->user(), 'Ticket reopened by Admin.');
        $this->notifyEmployee($ticket, 'Your IT ticket has been reopened', 'Reopened');

        return back()->with('success', 'Ticket reopened. Previous resolution details were preserved in the record/history.');
    }

    public function comment(Request $request, ItSupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'is_internal' => ['nullable', 'boolean'],
        ]);

        $isInternal = $request->boolean('is_internal');

        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'message' => $data['message'],
            'type' => 'comment',
            'is_internal' => $isInternal,
        ]);

        if (!$isInternal) {
            $this->notifyEmployee($ticket, 'IT support replied to your ticket', $ticket->status);
        }

        return back()->with('success', $isInternal ? 'Internal note added.' : 'Reply added.');
    }

    private function notifyEmployee(ItSupportTicket $ticket, string $title, string $status): void
    {
        $ticket->loadMissing('employee.user');

        if ($ticket->employee?->user) {
            $this->itSupportService->notify(
                $ticket->employee->user,
                $title,
                $ticket->ticket_number . ' is now ' . $status . '.',
                route('employee.it-support.show', $ticket),
                $status === 'Resolved' ? 'success' : 'info'
            );
        }
    }
}
