<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollEmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));

        $employees = Employee::with(['department', 'payrollProfile'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('employee_id', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%")
                        ->orWhereHas('department', fn ($d) => $d->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('full_name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.payroll-management.employees.index', [
            'pageTitle' => 'Payroll Employees',
            'employees' => $employees,
            'search' => $search,
        ]);
    }

    public function show(Employee $employee): View
    {
        $employee->load([
            'department',
            'payrollProfile',
            'superannuation',
            'payrolls' => fn ($q) => $q->latest('payroll_year')->latest('payroll_month')->latest('id'),
        ]);

        return view('admin.payroll-management.employees.show', [
            'pageTitle' => 'Employee Payroll Card',
            'employee' => $employee,
        ]);
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate([
            'employment_type' => ['nullable', 'string', 'max:80'],
            'payroll_status' => ['required', 'in:Active,Inactive,On Hold'],
            'pay_frequency' => ['required', 'in:Weekly,Fortnightly,Monthly'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'default_basic_salary' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'default_bonus' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'payroll_notes' => ['nullable', 'string', 'max:5000'],
            'account_holder_name' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'routing_identifier' => ['nullable', 'string', 'max:255'],
            'bank_branch' => ['nullable', 'string', 'max:255'],
        ]);

        $data['default_basic_salary'] = $data['default_basic_salary'] ?? 0;
        $data['default_bonus'] = $data['default_bonus'] ?? 0;

        $employee->payrollProfile()->updateOrCreate(
            ['employee_id' => $employee->id],
            $data
        );

        return back()->with('success', 'Employee payroll card updated successfully.');
    }
}
