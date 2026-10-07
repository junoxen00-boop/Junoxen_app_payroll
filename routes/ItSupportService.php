<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\ItSupportTicket;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ItSupportService
{
    public function isAdmin(User $user): bool
    {
        return strcasecmp((string) $user->role?->name, 'Admin') === 0;
    }

    public function isItEmployee(User $user): bool
    {
        $user->loadMissing('employee.department');

        return $user->employee
            && $user->employee->status === 'Active'
            && strcasecmp(trim((string) $user->employee->department?->name), 'IT') === 0;
    }

    public function requireItEmployee(User $user): void
    {
        abort_unless($this->isItEmployee($user), 403, 'IT Support access is limited to active IT department employees.');
    }

    public function itEmployees(): Collection
    {
        return Employee::query()
            ->with(['user', 'department'])
            ->where('status', 'Active')
            ->whereNotNull('user_id')
            ->whereHas('department', function ($query) {
                $query->whereRaw('LOWER(TRIM(name)) = ?', ['it']);
            })
            ->orderBy('full_name')
            ->get();
    }

    public function adminUsers(): Collection
    {
        return User::query()
            ->with('role')
            ->whereHas('role', function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['admin']);
            })
            ->get();
    }

    public function assignTicketNumber(ItSupportTicket $ticket): void
    {
        if ($ticket->ticket_number) {
            return;
        }

        $year = ($ticket->created_at ?? now())->format('Y');
        $number = 'JNX-IT-' . $year . '-' . str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT);

        $ticket->forceFill([
            'ticket_number' => $number,
        ])->saveQuietly();
    }

    public function activity(
        ItSupportTicket $ticket,
        ?User $user,
        string $message,
        bool $internal = false
    ): void {
        $ticket->comments()->create([
            'user_id' => $user?->id,
            'message' => $message,
            'type' => 'activity',
            'is_internal' => $internal,
        ]);
    }

    public function notify(User $user, string $title, string $message, string $url, string $type = 'info'): void
    {
        Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => $url,
            'is_read' => false,
        ]);
    }

    public function notifyAdminsAndItTeam(ItSupportTicket $ticket): void
    {
        $users = collect();

        foreach ($this->adminUsers() as $admin) {
            $users->put($admin->id, $admin);
        }

        foreach ($this->itEmployees() as $employee) {
            if ($employee->user) {
                $users->put($employee->user->id, $employee->user);
            }
        }

        foreach ($users as $user) {
            $isAdmin = $this->isAdmin($user);
            $url = $isAdmin
                ? route('admin.it-support.show', $ticket)
                : route('it-support.show', $ticket);

            $this->notify(
                $user,
                $ticket->priority === 'Critical' ? 'Critical IT support ticket' : 'New IT support ticket',
                $ticket->ticket_number . ': ' . $ticket->subject,
                $url,
                $ticket->priority === 'Critical' ? 'danger' : 'info'
            );
        }
    }

    public function createTicket(array $data, Employee $employee, User $creator): ItSupportTicket
    {
        return DB::transaction(function () use ($data, $employee, $creator) {
            $ticket = ItSupportTicket::create([
                'employee_id' => $employee->id,
                'department_id' => $employee->department_id,
                'subject' => $data['subject'],
                'category' => $data['category'],
                'description' => $data['description'],
                'priority' => $data['priority'],
                'status' => 'Open',
            ]);

            $this->assignTicketNumber($ticket);
            $this->activity($ticket, $creator, 'Ticket created with status Open.');

            return $ticket->fresh();
        });
    }
}
