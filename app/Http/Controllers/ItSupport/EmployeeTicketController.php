<?php

namespace App\Http\Controllers\ItSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItSupportTicketRequest;
use App\Models\ItSupportTicket;
use App\Services\ItSupportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeTicketController extends Controller
{
    public function __construct(
        private readonly ItSupportService $itSupportService
    ) {}

    public function index(Request $request): View
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 403, 'No employee profile is linked to this account.');

        $query = ItSupportTicket::query()
            ->with(['department', 'assignedTo'])
            ->where('employee_id', $employee->id)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        return view('employee.it-support.index', [
            'pageTitle' => 'IT Support - My Tickets',
            'tickets' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->employee, 403, 'No employee profile is linked to this account.');

        return view('employee.it-support.create', [
            'pageTitle' => 'Raise IT Support Ticket',
        ]);
    }

    public function store(StoreItSupportTicketRequest $request): RedirectResponse
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 403, 'No employee profile is linked to this account.');

        $ticket = $this->itSupportService->createTicket(
            $request->validated(),
            $employee,
            $request->user()
        );

        $this->itSupportService->notifyAdminsAndItTeam($ticket);

        return redirect()
            ->route('employee.it-support.show', $ticket)
            ->with('success', 'IT support ticket created successfully.');
    }

    public function show(Request $request, ItSupportTicket $ticket): View
    {
        $employee = $request->user()->employee;
        abort_unless($employee && $ticket->employee_id === $employee->id, 403);

        $ticket->load([
            'department',
            'assignedTo.employee',
            'resolvedBy',
            'comments' => fn ($query) => $query->where('is_internal', false)->with('user'),
        ]);

        return view('employee.it-support.show', [
            'pageTitle' => $ticket->ticket_number,
            'ticket' => $ticket,
        ]);
    }

    public function comment(Request $request, ItSupportTicket $ticket): RedirectResponse
    {
        $employee = $request->user()->employee;
        abort_unless($employee && $ticket->employee_id === $employee->id, 403);
        abort_if($ticket->status === 'Closed', 422, 'Closed tickets cannot receive new comments.');

        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'message' => $data['message'],
            'type' => 'comment',
            'is_internal' => false,
        ]);

        if ($ticket->assignedTo) {
            $this->itSupportService->notify(
                $ticket->assignedTo,
                'IT ticket updated by employee',
                $ticket->ticket_number . ': ' . $ticket->subject,
                route('it-support.show', $ticket),
                'info'
            );
        }

        return back()->with('success', 'Comment added.');
    }
}
