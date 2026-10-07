<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\StpBatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollStpController extends Controller
{
    public function index(): View
    {
        return view('admin.payroll-management.stp.index', [
            'pageTitle' => 'Single Touch Payroll',
            'batches' => StpBatch::latest('payroll_year')->latest('payroll_month')->paginate(20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'payroll_month' => ['required', 'integer', 'between:1,12'],
            'payroll_year' => ['required', 'integer', 'between:2000,2100'],
            'status' => ['required', 'in:Draft,Ready'],
        ]);

        $payrolls = Payroll::query()
            ->where('payroll_month', $data['payroll_month'])
            ->where('payroll_year', $data['payroll_year']);

        StpBatch::updateOrCreate(
            [
                'payroll_month' => $data['payroll_month'],
                'payroll_year' => $data['payroll_year'],
            ],
            [
                'employee_count' => (clone $payrolls)->count(),
                'gross_payments' => (clone $payrolls)->sum('gross_salary'),
                'tax_amount' => (clone $payrolls)->sum('professional_tax'),
                'status' => $data['status'],
                'submitted_at' => null,
                'created_by' => $request->user()->id,
            ]
        );

        return back()->with('success', 'STP preparation batch saved. No external ATO submission was performed.');
    }
}
