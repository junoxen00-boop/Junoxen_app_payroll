<?php

use App\Models\Payroll;
use App\Models\User;
use App\Services\PayrollService;
use App\Services\PayrollStatutoryService;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->admin = User::where('email', 'admin@junoxen.com')->firstOrFail();
    $this->employee = User::where('email', 'employee@junoxen.com')->firstOrFail()->employee;
    $this->service = app(PayrollService::class);
    $this->statutory = app(PayrollStatutoryService::class);
});

test('telangana professional tax slabs are calculated server side', function () {
    expect($this->statutory->professionalTaxCents(14000 * 100))->toBe(0)
        ->and($this->statutory->professionalTaxCents(18000 * 100))->toBe(15000)
        ->and($this->statutory->professionalTaxCents(25000 * 100))->toBe(20000);
});

test('pf ceiling history applies 15000 before and 25000 after 17 september 2026', function () {
    $salary = $this->employee->salaryStructures()->firstOrFail();

    expect($this->statutory->providentFundCents($salary, Carbon::parse('2026-08-31')))->toBe(180000)
        ->and($this->statutory->providentFundCents($salary, Carbon::parse('2026-09-30')))->toBe(300000);
});

test('payroll calculation uses bonus statutory deductions and server totals', function () {
    $payroll = $this->service->generate($this->employee, 9, 2026, [
        'bonus' => 1000,
        'leave_days' => 0,
        'lop_days' => 0,
        // Browser-supplied statutory values are intentionally ignored.
        'pf_deduction' => 1,
        'professional_tax' => 1,
    ], $this->admin, '127.0.0.1');

    expect($payroll->gross_salary)->toBe('49500.00')
        ->and($payroll->pf_deduction)->toBe('3000.00')
        ->and($payroll->professional_tax)->toBe('200.00')
        ->and($payroll->total_deductions)->toBe('3200.00')
        ->and($payroll->net_salary)->toBe('46300.00')
        ->and($payroll->paid_days)->toBe('30.00');
});

test('lop is calculated once from calendar day proration', function () {
    $payroll = $this->service->generate($this->employee, 9, 2026, [
        'leave_days' => 2,
        'lop_days' => 2,
    ], $this->admin, '127.0.0.1');

    expect($payroll->lop_deduction)->toBe('2000.00')
        ->and($payroll->leave_deduction)->toBe('0.00')
        ->and($payroll->paid_days)->toBe('28.00')
        ->and($payroll->total_deductions)->toBe('5200.00')
        ->and($payroll->net_salary)->toBe('43300.00');
});

test('lop days cannot exceed leave days', function () {
    expect(fn () => $this->service->generate($this->employee, 10, 2026, [
        'leave_days' => 1,
        'lop_days' => 2,
    ], $this->admin, '127.0.0.1'))->toThrow(ValidationException::class);
});

test('duplicate employee month year payroll is rejected', function () {
    $this->service->generate($this->employee, 8, 2026, [], $this->admin, '127.0.0.1');

    expect(fn () => $this->service->generate($this->employee, 8, 2026, [], $this->admin, '127.0.0.1'))
        ->toThrow(ValidationException::class);
});

test('admin can mark payroll paid', function () {
    $payroll = $this->service->generate($this->employee, 7, 2026, [], $this->admin, '127.0.0.1');

    $response = $this->actingAs($this->admin)->post(route('admin.payroll.mark-paid', $payroll), [
        'payment_date' => '2026-07-31',
        'payment_method' => 'Bank Transfer',
        'payment_reference' => 'TXN-TEST-001',
    ]);

    $response->assertSessionHasNoErrors();
    expect($payroll->fresh()->status)->toBe('Paid')
        ->and($payroll->fresh()->payments()->count())->toBe(1);
});

test('database prevents duplicate payroll period', function () {
    $this->service->generate($this->employee, 6, 2026, [], $this->admin, '127.0.0.1');
    $existing = Payroll::firstWhere([
        'employee_id' => $this->employee->id,
        'payroll_month' => 6,
        'payroll_year' => 2026,
    ]);

    expect($existing)->not->toBeNull();
});
