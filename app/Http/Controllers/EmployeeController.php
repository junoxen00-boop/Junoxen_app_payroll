<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\TaskAssignment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    private function adminOnly()
    {
        if (auth()->user()->role->name !== 'Admin') {
            abort(403, 'Only Admin can perform this action.');
        }
    }
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $joined = trim((string) $request->query('joined', ''));

        $employees = Employee::query()
            ->with('department')
            ->when($joined === 'today', function ($query) {
                $query->whereDate('joining_date', Carbon::today());
            })
            ->when($search !== '', function ($query) use ($search) {
                $dateSearch = $this->resolveDateSearch($search);

                $query->where(function ($subQuery) use ($search, $dateSearch) {
                    $subQuery
                        ->where('employee_id', 'like', '%' . $search . '%')
                        ->orWhere('full_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('mobile_number', 'like', '%' . $search . '%')
                        ->orWhere('designation', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%')
                        ->orWhere('joining_date', 'like', '%' . $search . '%')
                        ->orWhereHas('department', function ($departmentQuery) use ($search) {
                            $departmentQuery->where('name', 'like', '%' . $search . '%');
                        });

                    if ($dateSearch !== null) {
                        $subQuery->orWhereDate('joining_date', $dateSearch);
                    }
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('employees.index', compact('employees', 'search'));
    }

    public function create(): View
    {
        $this->adminOnly();

        $departments = Department::query()
            ->orderBy('name')
            ->get();

        return view('employees.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
{
    $this->adminOnly();

    $validated = $this->validateEmployee($request);

    $temporaryPassword = str()->password(10);

    DB::transaction(function () use ($validated, $temporaryPassword) {

        $employeeRole = Role::where('name', 'Employee')->firstOrFail();

        $user = User::create([
    'name' => $validated['full_name'],
    'email' => $validated['email'],
    'password' => Hash::make($temporaryPassword),
    'must_change_password' => true,
    'role_id' => $employeeRole->id,
]);

        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_id' => null,
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'mobile_number' => $validated['mobile_number'],
            'department_id' => $validated['department_id'],
            'designation' => $validated['designation'],
            'joining_date' => $validated['joining_date'],
            'status' => $validated['status'],
        ]);

        $employee->employee_id = $this->generateEmployeeId($employee->id);
        $employee->save();
    });

   return Redirect::route('employees.index')
    ->with('success', 'Employee created successfully.')
    ->with('temporary_password', $temporaryPassword)
    ->with('employee_email', $validated['email']);
}

    public function show(Employee $employee): View
{
    $employee->load('department');

    $today = now();

    $assignmentQuery = TaskAssignment::with('task')
        ->where('employee_id', $employee->id);

    $taskStats = [

        'total_tasks' => (clone $assignmentQuery)->count(),

        'pending_tasks' => (clone $assignmentQuery)
            ->where('status', 'Pending')
            ->count(),

        'in_progress_tasks' => (clone $assignmentQuery)
            ->where('status', 'In Progress')
            ->count(),

        'completed_tasks' => (clone $assignmentQuery)
            ->where('status', 'Completed')
            ->count(),

        'overdue_tasks' => (clone $assignmentQuery)
            ->whereHas('task', function ($q) use ($today) {

                $q->whereDate('due_date', '<', $today);

            })
            ->where('status', '!=', 'Completed')
            ->count(),

    ];

    $progressChart = [

        'labels' => [
            'Pending',
            'In Progress',
            'Completed',
            'Overdue',
        ],

        'data' => [

            $taskStats['pending_tasks'],
            $taskStats['in_progress_tasks'],
            $taskStats['completed_tasks'],
            $taskStats['overdue_tasks'],

        ],

    ];

    $employeeTasks = TaskAssignment::with('task')
        ->where('employee_id', $employee->id)
        ->latest()
        ->paginate(10);

    return view('employees.show', compact(
        'employee',
        'taskStats',
        'progressChart',
        'employeeTasks'
    ));
}

    public function edit(Employee $employee): View
    {
        $this->adminOnly();

        $departments = Department::query()
            ->orderBy('name')
            ->get();

        return view('employees.edit', compact('employee', 'departments'));
    }

   public function update(Request $request, Employee $employee): RedirectResponse
{
    $this->adminOnly();

    $validated = $this->validateEmployee($request, $employee);

    DB::transaction(function () use ($validated, $employee) {

        if ($employee->user) {

            $employee->user->update([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
            ]);

        }

        $employee->update([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'mobile_number' => $validated['mobile_number'],
            'department_id' => $validated['department_id'],
            'designation' => $validated['designation'],
            'joining_date' => $validated['joining_date'],
            'status' => $validated['status'],
        ]);

    });

    return Redirect::route('employees.index')
        ->with('success', 'Employee updated successfully.');
}

   public function destroy(Employee $employee): RedirectResponse
{
    $this->adminOnly();

    DB::transaction(function () use ($employee) {

        if ($employee->user) {
            $employee->user->delete();
        }

        $employee->delete();

    });

    return Redirect::route('employees.index')
        ->with('success', 'Employee deleted successfully.');
}

    private function validateEmployee(Request $request, ?Employee $employee = null): array
    {
        $request->merge([
            'full_name' => trim((string) $request->input('full_name')),
            'email' => strtolower(trim((string) $request->input('email'))),
            'mobile_number' => trim((string) $request->input('mobile_number')),
            'designation' => trim((string) $request->input('designation')),
        ]);

        return $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:150',
            ],
            'email' => [
    'required',
    'email',
    'max:150',

    Rule::unique('employees', 'email')
        ->ignore($employee?->id),

    Rule::unique('users', 'email')
        ->ignore($employee?->user_id, 'id'),
],
            'mobile_number' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s()]+$/',
            ],
            'department_id' => [
                'required',
                'integer',
                Rule::exists('departments', 'id'),
            ],
            'designation' => [
                'required',
                'string',
                'max:150',
            ],
            'joining_date' => [
                'required',
                'date',
            ],
            'status' => [
                'required',
                Rule::in(['Active', 'Inactive']),
            ],
        ], [
            'full_name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'This email address is already used by another employee.',
            'mobile_number.required' => 'Mobile number is required.',
            'mobile_number.regex' => 'Mobile number can only contain numbers, spaces, +, -, and brackets.',
            'department_id.required' => 'Please select a department.',
            'department_id.exists' => 'Selected department does not exist.',
            'designation.required' => 'Designation is required.',
            'joining_date.required' => 'Joining date is required.',
            'status.required' => 'Please select employee status.',
        ]);
    }

    private function generateEmployeeId(int $id): string
    {
        return 'EMP-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);
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