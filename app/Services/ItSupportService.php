<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\ItSupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItSupportService
{
    /**
     * Generate the next unique IT support ticket number.
     *
     * Example:
     * JNX-IT-2026-0001
     */
    public function nextTicketNumber(): string
    {
        $year = now()->year;

        $prefix = "JNX-IT-{$year}-";

        $lastTicket = ItSupportTicket::query()
            ->where('ticket_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $nextNumber = 1;

        if ($lastTicket) {
            $lastSequence = (int) substr(
                $lastTicket->ticket_number,
                -4
            );

            $nextNumber = $lastSequence + 1;
        }

        return $prefix . str_pad(
            (string) $nextNumber,
            4,
            '0',
            STR_PAD_LEFT
        );
    }


    /**
     * Create a new support ticket.
     */
    public function createTicket(
        Employee $employee,
        array $data
    ): ItSupportTicket {
        return DB::transaction(function () use (
            $employee,
            $data
        ) {
            $ticketNumber = $this->nextTicketNumber();

            return ItSupportTicket::create([
                'ticket_number' => $ticketNumber,

                'employee_id' => $employee->id,

                'department_id' =>
                    $employee->department_id,

                'subject' =>
                    $data['subject'],

                'category' =>
                    $data['category'],

                'description' =>
                    $data['description'],

                'priority' =>
                    $data['priority'] ?? 'Medium',

                'status' => 'Open',

                'assigned_to' => null,

                'resolution' => null,

                'resolved_by' => null,

                'resolved_at' => null,
            ]);
        });
    }


    /**
     * Assign a ticket to an IT employee.
     */
    public function assignTicket(
        ItSupportTicket $ticket,
        Employee $employee
    ): ItSupportTicket {
        if (!$this->isItEmployee($employee)) {
            throw ValidationException::withMessages([
                'assigned_to' =>
                    'The selected employee is not a member of the IT department.',
            ]);
        }

        $ticket->update([
            'assigned_to' => $employee->id,

            'status' =>
                $ticket->status === 'Open'
                    ? 'Assigned'
                    : $ticket->status,
        ]);

        return $ticket->fresh();
    }


    /**
     * Assign an available ticket to the
     * currently authenticated IT employee.
     */
    public function assignToSelf(
        ItSupportTicket $ticket,
        Employee $employee
    ): ItSupportTicket {
        if (!$this->isItEmployee($employee)) {
            abort(403, 'Only IT employees can accept IT support tickets.');
        }

        if ($ticket->assigned_to !== null) {
            throw ValidationException::withMessages([
                'ticket' =>
                    'This ticket has already been assigned.',
            ]);
        }

        $ticket->update([
            'assigned_to' => $employee->id,
            'status' => 'Assigned',
        ]);

        return $ticket->fresh();
    }


    /**
     * Update ticket status.
     */
    public function updateStatus(
        ItSupportTicket $ticket,
        string $status
    ): ItSupportTicket {
        $allowedStatuses = [
            'Open',
            'Assigned',
            'In Progress',
            'Resolved',
            'Closed',
            'Reopened',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw ValidationException::withMessages([
                'status' =>
                    'The selected ticket status is invalid.',
            ]);
        }

        $ticket->status = $status;

        if ($status !== 'Resolved') {
            $ticket->resolved_at = null;
            $ticket->resolved_by = null;
        }

        $ticket->save();

        return $ticket->fresh();
    }


    /**
     * Resolve a ticket.
     */
    public function resolveTicket(
        ItSupportTicket $ticket,
        User $user,
        string $resolution
    ): ItSupportTicket {
        if (trim($resolution) === '') {
            throw ValidationException::withMessages([
                'resolution' =>
                    'Resolution notes are required.',
            ]);
        }

        $ticket->update([
            'resolution' => $resolution,
            'status' => 'Resolved',
            'resolved_by' => $user->id,
            'resolved_at' => now(),
        ]);

        return $ticket->fresh();
    }


    /**
     * Reopen an already resolved or closed ticket.
     */
    public function reopenTicket(
        ItSupportTicket $ticket
    ): ItSupportTicket {
        if (!in_array(
            $ticket->status,
            ['Resolved', 'Closed'],
            true
        )) {
            throw ValidationException::withMessages([
                'ticket' =>
                    'Only resolved or closed tickets can be reopened.',
            ]);
        }

        $ticket->update([
            'status' => 'Reopened',
            'resolved_at' => null,
            'resolved_by' => null,
        ]);

        return $ticket->fresh();
    }


    /**
     * Check whether an employee belongs
     * to the IT department.
     */
    public function isItEmployee(
        ?Employee $employee
    ): bool {
        if (!$employee) {
            return false;
        }

        $employee->loadMissing('department');

        $departmentName =
            $employee->department?->name;

        if (!$departmentName) {
            return false;
        }

        return strtolower(
            trim($departmentName)
        ) === 'it';
    }


    /**
     * Return all IT department employees.
     */
    public function itEmployees()
    {
        return Employee::query()
            ->whereHas(
                'department',
                function ($query) {
                    $query->whereRaw(
                        'LOWER(name) = ?',
                        ['it']
                    );
                }
            )
            ->orderBy('full_name')
            ->get();
    }
}