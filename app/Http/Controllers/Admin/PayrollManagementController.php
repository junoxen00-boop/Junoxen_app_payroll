<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\View\View;

class PayrollManagementController extends Controller
{
    public function index(): View
    {
        $month = now()->month;
        $year = now()->year;

        $current = Payroll::query()
            ->where('payroll_month', $month)
            ->where('payroll_year', $year);

        return view('admin.payroll-management.index', [
            'pageTitle' => 'Payroll Overview',
            'month' => $month,
            'year' => $year,
            'totalEmployees' => Employee::count(),
            'activeEmployees' => Employee::where('status', 'Active')->count(),
            'payrollsThisMonth' => (clone $current)->count(),
            'draftPayrolls' => (clone $current)->where('status', 'Draft')->count(),
            'generatedPayrolls' => (clone $current)->where('status', 'Generated')->count(),
            'paidPayrolls' => (clone $current)->where('status', 'Paid')->count(),
            'grossPayroll' => (clone $current)->sum('gross_salary'),
            'totalDeductions' => (clone $current)->sum('total_deductions'),
            'totalNetPayable' => (clone $current)->sum('net_salary'),
            'recentPayrolls' => Payroll::with('employee.department')->latest('id')->take(8)->get(),
            'pendingPayrolls' => Payroll::with('employee.department')
                ->whereIn('status', ['Draft', 'Generated'])
                ->latest('id')
                ->take(8)
                ->get(),
        ]);
    }
}
