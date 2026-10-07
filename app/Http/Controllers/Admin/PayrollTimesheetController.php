<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeTimesheet;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PayrollTimesheetController extends Controller
{
    public function index(Request $request): View
    {
        $query = EmployeeTimesheet::with('employee.department')->latest('work_date');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->integer('employee_id'));
        }

        return view('admin.payroll-management.timesheets.index', [
            'pageTitle' => 'Timesheets',
            'timesheets' => $query->paginate(20)->withQueryString(),
            'employees' => Employee::orderBy('full_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'work_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'break_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'status' => ['required', 'in:Draft,Submitted,Approved,Rejected'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $start = Carbon::createFromFormat('H:i', $data['start_time']);
        $end = Carbon::createFromFormat('H:i', $data['end_time']);

        if ($end->lessThanOrEqualTo($start)) {
            throw ValidationException::withMessages([
                'end_time' => 'End Time must be later than Start Time.',
            ]);
        }

        $breakMinutes = (int) ($data['break_minutes'] ?? 0);
        $workedMinutes = $start->diffInMinutes($end) - $breakMinutes;

        if ($workedMinutes < 0) {
            throw ValidationException::withMessages([
                'break_minutes' => 'Break Duration cannot exceed the worked time.',
            ]);
        }

        $data['break_minutes'] = $breakMinutes;
        $data['hours_worked'] = number_format($workedMinutes / 60, 2, '.', '');
        $data['created_by'] = $request->user()->id;

        EmployeeTimesheet::updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'work_date' => $data['work_date'],
            ],
            $data
        );

        return back()->with('success', 'Timesheet saved successfully.');
    }

    public function destroy(EmployeeTimesheet $timesheet): RedirectResponse
    {
        $timesheet->delete();
        return back()->with('success', 'Timesheet deleted successfully.');
    }
}
