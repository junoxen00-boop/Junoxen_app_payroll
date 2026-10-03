<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
   public function index(Request $request): View
{
    $search = trim((string) $request->get('q'));

    $department = $request->department;

    $employee = $request->employee;

    $priority = $request->priority;

    $status = $request->status;

    $tasks = Task::with([
            'creator',
            'employees.department',
            'assignments.employee'
        ])

        ->when($search, function ($query) use ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('task_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");

            });

        })

        ->when($department, function ($query) use ($department) {

            $query->whereHas('employees', function ($q) use ($department) {

                $q->where('department_id', $department);

            });

        })

        ->when($employee, function ($query) use ($employee) {

            $query->whereHas('employees', function ($q) use ($employee) {

                $q->where('employees.id', $employee);

            });

        })

        ->when($priority, function ($query) use ($priority) {

            $query->where('priority', $priority);

        })

        ->when($status, function ($query) use ($status) {

            $query->whereHas('assignments', function ($q) use ($status) {

                $q->where('status', $status);

            });

        })

        ->latest()
        ->paginate(10)
        ->withQueryString();

    $departments = Department::orderBy('name')->get();

    $employees = Employee::where('status', 'Active')
                    ->orderBy('full_name')
                    ->get();

    return view('tasks.index', compact(
        'tasks',
        'search',
        'departments',
        'employees',
        'department',
        'employee',
        'priority',
        'status'
    ));
}

    public function create(): View
    {
        $employees = Employee::with('department')
            ->where('status', 'Active')
            ->orderBy('full_name')
            ->get();

        return view('tasks.create', compact('employees'));
    }

        public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTask($request);

        DB::transaction(function () use ($validated) {

            $task = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'],
                'start_date' => $validated['start_date'],
                'due_date' => $validated['due_date'],
                'created_by' => auth()->id(),
            ]);

           foreach ($validated['employees'] as $employeeId) {

    TaskAssignment::create([
        'task_id' => $task->id,
        'employee_id' => $employeeId,
        'status' => 'Pending',
        'progress' => 0,
        'remarks' => null,
    ]);

    $employee = Employee::with('user')->find($employeeId);

    if ($employee && $employee->user) {

        Notification::create([
            'user_id' => $employee->user->id,
            'title' => 'New Task Assigned',
            'message' => 'A new task "' . $task->title . '" has been assigned to you.',
            'type' => 'info',
            'url' => route('employee.tasks.index'),
        ]);

$setting = Setting::first();

if ($setting && $setting->email_notifications) {

    Mail::send(
        'emails.task-assigned',
        [
            'employee' => $employee,
            'task' => $task,
        ],
        function ($message) use ($employee, $setting) {

            $message->to($employee->email)
                    ->subject('New Task Assigned');

            if ($setting->sender_email) {
                $message->from(
                    $setting->sender_email,
                    $setting->sender_name ?: config('app.name')
                );
            }

            if ($setting->reply_to_email) {
                $message->replyTo($setting->reply_to_email);
            }
        }
    );
}

    }

}

        });

        return Redirect::route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task): View
    {
        $task->load([
            'creator',
            'employees.department',
            'assignments.employee.department',
        ]);

        return view('tasks.show', compact('task'));
    }

        public function edit(Task $task): View
    {
        $task->load('employees');

        $employees = Employee::with('department')
            ->where('status', 'Active')
            ->orderBy('full_name')
            ->get();

        $selectedEmployees = $task->employees->pluck('id')->toArray();

        return view('tasks.edit', compact(
            'task',
            'employees',
            'selectedEmployees'
        ));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $this->validateTask($request);

        DB::transaction(function () use ($validated, $task) {

            $task->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'],
                'start_date' => $validated['start_date'],
                'due_date' => $validated['due_date'],
            ]);

            $task->assignments()->delete();

            foreach ($validated['employees'] as $employeeId) {

                TaskAssignment::create([
                    'task_id' => $task->id,
                    'employee_id' => $employeeId,
                    'status' => 'Pending',
                    'progress' => 0,
                    'remarks' => null,
                ]);

            }

        });

        return Redirect::route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        DB::transaction(function () use ($task) {

            $task->assignments()->delete();

            $task->delete();

        });

        return Redirect::route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

        private function validateTask(Request $request): array
    {
        $request->merge([
            'title' => trim((string) $request->input('title')),
            'description' => $request->filled('description')
                ? trim((string) $request->input('description'))
                : null,
        ]);

        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:150',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'employees' => [
                'required',
                'array',
                'min:1',
            ],

            'employees.*' => [
                'integer',
                Rule::exists('employees', 'id'),
            ],

            'priority' => [
                'required',
                Rule::in(['Low', 'Medium', 'High', 'Critical']),
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'due_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],

        ], [
            'title.required' => 'Task title is required.',
            'employees.required' => 'Please select at least one employee.',
            'employees.*.exists' => 'One of the selected employees does not exist.',
            'priority.required' => 'Please select task priority.',
            'start_date.required' => 'Start date is required.',
            'due_date.required' => 'Due date is required.',
            'due_date.after_or_equal' => 'Due date must be the same as or after the start date.',
        ]);
    }

    private function resolveDateSearch(string $search): ?string
    {
        $formats = [
            'Y-m-d',
            'd-m-Y',
            'd/m/Y',
            'm/d/Y',
            'd M Y',
            'd F Y',
            'M d Y',
            'F d Y',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $search);

                if ($date && $date->format($format) === $search) {
                    return $date->toDateString();
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return null;
    }
}