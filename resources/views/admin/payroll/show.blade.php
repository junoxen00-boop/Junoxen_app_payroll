@extends('layouts.admin')

@section('content')

<style>

    @media print {

        @page {
            size: A4 portrait;
            margin: 8mm;
        }


        header,
        nav,
        .navbar,
        .topbar,
        .app-header,
        .main-header,
        .admin-header,
        .sidebar,
        .admin-sidebar,
        .sidebar-overlay,
        .offcanvas,
        .no-print,
        button,
        .btn {
            display: none !important;
        }


        html,
        body {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            font-size: 11px !important;
        }


        main,
        .main-content,
        .content,
        .content-wrapper,
        .page-content,
        .container,
        .container-fluid {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }


        .row {
            display: block !important;
            margin: 0 !important;
        }


        .col-lg-8,
        .col-lg-4,
        .col-md-6 {
            width: 100% !important;
            max-width: 100% !important;
            flex: none !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }


        .card {
            width: 100% !important;
            margin: 0 0 10px 0 !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background: #ffffff !important;

            page-break-inside: auto !important;
            break-inside: auto !important;
        }


        .card-body {
            padding: 8px 0 !important;
        }


        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }


        tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }


        td,
        th {
            padding: 3px 4px !important;
            font-size: 10px !important;
        }


        h2 {
            font-size: 20px !important;
        }


        h3 {
            font-size: 17px !important;
            margin-bottom: 2px !important;
        }


        h5 {
            font-size: 13px !important;
            margin-bottom: 5px !important;
        }


        h6 {
            font-size: 11px !important;
            margin-bottom: 4px !important;
        }


        p {
            margin-bottom: 2px !important;
        }


        .mb-4 {
            margin-bottom: 6px !important;
        }


        .mt-2 {
            margin-top: 3px !important;
        }


        .g-4 {
            --bs-gutter-x: 0 !important;
            --bs-gutter-y: 0 !important;
        }


        .p-4 {
            padding: 8px !important;
        }


        .junoxen-payroll-logo {
            width: 145px !important;
            max-width: 145px !important;
            height: auto !important;
        }


        .payroll-print-header {
            margin-bottom: 8px !important;
            padding-bottom: 8px !important;
        }


        .employee-info-section {
            margin-bottom: 6px !important;
        }


        .payroll-financial-column {
            margin-bottom: 8px !important;
        }


        .net-salary-box {
            margin-top: 6px !important;
            padding: 8px 10px !important;
        }


        .net-salary-box .fs-3 {
            font-size: 18px !important;
        }


        .net-salary-box .fs-5 {
            font-size: 13px !important;
        }


        .print-side-section {
            margin-top: 8px !important;
            padding-top: 6px !important;
            border-top: 1px solid #dddddd !important;
        }


        .payment-print-card,
        .audit-print-card,
        .net-salary-box {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

    }

</style>


@php

    $logoPath = resource_path(
        'views/admin/payroll/Junoxen_Busines_Card_3.jpg'
    );

    $logoData = file_exists($logoPath)
        ? 'data:image/jpeg;base64,' .
            base64_encode(
                file_get_contents($logoPath)
            )
        : null;


    $monthName = DateTime::createFromFormat(
        '!m',
        $payroll->payroll_month
    )->format('F');

@endphp


{{-- PAYSLIP HEADER --}}
<div
    class="payroll-print-header"
    style="
        width:100%;
        margin-bottom:20px;
        border-bottom:2px solid #0b58a4;
        padding-bottom:15px;
    "
>

    <table
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
    >

        <tr>

            <td
                style="
                    width:55%;
                    vertical-align:middle;
                "
            >

                @if($logoData)

                    <img
                        src="{{ $logoData }}"
                        alt="Junoxen Logo"
                        class="junoxen-payroll-logo"
                        style="
                            width:220px;
                            max-width:100%;
                            height:auto;
                            display:block;
                            object-fit:contain;
                        "
                    >

                @else

                    <div
                        style="
                            font-size:28px;
                            font-weight:800;
                            color:#0b58a4;
                        "
                    >
                        JUNOXEN PVT LTD
                    </div>

                @endif

            </td>


            <td
                style="
                    width:45%;
                    text-align:right;
                    vertical-align:middle;
                "
            >

                <h2
                    style="
                        margin:0;
                        font-size:24px;
                        color:#0b58a4;
                    "
                >
                    Payslip
                </h2>


                <p
                    style="
                        margin:5px 0 0;
                        font-size:14px;
                        color:#555;
                    "
                >

                    {{ $monthName }}

                    {{ $payroll->payroll_year }}

                </p>

            </td>

        </tr>

    </table>

</div>


{{-- PAYROLL NUMBER --}}
<div
    class="
        d-flex
        flex-wrap
        justify-content-between
        align-items-center
        gap-3
        mb-4
    "
>

    <div>

        <h3 class="fw-bold mb-1">

            {{ $payroll->payroll_number }}

        </h3>


        <p class="text-muted mb-0">

            {{ $payroll->employee->full_name }}

            ·

            {{ $monthName }}

            {{ $payroll->payroll_year }}

        </p>

    </div>


    {{-- SCREEN ACTIONS --}}
    <div class="d-flex gap-2 no-print">

        @if(
            !in_array(
                $payroll->status,
                ['Paid', 'Cancelled']
            )
        )

            <a
                href="{{
                    route(
                        'admin.payroll.edit',
                        $payroll
                    )
                }}"
                class="btn btn-outline-primary"
            >
                Edit
            </a>


            <form
                method="POST"
                action="{{
                    route(
                        'admin.payroll.cancel',
                        $payroll
                    )
                }}"
                onsubmit="return confirm('Cancel this payroll?');"
            >

                @csrf

                <button
                    type="submit"
                    class="btn btn-outline-danger"
                >
                    Cancel Payroll
                </button>

            </form>

        @endif


        <button
            type="button"
            onclick="window.print()"
            class="btn btn-outline-secondary"
        >

            <i class="bi bi-printer"></i>

            Print

        </button>

    </div>

</div>


{{-- PAYROLL CONTENT --}}
<div class="row g-4">


    {{-- LEFT COLUMN --}}
    <div class="col-lg-8">

        <div class="card">

            <div class="card-body p-4">


                {{-- EMPLOYEE INFORMATION --}}
                <div
                    class="
                        row
                        mb-4
                        employee-info-section
                    "
                >

                    <div class="col-md-6">

                        <h5 class="fw-bold">
                            Employee Information
                        </h5>


                        <div>

                            {{ $payroll->employee->employee_id }}

                            ·

                            {{ $payroll->employee->full_name }}

                        </div>


                        <div class="text-muted">

                            {{ $payroll->employee->designation }}

                            ·

                            {{ $payroll->employee->department?->name }}

                        </div>

                    </div>


                    <div class="col-md-6 text-md-end">

                        <span
                            class="
                                badge
                                fs-6

                                {{
                                    $payroll->status === 'Paid'
                                        ? 'bg-success'
                                        : (
                                            $payroll->status === 'Cancelled'
                                                ? 'bg-danger'
                                                : 'bg-primary'
                                        )
                                }}
                            "
                        >

                            {{ $payroll->status }}

                        </span>


                        <div class="small text-muted mt-2">

                            Generated

                            {{
                                $payroll
                                    ->generated_at
                                    ?->format(
                                        'd M Y H:i'
                                    )
                            }}

                        </div>

                    </div>

                </div>


                {{-- PAYROLL FINANCIAL SUMMARY --}}
                <div class="row g-4">
                    <div class="col-md-6 payroll-financial-column">
                        <h6 class="fw-bold">Earnings</h6>
                        <table class="table table-sm">
                            <tr><td>Basic Salary</td><td class="text-end">₹{{ number_format((float) $payroll->basic_salary, 2) }}</td></tr>
                            <tr><td>Bonus</td><td class="text-end">₹{{ number_format((float) $payroll->bonus, 2) }}</td></tr>
                            <tr class="fw-bold"><td>Gross Earnings</td><td class="text-end">₹{{ number_format((float) $payroll->gross_salary, 2) }}</td></tr>
                        </table>

                        <h6 class="fw-bold mt-4">Attendance / Leave</h6>
                        <table class="table table-sm">
                            <tr><td>Leave Days</td><td class="text-end">{{ number_format((float) ($payroll->leave_days ?? 0), 2) }}</td></tr>
                            <tr><td>LOP Days</td><td class="text-end">{{ number_format((float) ($payroll->lop_days ?? 0), 2) }}</td></tr>
                            <tr><td>Paid Days</td><td class="text-end">{{ number_format((float) ($payroll->paid_days ?? 0), 2) }}</td></tr>
                        </table>
                    </div>

                    <div class="col-md-6 payroll-financial-column">
                        <h6 class="fw-bold">Deductions</h6>
                        <table class="table table-sm">
                            <tr><td>Professional Tax</td><td class="text-end">₹{{ number_format((float) $payroll->professional_tax, 2) }}</td></tr>
                            <tr><td>LOP Deduction</td><td class="text-end">₹{{ number_format((float) ($payroll->lop_deduction ?? 0), 2) }}</td></tr>
                            <tr class="fw-bold"><td>Total Deductions</td><td class="text-end">₹{{ number_format((float) $payroll->total_deductions, 2) }}</td></tr>
                        </table>
                    </div>
                </div>

                {{-- TOTAL NET PAYABLE --}}
                <div
                    class="
                        p-4
                        rounded-4
                        bg-light
                        d-flex
                        justify-content-between
                        align-items-center
                        net-salary-box
                    "
                >

                    <span class="fw-bold fs-5">

                        TOTAL NET PAYABLE

                    </span>


                    <span
                        class="
                            fw-bold
                            fs-3
                            text-success
                        "
                    >

                        ₹{{
                            number_format(
                                (float)
                                $payroll->net_salary,
                                2
                            )
                        }}

                    </span>

                </div>

            </div>

        </div>

    </div>


    {{-- RIGHT COLUMN --}}
    <div
        class="
            col-lg-4
            print-side-section
        "
    >


        {{-- PAYMENT --}}
        <div
            class="
                card
                mb-4
                payment-print-card
            "
        >

            <div class="card-body p-4">

                <h5 class="fw-bold">
                    Payment
                </h5>


                @if($payroll->status === 'Paid')


                    <p class="mb-1">

                        <strong>
                            Date:
                        </strong>

                        {{
                            $payroll
                                ->payment_date
                                ?->format(
                                    'd M Y'
                                )
                        }}

                    </p>


                    <p class="mb-1">

                        <strong>
                            Method:
                        </strong>

                        {{ $payroll->payment_method }}

                    </p>


                    <p class="mb-0">

                        <strong>
                            Reference:
                        </strong>

                        {{
                            $payroll->payment_reference
                                ?: '—'
                        }}

                    </p>


                @elseif(
                    $payroll->status !== 'Cancelled'
                )


                    <form
                        method="POST"
                        class="no-print"
                        action="{{
                            route(
                                'admin.payroll.mark-paid',
                                $payroll
                            )
                        }}"
                    >

                        @csrf


                        <div class="mb-2">

                            <label class="form-label">
                                Payment Date
                            </label>

                            <input
                                type="date"
                                name="payment_date"
                                class="form-control"
                                value="{{
                                    now()->format(
                                        'Y-m-d'
                                    )
                                }}"
                                required
                            >

                        </div>


                        <div class="mb-2">

                            <label class="form-label">
                                Method
                            </label>


                            <select
                                name="payment_method"
                                class="form-select"
                                required
                            >

                                @foreach([
                                    'Bank Transfer',
                                    'Cash',
                                    'Cheque',
                                    'Other'
                                ] as $m)

                                    <option>
                                        {{ $m }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="mb-2">

                            <label class="form-label">
                                Reference
                            </label>

                            <input
                                name="payment_reference"
                                class="form-control"
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Notes
                            </label>

                            <textarea
                                name="payment_notes"
                                class="form-control"
                                rows="2"
                            ></textarea>

                        </div>


                        <button
                            type="submit"
                            class="
                                btn
                                btn-success
                                w-100
                            "
                            onclick="
                                return confirm(
                                    'Mark this payroll as paid? This locks payroll editing.'
                                );
                            "
                        >

                            Mark Paid

                        </button>

                    </form>


                @else


                    <p class="text-muted mb-0">

                        Cancelled payroll cannot be paid.

                    </p>


                @endif

            </div>

        </div>


        {{-- AUDIT HISTORY --}}
        <div
            class="
                card
                audit-print-card
            "
        >

            <div class="card-body p-4">

                <h5 class="fw-bold">
                    Audit History
                </h5>


                @forelse(
                    $payroll
                        ->auditLogs
                        ->sortByDesc(
                            'created_at'
                        )
                    as $log
                )

                    <div
                        class="
                            border-bottom
                            py-2
                        "
                    >

                        <div class="fw-semibold">

                            {{
                                str_replace(
                                    '_',
                                    ' ',
                                    ucfirst(
                                        $log->action
                                    )
                                )
                            }}

                        </div>


                        <div class="small text-muted">

                            {{
                                $log->user?->name
                                ?? 'System'
                            }}

                            ·

                            {{
                                $log
                                    ->created_at
                                    ?->format(
                                        'd M Y H:i'
                                    )
                            }}

                        </div>

                    </div>


                @empty

                    <p class="text-muted">

                        No audit entries.

                    </p>

                @endforelse

            </div>

        </div>

    </div>

</div>

@endsection