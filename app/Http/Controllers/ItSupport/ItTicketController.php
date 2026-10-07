<?php

namespace App\Http\Controllers\ItSupport;

use App\Http\Controllers\Controller;
use App\Models\ItSupportTicket;
use App\Services\ItSupportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItTicketController extends Controller
{
    public function __construct(
        private readonly ItSupportService $itSupportService
    ) {}

    public function dashboard(Request $request): View
    {
        $this->itSupportService->requireItEmployee($request->user());

        $userId = $request->user()->id;

        return view('it-support.technician.dashboard', [
            'pageTitle' => 'IT Support Dashboard',
            'openCount' => ItSupportTicket::whereIn('status', ['Open', 'Reopened'])
                ->whereNull('assigned_to_user_id')->count(),
            'assignedCount' => ItSupportTicket::where('assigned_to_user_id', $userId)
                ->whereIn('status', ['Assigned', 'In Progress', 'Reopened'])->count(),
            'inProgressCount' => ItSupportTicket::where('assigned_to_user_id', $userId)
                ->where('status', 'In Progress')->count(),
            'criticalCount' => ItSupportTicket::where('priority', 'Critical')
                ->where(function ($query) use ($userId) {
                    $query->whereNull('assigned_to_user_id')
                        ->orWhere('assigned_to_user_id', $userId);
                })
                ->whereNotIn('status', ['Resolved', 'Closed'])->count(),
            'resolvedTodayCount' => ItSupportTicket::where('resolved_by_user_id', $userId)
                ->whereDate('resolved_at', today())->count(),
            'resolvedTotalCount' => ItSupportTicket::where('resolved_by_user_id', $userId)
                ->where('status', 'Resolved')->count(),
            'unassignedTickets' => ItSupportTicket::with(['employee.department'])
                ->whereNull('assigned_to_user_id')
                ->whereIn('status', ['Open', 'Reopened'])
                ->latest()->take(8)->get(),
            'myTickets' => ItSupportTicket::with(['employee.department'])
                ->where('assigned_to_user_id', $userId)
                ->whereIn('status', ['Assigned', 'In Progress', 'Reopened'])
                ->latest()->take(8)->get(),
        ]);
    }

    public function open(Request $request): View
    {
        $this->itSupportService->requireItEmployee($request->user());

        $tickets = ItSupportTicket::with(['employee.department'])
            ->whereNull('assigned_to_user_id')
            ->whereIn('status', ['Open', 'Reopened'])
            ->latest()
            ->paginate(20);

        return view('it-support.technician.list', [
            'pageTitle' => 'Open IT Tickets',
            'heading' => 'Open / Reopened Tickets',
            'tickets' => $tickets,
        ]);
    }

    public function assigned(Request $request): View
    {
        $this->itSupportService->requireItEmployee($request->user());

        $tickets = ItSupportTicket::with(['employee.department'])
            ->where('assigned_to_user_id', $request->user()->id)
            ->whereIn('status', ['Assigned', 'In Progress', 'Reopened'])
            ->latest()
            ->paginate(20);

        return view('it-support.technician.list', [
            'pageTitle' => 'Assigned IT Tickets',
            'heading' => 'Assigned To Me',
            'tickets' => $tickets,
        ]);
    }

    public function resolved(Request $request): View
    {
        $this->itSupportService->requireItEmployee($request->user());

        $tickets = ItSupportTicket::with(['employee.department'])
            ->where('assigned_to_user_id', $request->user()->id)
            ->whereIn('status', ['Resolved', 'Closed'])
            ->latest('resolved_at')
            ->paginate(20);

        return view('it-support.technician.list', [
            'pageTitle' => 'Resolved IT Tickets',
            'heading' => 'My Resolved Tickets',
            'tickets' => $tickets,
        ]);
    }

    public function show(Request $request, ItSupportTicket $ticket): View
    {
        $this->assertTechnicianCanAccess($request, $ticket);

        $ticket->load(['employee.department', 'assignedTo.employee', 'resolvedBy', 'comments.user']);

        return view('it-support.technician.show', [
            'pageTitle' => $ticket->ticket_number,
            'ticket' => $ticket,
        ]);
    }

    public function assignSelf(Request $request, ItSupportTicket $ticket): RedirectResponse
    {
        $this->itSupportService->requireItEmployee($request->user());

        abort_unless(
            $ticket->assigned_to_user_id === null && in_array($ticket->status, ['Open', 'Reopened'], true),
            422,
            'This ticket is no longer available for self-assignment.'
        );

        $ticket->update([
            'assigned_to_user_id' => $request->user()->id,
            'status' => 'Assigned',
        ]);

        $this->itSupportService->activity($ticket, $request->user(), 'Ticket assigned to ' . $request->user()->name . '.');
        $this->notifyEmployee($ticket, 'Your IT ticket has been assigned', 'Assigned');

        return redirect()->route('it-support.show', $ticket)->with('success', 'Ticket assigned to you.');
    }

    public function updateStatus(Request $request, ItSupportTicket $ticket): RedirectResponse
    {
        $this->assertAssignedToTechnician($request, $ticket);

        $data = $request->validate([
            'status' => ['required', 'in:Assigned,In Progress'],
        ]);

        $ticket->update(['status' => $data['status']]);
        $this->itSupportService->activity($ticket, $request->user(), 'Status changed to ' . $data['status'] . '.');
        $this->notifyEmployee($ticket, 'IT ticket status updated', $data['status']);

        return back()->with('success', 'Ticket status updated.');
    }

    public function resolve(Request $request, ItSupportTicket $ticket): RedirectResponse
    {
        $this->assertAssignedToTechnician($request, $ticket);

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

        return back()->with('success', 'Ticket marked as resolved.');
    }

    public function comment(Request $request, ItSupportTicket $ticket): RedirectResponse
    {
        $this->assertTechnicianCanAccess($request, $ticket);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'message' => $data['message'],
            'type' => 'comment',
            'is_internal' => false,
        ]);

        $this->notifyEmployee($ticket, 'IT support replied to your ticket', $ticket->status);

        return back()->with('success', 'Reply added.');
    }

    private function assertTechnicianCanAccess(Request $request, ItSupportTicket $ticket): void
    {
        $this->itSupportService->requireItEmployee($request->user());

        $canAccess = $ticket->assigned_to_user_id === $request->user()->id
            || ($ticket->assigned_to_user_id === null && in_array($ticket->status, ['Open', 'Reopened'], true));

        abort_unless($canAccess, 403);
    }

    private function assertAssignedToTechnician(Request $request, ItSupportTicket $ticket): void
    {
        $this->itSupportService->requireItEmployee($request->user());
        abort_unless($ticket->assigned_to_user_id === $request->user()->id, 403);
        abort_if(in_array($ticket->status, ['Resolved', 'Closed'], true), 422, 'Resolved or closed tickets cannot be modified here.');
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
