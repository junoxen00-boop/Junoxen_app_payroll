<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(Request $request): View
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 403, 'No employee profile is linked to this account.');
        $payrolls = Payroll::where('employee_id', $employee->id)->latest('payroll_year')->latest('payroll_month')->paginate(12);
        return view('employee.payroll.index', ['payrolls' => $payrolls, 'pageTitle' => 'My Payroll']);
    }

    public function show(Request $request, Payroll $payroll): View
    {
        $this->authorizeOwnership($request, $payroll);
        $payroll->load('employee.department', 'payments');
        return view('employee.payroll.show', ['payroll' => $payroll, 'pageTitle' => 'Payslip '.$payroll->payroll_number]);
    }

    public function print(Request $request, Payroll $payroll): View
    {
        $this->authorizeOwnership($request, $payroll);
        $payroll->load('employee.department');
        return view('payroll.payslip', ['payroll' => $payroll]);
    }

    private function authorizeOwnership(Request $request, Payroll $payroll): void
    {
        abort_unless($request->user()->employee?->id === $payroll->employee_id, 403, 'You may only view your own payroll.');
    }
}
