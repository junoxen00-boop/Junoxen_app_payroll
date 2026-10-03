@php
    $monthName = DateTime::createFromFormat('!m', (int) $payroll->payroll_month)->format('F');
    $joiningDate = $payroll->employee->joining_date?->format('d M Y') ?? '-';
    $payDate = $payroll->payment_date?->format('d M Y') ?? ($payroll->period_end?->format('d M Y') ?? '-');

    $net = (float) $payroll->net_salary;
    $rupees = (int) floor($net);
    $paise = (int) round(($net - $rupees) * 100);
    $amountWords = number_format($net, 2) . ' Rupees';

    if (class_exists(\NumberFormatter::class)) {
        $formatter = new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT);
        $amountWords = ucwords($formatter->format($rupees)) . ' Rupees';
        if ($paise > 0) {
            $amountWords .= ' and ' . ucwords($formatter->format($paise)) . ' Paise';
        }
        $amountWords .= ' Only';
    }
@endphp

<style>
    .jnx-payslip {
        max-width: 900px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .08);
        color: #0f172a;
    }
    .jnx-payslip-header {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        align-items: center;
        padding: 26px 30px;
        border-bottom: 1px solid #e5e7eb;
    }
    .jnx-company { font-size: 25px; font-weight: 800; letter-spacing: .02em; }
    .jnx-subtle { color: #64748b; font-size: 13px; }
    .jnx-payslip-title { text-align: right; }
    .jnx-payslip-title h1 { margin: 0; font-size: 30px; font-weight: 800; }
    .jnx-section { padding: 22px 30px; border-bottom: 1px solid #eef2f7; }
    .jnx-section-title { font-weight: 800; margin-bottom: 14px; font-size: 15px; text-transform: uppercase; letter-spacing: .05em; }
    .jnx-info-grid { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 14px 22px; }
    .jnx-info-label { font-size: 12px; color: #64748b; margin-bottom: 3px; }
    .jnx-info-value { font-weight: 700; }
    .jnx-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 26px; }
    .jnx-table { width: 100%; border-collapse: collapse; }
    .jnx-table td { padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
    .jnx-table td:last-child { text-align: right; font-weight: 600; }
    .jnx-table .jnx-total td { border-top: 2px solid #cbd5e1; border-bottom: 0; padding-top: 11px; font-weight: 800; }
    .jnx-net { padding: 22px 30px; display: flex; align-items: center; justify-content: space-between; background: #f8fafc; }
    .jnx-net-label { font-weight: 800; font-size: 18px; }
    .jnx-net-value { font-weight: 900; font-size: 28px; }
    .jnx-words { padding: 0 30px 20px; background: #f8fafc; color: #475569; font-size: 13px; }
    .jnx-footer { padding: 18px 30px; text-align: center; color: #64748b; font-size: 12px; }

    @media (max-width: 767px) {
        .jnx-payslip-header { align-items: flex-start; flex-direction: column; }
        .jnx-payslip-title { text-align: left; }
        .jnx-info-grid { grid-template-columns: 1fr 1fr; }
        .jnx-two-col { grid-template-columns: 1fr; gap: 12px; }
        .jnx-section, .jnx-payslip-header, .jnx-net, .jnx-footer { padding-left: 18px; padding-right: 18px; }
        .jnx-words { padding-left: 18px; padding-right: 18px; }
    }

    @media print {
        .jnx-payslip {
            max-width: none;
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }
        .jnx-payslip-header, .jnx-section, .jnx-net { break-inside: avoid; page-break-inside: avoid; }
    }
</style>

<div class="jnx-payslip">
    <div class="jnx-payslip-header">
        <div>
            <div class="jnx-company">JUNOXEN PVT LTD</div>
            <div class="jnx-subtle">Employee Payroll Statement</div>
        </div>
        <div class="jnx-payslip-title">
            <h1>Payslip</h1>
            <div class="jnx-subtle">{{ $monthName }} {{ $payroll->payroll_year }}</div>
            <div class="jnx-subtle">{{ $payroll->payroll_number }}</div>
        </div>
    </div>

    <div class="jnx-section">
        <div class="jnx-section-title">Employee Summary</div>
        <div class="jnx-info-grid">
            <div><div class="jnx-info-label">Employee Name</div><div class="jnx-info-value">{{ $payroll->employee->full_name ?? '-' }}</div></div>
            <div><div class="jnx-info-label">Employee ID</div><div class="jnx-info-value">{{ $payroll->employee->employee_id ?? '-' }}</div></div>
            <div><div class="jnx-info-label">Designation</div><div class="jnx-info-value">{{ $payroll->employee->designation ?? '-' }}</div></div>
            <div><div class="jnx-info-label">Department</div><div class="jnx-info-value">{{ $payroll->employee->department?->name ?? '-' }}</div></div>
            <div><div class="jnx-info-label">Date of Joining</div><div class="jnx-info-value">{{ $joiningDate }}</div></div>
            <div><div class="jnx-info-label">Pay Period</div><div class="jnx-info-value">{{ $monthName }} {{ $payroll->payroll_year }}</div></div>
            <div><div class="jnx-info-label">Pay Date</div><div class="jnx-info-value">{{ $payDate }}</div></div>
            <div><div class="jnx-info-label">Paid Days</div><div class="jnx-info-value">{{ number_format((float) ($payroll->paid_days ?? 0), 2) }}</div></div>
            <div><div class="jnx-info-label">LOP Days</div><div class="jnx-info-value">{{ (int) ($payroll->lop_days ?? 0) }}</div></div>
        </div>
    </div>

    <div class="jnx-section">
        <div class="jnx-two-col">
            <div>
                <div class="jnx-section-title">Earnings</div>
                <table class="jnx-table">
                    <tr><td>Basic Salary</td><td>₹{{ number_format((float) $payroll->basic_salary, 2) }}</td></tr>
                    @if((float) $payroll->bonus > 0)
                        <tr><td>Bonus</td><td>₹{{ number_format((float) $payroll->bonus, 2) }}</td></tr>
                    @endif
                    <tr class="jnx-total"><td>Gross Earnings</td><td>₹{{ number_format((float) $payroll->gross_salary, 2) }}</td></tr>
                </table>
            </div>

            <div>
                <div class="jnx-section-title">Deductions</div>
                <table class="jnx-table">
                    <tr><td>Professional Tax</td><td>₹{{ number_format((float) $payroll->professional_tax, 2) }}</td></tr>
      
                    <tr><td>LOP Deduction</td><td>₹{{ number_format((float) ($payroll->lop_deduction ?? 0), 2) }}</td></tr>
                    <tr class="jnx-total"><td>Total Deductions</td><td>₹{{ number_format((float) $payroll->total_deductions, 2) }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="jnx-net">
        <div class="jnx-net-label">TOTAL NET PAYABLE</div>
        <div class="jnx-net-value">₹{{ number_format((float) $payroll->net_salary, 2) }}</div>
    </div>
    <div class="jnx-words"><strong>Amount in words:</strong> {{ $amountWords }}</div>

    <div class="jnx-footer">
        This is a system-generated payslip for JUNOXEN PVT LTD and does not require a physical signature.
    </div>
</div>
