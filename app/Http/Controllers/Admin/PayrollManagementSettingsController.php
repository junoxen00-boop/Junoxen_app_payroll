<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollManagementSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollManagementSettingsController extends Controller
{
    public function index(): View
    {
        $settings = PayrollManagementSetting::firstOrCreate([], [
            'company_name' => 'Junoxen PVT LTD',
            'pay_frequency' => 'Monthly',
            'currency' => 'INR',
            'default_payment_method' => 'Bank Transfer',
            'lop_calculation_basis' => 'Calendar Days',
            'payroll_year' => now()->year,
            'attendance_payroll_enabled' => false,
            'grace_minutes' => 10,
            'grace_deduction_mode' => 'excess',
            'standard_work_minutes_per_day' => 480,
            'deduct_late_arrival' => true,
            'late_deduction_rounding' => 'Exact Minutes',
        ]);

        return view('admin.payroll-management.settings.index', [
            'pageTitle' => 'Payroll Settings',
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'pay_frequency' => ['required', 'in:Weekly,Fortnightly,Monthly'],
            'currency' => ['required', 'string', 'max:10'],
            'default_payment_method' => ['required', 'in:Bank Transfer,Cash,Cheque,Other'],
            'lop_calculation_basis' => ['required', 'in:Calendar Days'],
            'payroll_year' => ['nullable', 'integer', 'between:2000,2100'],
            'payslip_footer' => ['nullable', 'string', 'max:3000'],
            'attendance_payroll_enabled' => ['nullable', 'boolean'],
            'default_shift_start' => ['nullable', 'date_format:H:i'],
            'default_shift_end' => ['nullable', 'date_format:H:i', 'after:default_shift_start'],
            'grace_minutes' => ['required', 'integer', 'between:0,240'],
            'grace_deduction_mode' => ['required', 'in:excess,full'],
            'standard_work_minutes_per_day' => ['required', 'integer', 'between:60,1440'],
            'deduct_late_arrival' => ['nullable', 'boolean'],
            'late_deduction_rounding' => ['required', 'in:Exact Minutes,Nearest 15 Minutes,Nearest 30 Minutes,Whole Hour'],
        ]);

        $data['attendance_payroll_enabled'] = $request->boolean('attendance_payroll_enabled');
        $data['deduct_late_arrival'] = $request->boolean('deduct_late_arrival');

        $settings = PayrollManagementSetting::firstOrCreate([]);
        $settings->update($data);

        return back()->with('success', 'Payroll settings updated successfully.');
    }
}