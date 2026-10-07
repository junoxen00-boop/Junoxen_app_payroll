@extends('layouts.admin')

@section('content')

{{-- ==========================================================
     PAGE HEADER
========================================================== --}}

<div
    class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"
>

    <div>

        <h3 class="fw-bold mb-1">
            All Payrolls
        </h3>

        <p class="text-muted mb-0">
            Search and manage employee payroll records.
        </p>

    </div>


    {{-- ======================================================
         TOP RIGHT ACTIONS
    ====================================================== --}}

    <div
        class="payroll-top-actions"
    >

        {{-- ==================================================
             ONE BULK APPROVED & PAID BUTTON
        ================================================== --}}

        <form
            method="POST"
            action="{{ route('admin.payroll.mark-all-paid') }}"
            id="bulkPaidForm"
            class="m-0"
        >

            @csrf


            {{-- Current filters --}}

            <input
                type="hidden"
                name="employee_id"
                value="{{ request('employee_id') }}"
            >

            <input
                type="hidden"
                name="department_id"
                value="{{ request('department_id') }}"
            >

            <input
                type="hidden"
                name="payroll_month"
                value="{{ request('payroll_month') }}"
            >

            <input
                type="hidden"
                name="payroll_year"
                value="{{ request('payroll_year') }}"
            >

            <input
                type="hidden"
                name="status"
                value="{{ request('status') }}"
            >


            <button
                type="submit"
                class="approved-paid-main-btn"
                id="approvedPaidButton"
            >

                Approved & Paid

            </button>

        </form>


        {{-- ==================================================
             GENERATE PAYROLL
        ================================================== --}}

        <a
            href="{{ route('admin.payroll.create') }}"
            class="btn btn-primary generate-payroll-main-btn"
        >

            <i
                class="bi bi-file-earmark-plus me-1"
            ></i>

            Generate Payroll

        </a>

    </div>

</div>



{{-- ==========================================================
     STANDARD SUCCESS MESSAGE
========================================================== --}}

@if(session('success'))

    <div
        class="alert alert-success alert-dismissible fade show"
        role="alert"
    >

        <i
            class="bi bi-check-circle-fill me-2"
        ></i>

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

@endif



{{-- ==========================================================
     BULK WARNING
========================================================== --}}

@if(session('bulk_paid_warning'))

    <div
        class="alert alert-warning alert-dismissible fade show"
        role="alert"
    >

        <i
            class="bi bi-exclamation-triangle-fill me-2"
        ></i>

        {{ session('bulk_paid_warning') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

@endif



{{-- ==========================================================
     ERROR MESSAGE
========================================================== --}}

@if($errors->any())

    <div
        class="alert alert-danger alert-dismissible fade show"
        role="alert"
    >

        <strong>
            Please fix the following:
        </strong>

        <ul class="mb-0 mt-2">

            @foreach(
                $errors->all()
                as $error
            )

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

@endif



{{-- ==========================================================
     FILTERS
========================================================== --}}

<div
    class="card border-0 shadow-sm mb-4 payroll-filter-card"
>

    <div
        class="card-body p-4"
    >

        <form
            method="GET"
            action="{{ route('admin.payroll.index') }}"
        >

            <div
                class="row g-3 align-items-end"
            >


                {{-- ==========================================
                     EMPLOYEE
                ========================================== --}}

                <div
                    class="col-xl-3 col-md-6"
                >

                    <label
                        class="form-label fw-semibold"
                    >

                        Employee

                    </label>


                    <select
                        name="employee_id"
                        class="form-select"
                    >

                        <option value="">

                            All employees

                        </option>


                        @foreach(
                            $employees
                            as $employee
                        )

                            <option
                                value="{{ $employee->id }}"
                                @selected(
                                    request(
                                        'employee_id'
                                    )
                                    == $employee->id
                                )
                            >

                                {{ $employee->full_name }}

                            </option>

                        @endforeach

                    </select>

                </div>



                {{-- ==========================================
                     DEPARTMENT
                ========================================== --}}

                <div
                    class="col-xl-2 col-md-6"
                >

                    <label
                        class="form-label fw-semibold"
                    >

                        Department

                    </label>


                    <select
                        name="department_id"
                        class="form-select"
                    >

                        <option value="">

                            All departments

                        </option>


                        @foreach(
                            $departments
                            as $department
                        )

                            <option
                                value="{{ $department->id }}"
                                @selected(
                                    request(
                                        'department_id'
                                    )
                                    == $department->id
                                )
                            >

                                {{ $department->name }}

                            </option>

                        @endforeach

                    </select>

                </div>



                {{-- ==========================================
                     MONTH
                ========================================== --}}

                <div
                    class="col-xl-2 col-md-4"
                >

                    <label
                        class="form-label fw-semibold"
                    >

                        Month

                    </label>


                    <select
                        name="payroll_month"
                        class="form-select"
                    >

                        <option value="">

                            All months

                        </option>


                        @for(
                            $month = 1;
                            $month <= 12;
                            $month++
                        )

                            <option
                                value="{{ $month }}"
                                @selected(
                                    request(
                                        'payroll_month'
                                    )
                                    == $month
                                )
                            >

                                {{
                                    \Carbon\Carbon::create()
                                        ->month(
                                            $month
                                        )
                                        ->format(
                                            'F'
                                        )
                                }}

                            </option>

                        @endfor

                    </select>

                </div>



                {{-- ==========================================
                     YEAR
                ========================================== --}}

                <div
                    class="col-xl-2 col-md-4"
                >

                    <label
                        class="form-label fw-semibold"
                    >

                        Year

                    </label>


                    <input
                        type="number"
                        name="payroll_year"
                        class="form-control"
                        placeholder="Year"
                        min="2000"
                        max="2100"
                        value="{{ request('payroll_year') }}"
                    >

                </div>



                {{-- ==========================================
                     STATUS
                ========================================== --}}

                <div
                    class="col-xl-2 col-md-4"
                >

                    <label
                        class="form-label fw-semibold"
                    >

                        Status

                    </label>


                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="">

                            All statuses

                        </option>


                        <option
                            value="Draft"
                            @selected(
                                request(
                                    'status'
                                )
                                === 'Draft'
                            )
                        >

                            Draft

                        </option>


                        <option
                            value="Generated"
                            @selected(
                                request(
                                    'status'
                                )
                                === 'Generated'
                            )
                        >

                            Generated

                        </option>


                        <option
                            value="Paid"
                            @selected(
                                request(
                                    'status'
                                )
                                === 'Paid'
                            )
                        >

                            Paid

                        </option>


                        <option
                            value="Cancelled"
                            @selected(
                                request(
                                    'status'
                                )
                                === 'Cancelled'
                            )
                        >

                            Cancelled

                        </option>

                    </select>

                </div>



                {{-- ==========================================
                     GO
                ========================================== --}}

                <div
                    class="col-xl-1 col-md-12"
                >

                    <button
                        type="submit"
                        class="btn btn-primary w-100 filter-go-btn"
                    >

                        Go

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>



{{-- ==========================================================
     TABLE
========================================================== --}}

<div
    class="card border-0 shadow-sm payroll-table-card"
>

    <div
        class="card-body p-0"
    >

        <div
            class="table-responsive"
        >

            <table
                class="table align-middle mb-0 payroll-table"
            >

                <thead
                    class="table-light"
                >

                    <tr>

                        <th
                            class="px-3 py-3"
                        >

                            Payroll #

                        </th>


                        <th
                            class="py-3"
                        >

                            Employee

                        </th>


                        <th
                            class="py-3"
                        >

                            Department

                        </th>


                        <th
                            class="py-3"
                        >

                            Period

                        </th>


                        <th
                            class="py-3"
                        >

                            Gross

                        </th>


                        <th
                            class="py-3"
                        >

                            Deductions

                        </th>


                        <th
                            class="py-3"
                        >

                            Net

                        </th>


                        <th
                            class="py-3"
                        >

                            Status

                        </th>


                        <th
                            class="py-3 text-end pe-3"
                        >

                            Actions

                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse(
                        $payrolls
                        as $payroll
                    )

                        <tr>


                            {{-- =================================
                                 PAYROLL NUMBER
                            ================================= --}}

                            <td
                                class="px-3"
                            >

                                <div
                                    class="fw-semibold"
                                >

                                    {{
                                        $payroll
                                            ->payroll_number
                                    }}

                                </div>

                            </td>



                            {{-- =================================
                                 EMPLOYEE
                            ================================= --}}

                            <td>

                                <div
                                    class="fw-semibold"
                                >

                                    {{
                                        $payroll
                                            ->employee
                                            ->full_name
                                    }}

                                </div>


                                <small
                                    class="text-muted"
                                >

                                    {{
                                        $payroll
                                            ->employee
                                            ->employee_id
                                    }}

                                </small>

                            </td>



                            {{-- =================================
                                 DEPARTMENT
                            ================================= --}}

                            <td>

                                {{
                                    $payroll
                                        ->employee
                                        ->department
                                        ?->name
                                    ?? '-'
                                }}

                            </td>



                            {{-- =================================
                                 PERIOD
                            ================================= --}}

                            <td>

                                {{
                                    \Carbon\Carbon::create()
                                        ->month(
                                            $payroll
                                                ->payroll_month
                                        )
                                        ->format(
                                            'M'
                                        )
                                }}

                                {{
                                    $payroll
                                        ->payroll_year
                                }}

                            </td>



                            {{-- =================================
                                 GROSS
                            ================================= --}}

                            <td>

                                ₹{{
                                    number_format(
                                        (float)
                                        $payroll
                                            ->gross_salary,
                                        2
                                    )
                                }}

                            </td>



                            {{-- =================================
                                 DEDUCTIONS
                            ================================= --}}

                            <td>

                                ₹{{
                                    number_format(
                                        (float)
                                        $payroll
                                            ->total_deductions,
                                        2
                                    )
                                }}

                            </td>



                            {{-- =================================
                                 NET
                            ================================= --}}

                            <td>

                                <strong>

                                    ₹{{
                                        number_format(
                                            (float)
                                            $payroll
                                                ->net_salary,
                                            2
                                        )
                                    }}

                                </strong>

                            </td>



                            {{-- =================================
                                 STATUS
                            ================================= --}}

                            <td>


                                @if(
                                    $payroll
                                        ->status
                                    === 'Paid'
                                )

                                    <span
                                        class="badge rounded-pill payroll-status-paid"
                                    >

                                        Paid

                                    </span>


                                @elseif(
                                    $payroll
                                        ->status
                                    === 'Draft'
                                )

                                    <span
                                        class="badge rounded-pill payroll-status-draft"
                                    >

                                        Draft

                                    </span>


                                @elseif(
                                    $payroll
                                        ->status
                                    === 'Generated'
                                )

                                    <span
                                        class="badge rounded-pill payroll-status-generated"
                                    >

                                        Generated

                                    </span>


                                @elseif(
                                    $payroll
                                        ->status
                                    === 'Cancelled'
                                )

                                    <span
                                        class="badge rounded-pill payroll-status-cancelled"
                                    >

                                        Cancelled

                                    </span>


                                @else

                                    <span
                                        class="badge rounded-pill bg-secondary"
                                    >

                                        {{
                                            $payroll
                                                ->status
                                        }}

                                    </span>

                                @endif

                            </td>



                            {{-- =================================
                                 ACTIONS
                            ================================= --}}

                            <td
                                class="text-end pe-3"
                            >

                                <div
                                    class="d-flex flex-wrap justify-content-end gap-2"
                                >


                                    {{-- =========================
                                         VIEW
                                    ========================= --}}

                                    <a
                                        href="{{
                                            route(
                                                'admin.payroll.show',
                                                $payroll
                                            )
                                        }}"
                                        class="btn btn-outline-primary btn-sm action-btn"
                                    >

                                        View

                                    </a>



                                    {{-- =========================
                                         EDIT
                                    ========================= --}}

                                    @if(
                                        !in_array(
                                            $payroll
                                                ->status,
                                            [
                                                'Paid',
                                                'Cancelled'
                                            ],
                                            true
                                        )
                                    )

                                        <a
                                            href="{{
                                                route(
                                                    'admin.payroll.edit',
                                                    $payroll
                                                )
                                            }}"
                                            class="btn btn-outline-secondary btn-sm action-btn"
                                        >

                                            Edit

                                        </a>

                                    @endif



                                    {{-- =========================
                                         IMPORTANT:
                                         NO APPROVED & PAID
                                         BUTTON HERE.
                                         ONLY ONE BUTTON EXISTS
                                         AT THE TOP.
                                    ========================= --}}



                                    {{-- =========================
                                         DELETE
                                    ========================= --}}

                                    @if(
                                        $payroll
                                            ->status
                                        === 'Draft'
                                    )

                                        <form
                                            method="POST"
                                            action="{{
                                                route(
                                                    'admin.payroll.destroy',
                                                    $payroll
                                                )
                                            }}"
                                            class="m-0"
                                            onsubmit="
                                                return confirm(
                                                    'Are you sure you want to delete this draft payroll?'
                                                );
                                            "
                                        >

                                            @csrf

                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm action-btn"
                                            >

                                                Delete

                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="9"
                                class="text-center py-5 text-muted"
                            >

                                No payroll records found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>



{{-- ==========================================================
     PAGINATION
========================================================== --}}

<div
    class="mt-4"
>

    {{
        $payrolls->links()
    }}

</div>



{{-- ==========================================================
     PAYSLIP SENT POPUP
========================================================== --}}

@if(
    session(
        'bulk_paid_success'
    )
)

    <div
        class="modal fade"
        id="payslipSentModal"
        tabindex="-1"
        aria-labelledby="payslipSentModalLabel"
        aria-hidden="true"
    >

        <div
            class="modal-dialog modal-dialog-centered"
        >

            <div
                class="modal-content payslip-sent-modal"
            >

                <div
                    class="modal-body text-center"
                >


                    {{-- ========================================
                         ENVELOPE CHECK ICON
                    ======================================== --}}

                    <div
                        class="payslip-sent-icon"
                    >

                        <i
                            class="bi bi-envelope-check"
                        ></i>

                    </div>



                    {{-- ========================================
                         TITLE
                    ======================================== --}}

                    <h2
                        class="payslip-sent-title"
                        id="payslipSentModalLabel"
                    >

                        Payslip sent

                    </h2>



                    {{-- ========================================
                         MESSAGE
                    ======================================== --}}

                    <p
                        class="payslip-sent-description"
                    >

                        Payroll has been approved and
                        marked as paid for all pending
                        employees.

                    </p>


                    <div
                        class="payslip-count-box"
                    >

                        <strong>

                            {{
                                session(
                                    'bulk_paid_success'
                                )
                            }}

                        </strong>

                        {{
                            session(
                                'bulk_paid_success'
                            ) == 1
                                ? 'employee processed'
                                : 'employees processed'
                        }}

                    </div>



                    {{-- ========================================
                         OK
                    ======================================== --}}

                    <button
                        type="button"
                        class="payslip-ok-btn"
                        data-bs-dismiss="modal"
                    >

                        OK

                    </button>

                </div>

            </div>

        </div>

    </div>

@endif



{{-- ==========================================================
     CSS
========================================================== --}}

<style>

    /*
    |--------------------------------------------------------------------------
    | Top actions
    |--------------------------------------------------------------------------
    */

    .payroll-top-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 18px;
        margin-left: auto;
    }


    /*
    |--------------------------------------------------------------------------
    | Approved & Paid
    |--------------------------------------------------------------------------
    |
    | ONE black button at top exactly as requested.
    |
    */

    .approved-paid-main-btn {
    min-width: 220px;
    height: 48px;

    padding: 0 30px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    background: #0d6efd;
    color: #ffffff;

    border: 2px solid #0d6efd;
    border-radius: 8px;

    font-size: 14px;
    font-weight: 600;

    cursor: pointer;

    box-shadow: none;

    transition:
        background-color .2s ease,
        border-color .2s ease,
        transform .1s ease;
}


  .approved-paid-main-btn:focus {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #ffffff;

    outline: none;

    box-shadow: 0 0 0 3px rgba(13, 110, 253, .25);
}


    .approved-paid-main-btn:focus {
        background: #000000;
        border-color: #000000;
        color: #ffffff;

        outline: none;

        box-shadow:
            0 0 0 3px
            rgba(
                0,
                0,
                0,
                .12
            );
    }


    .approved-paid-main-btn:active {
        transform:
            translateY(
                1px
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Payroll
    |--------------------------------------------------------------------------
    */

    .generate-payroll-main-btn {
        min-width: 170px;
        height: 48px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        border-radius: 8px;

        font-weight: 600;
    }


    /*
    |--------------------------------------------------------------------------
    | Filter card
    |--------------------------------------------------------------------------
    */

    .payroll-filter-card {
        border-radius: 18px;
    }


    .payroll-filter-card
    .form-control,
    .payroll-filter-card
    .form-select {
        min-height: 46px;

        border-radius: 10px;
    }


    .filter-go-btn {
        min-height: 46px;

        border-radius: 10px;

        font-weight: 600;
    }


    /*
    |--------------------------------------------------------------------------
    | Payroll table
    |--------------------------------------------------------------------------
    */

    .payroll-table-card {
        border-radius: 16px;

        overflow: hidden;
    }


    .payroll-table
    > :not(caption)
    > *
    > * {
        padding-top: 1rem;
        padding-bottom: 1rem;

        vertical-align: middle;
    }


    .payroll-table
    thead th {
        white-space: nowrap;

        font-weight: 700;

        color: #151515;
    }


    .payroll-table
    tbody tr:hover {
        background:
            rgba(
                13,
                110,
                253,
                .025
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    .payroll-status-paid {
        padding:
            7px 14px;

        background:
            #198754;

        color:
            #ffffff;
    }


    .payroll-status-draft {
        padding:
            7px 14px;

        background:
            #0d6efd;

        color:
            #ffffff;
    }


    .payroll-status-generated {
        padding:
            7px 14px;

        background:
            #ffc107;

        color:
            #212529;
    }


    .payroll-status-cancelled {
        padding:
            7px 14px;

        background:
            #dc3545;

        color:
            #ffffff;
    }


    /*
    |--------------------------------------------------------------------------
    | Action Buttons
    |--------------------------------------------------------------------------
    */

    .action-btn {
        min-width: 58px;

        border-radius: 9px;

        font-weight: 500;
    }


    /*
    |--------------------------------------------------------------------------
    | Payslip Sent Modal
    |--------------------------------------------------------------------------
    */

    .payslip-sent-modal {
        border: 0;

        border-radius: 20px;

        overflow: hidden;

        box-shadow:
            0 24px 80px
            rgba(
                0,
                0,
                0,
                .22
            );
    }


    .payslip-sent-modal
    .modal-body {
        padding:
            52px 40px
            42px;
    }


    /*
    |--------------------------------------------------------------------------
    | Sent icon
    |--------------------------------------------------------------------------
    */

    .payslip-sent-icon {
        width: 96px;
        height: 96px;

        margin:
            0 auto 24px;

        display: flex;
        align-items: center;
        justify-content: center;

        border:
            3px solid
            #000000;

        border-radius:
            50%;

        background:
            #ffffff;

        color:
            #000000;

        font-size:
            42px;
    }


    /*
    |--------------------------------------------------------------------------
    | Popup title
    |--------------------------------------------------------------------------
    */

    .payslip-sent-title {
        margin-bottom:
            12px;

        color:
            #111111;

        font-size:
            30px;

        font-weight:
            700;
    }


    /*
    |--------------------------------------------------------------------------
    | Popup text
    |--------------------------------------------------------------------------
    */

    .payslip-sent-description {
        max-width:
            360px;

        margin:
            0 auto 22px;

        color:
            #6c757d;

        font-size:
            15px;

        line-height:
            1.65;
    }


    /*
    |--------------------------------------------------------------------------
    | Count
    |--------------------------------------------------------------------------
    */

    .payslip-count-box {
        margin:
            0 auto 28px;

        padding:
            12px 20px;

        display:
            inline-block;

        background:
            #f5f5f5;

        border:
            1px solid
            #e7e7e7;

        border-radius:
            10px;

        color:
            #222222;
    }


    /*
    |--------------------------------------------------------------------------
    | OK
    |--------------------------------------------------------------------------
    */

    .payslip-ok-btn {
        min-width:
            130px;

        height:
            45px;

        padding:
            0 32px;

        background:
            #000000;

        color:
            #ffffff;

        border:
            2px solid
            #000000;

        border-radius:
            8px;

        font-weight:
            600;
    }


    .payslip-ok-btn:hover {
        background:
            #222222;

        border-color:
            #222222;

        color:
            #ffffff;
    }


    /*
    |--------------------------------------------------------------------------
    | Tablet
    |--------------------------------------------------------------------------
    */

    @media (
        max-width:
        992px
    ) {

        .payroll-top-actions {
            width:
                100%;

            justify-content:
                flex-start;
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Mobile
    |--------------------------------------------------------------------------
    */

    @media (
        max-width:
        768px
    ) {

        .payroll-top-actions {
            flex-direction:
                column;

            align-items:
                stretch;

            width:
                100%;
        }


        .payroll-top-actions
        form {
            width:
                100%;
        }


        .approved-paid-main-btn {
            width:
                100%;
        }


        .generate-payroll-main-btn {
            width:
                100%;
        }


        .payroll-table {
            min-width:
                1050px;
        }


        .payslip-sent-modal
        .modal-body {
            padding:
                40px 20px
                32px;
        }

    }

</style>



{{-- ==========================================================
     OPEN PAYSLIP SENT POPUP AUTOMATICALLY
========================================================== --}}

@if(
    session(
        'bulk_paid_success'
    )
)

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const modalElement =
            document.getElementById(
                'payslipSentModal'
            );

        if (!modalElement) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Bootstrap 5
        |--------------------------------------------------------------------------
        */

        if (
            typeof bootstrap
            !== 'undefined'
            &&
            typeof bootstrap.Modal
            !== 'undefined'
        ) {

            const modal =
                new bootstrap.Modal(
                    modalElement,
                    {
                        backdrop: true,
                        keyboard: true
                    }
                );

            modal.show();

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Bootstrap fallback
        |--------------------------------------------------------------------------
        */

        modalElement.classList.add(
            'show'
        );

        modalElement.style.display =
            'block';

        modalElement.removeAttribute(
            'aria-hidden'
        );

        modalElement.setAttribute(
            'aria-modal',
            'true'
        );

    }
);

</script>

@endif



{{-- ==========================================================
     PREVENT ACCIDENTAL DOUBLE CLICK
========================================================== --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const form =
            document.getElementById(
                'bulkPaidForm'
            );

        const button =
            document.getElementById(
                'approvedPaidButton'
            );

        if (
            !form ||
            !button
        ) {
            return;
        }


        form.addEventListener(
            'submit',
            function () {

                button.disabled =
                    true;

                button.innerText =
                    'Processing...';

                button.style.opacity =
                    '0.7';

            }
        );

    }
);

</script>

@endsection