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
        ]);

        $settings = PayrollManagementSetting::firstOrCreate([]);
        $settings->update($data);

        return back()->with('success', 'Payroll settings updated successfully.');
    }
}
