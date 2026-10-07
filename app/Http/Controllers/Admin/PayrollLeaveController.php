<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollLeaveController extends Controller
{
    public function index(Request $request): View
    {
        $query = EmployeeLeave::with('employee.department')->latest('start_date');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->integer('employee_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return view('admin.payroll-management.leave.index', [
            'pageTitle' => 'Leave Management',
            'leaves' => $query->paginate(20)->withQueryString(),
            'employees' => Employee::orderBy('full_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'leave_type' => ['required', 'string', 'max:80'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'leave_days' => ['required', 'numeric', 'min:0.5', 'max:31', 'multiple_of:0.5'],
            'is_paid' => ['required', 'boolean'],
            'status' => ['required', 'in:Pending,Approved,Rejected'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $data['created_by'] = $request->user()->id;
        EmployeeLeave::create($data);

        return back()->with('success', 'Leave record added successfully.');
    }

    public function destroy(EmployeeLeave $leave): RedirectResponse
    {
        $leave->delete();
        return back()->with('success', 'Leave record deleted successfully.');
    }
}
