@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="row g-4">

        {{-- PAYROLL MANAGEMENT NAVIGATION --}}
        <div class="col-xl-3 col-lg-4">

            @include(
                'admin.payroll-management.partials.nav'
            )

        </div>


        {{-- MAIN CONTENT --}}
        <div class="col-xl-9 col-lg-8">


            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))

                <div class="alert alert-success">
                    {{ session('success') }}
                </div>

            @endif


            {{-- VALIDATION ERRORS --}}
            @if($errors->any())

                <div class="alert alert-danger">

                    <ul class="mb-0">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- PAGE INFORMATION --}}
            <div class="alert alert-info">

                <strong>
                    Superannuation Administration
                </strong>

                <br>

                These records are maintained for payroll
                administration only.

                No external superannuation fund payment,
                submission or provider integration is
                performed from this page.

            </div>


            {{-- =====================================================
                 ADD / UPDATE SUPERANNUATION
            ====================================================== --}}

            <div class="card mb-4">

                <div class="card-body">

                    <h4 class="fw-bold mb-3">
                        Add / Update Superannuation
                    </h4>


                    <form
                        method="POST"
                        action="{{
                            route(
                                'admin.payroll-management.superannuation.store'
                            )
                        }}"
                        class="row g-3"
                    >

                        @csrf


                        {{-- EMPLOYEE --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Employee *
                            </label>

                            <select
                                name="employee_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Select employee
                                </option>


                                @foreach($employees as $employee)

                                    <option
                                        value="{{ $employee->id }}"
                                        @selected(
                                            old('employee_id')
                                            == $employee->id
                                        )
                                    >

                                        {{
                                            $employee->employee_id
                                        }}

                                        -

                                        {{
                                            $employee->full_name
                                        }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- FUND NAME --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Fund Name
                            </label>

                            <input
                                type="text"
                                name="fund_name"
                                class="form-control"
                                value="{{
                                    old('fund_name')
                                }}"
                                maxlength="255"
                            >

                        </div>


                        {{-- MEMBER NUMBER --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Member Number
                            </label>

                            <input
                                type="text"
                                name="member_number"
                                class="form-control"
                                value="{{
                                    old('member_number')
                                }}"
                                maxlength="255"
                            >

                        </div>


                        {{-- USI --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                USI / Fund Identifier
                            </label>

                            <input
                                type="text"
                                name="usi"
                                class="form-control"
                                value="{{
                                    old('usi')
                                }}"
                                maxlength="255"
                            >

                        </div>


                        {{-- EMPLOYEE CONTRIBUTION --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Employee Contribution
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₹
                                </span>

                                <input
                                    type="number"
                                    name="employee_contribution"
                                    min="0"
                                    step="0.01"
                                    class="form-control"
                                    value="{{
                                        old(
                                            'employee_contribution',
                                            0
                                        )
                                    }}"
                                >

                            </div>

                        </div>


                        {{-- EMPLOYER CONTRIBUTION --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Employer Contribution
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₹
                                </span>

                                <input
                                    type="number"
                                    name="employer_contribution"
                                    min="0"
                                    step="0.01"
                                    class="form-control"
                                    value="{{
                                        old(
                                            'employer_contribution',
                                            0
                                        )
                                    }}"
                                >

                            </div>

                        </div>


                        {{-- STATUS --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Status *
                            </label>

                            <select
                                name="status"
                                class="form-select"
                                required
                            >

                                <option
                                    value="Active"
                                    @selected(
                                        old(
                                            'status',
                                            'Active'
                                        )
                                        === 'Active'
                                    )
                                >
                                    Active
                                </option>

                                <option
                                    value="Inactive"
                                    @selected(
                                        old('status')
                                        === 'Inactive'
                                    )
                                >
                                    Inactive
                                </option>

                            </select>

                        </div>


                        {{-- SUBMIT --}}
                        <div class="col-12">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Save Superannuation
                            </button>

                        </div>

                    </form>

                </div>

            </div>



            {{-- =====================================================
                 SUPERANNUATION RECORDS
            ====================================================== --}}

            <div class="card">

                <div class="card-header bg-white">

                    <h5 class="fw-bold mb-0">
                        Employee Superannuation Records
                    </h5>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table
                            class="
                                table
                                table-hover
                                align-middle
                                mb-0
                            "
                        >

                            <thead>

                                <tr>

                                    <th>
                                        Employee
                                    </th>

                                    <th>
                                        Fund
                                    </th>

                                    <th>
                                        Member Number
                                    </th>

                                    <th>
                                        USI
                                    </th>

                                    <th class="text-end">
                                        Employee Contribution
                                    </th>

                                    <th class="text-end">
                                        Employer Contribution
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse(
                                    $employees
                                    as $employee
                                )

                                    @php
                                        $superannuation =
                                            $employee
                                                ->superannuation;
                                    @endphp


                                    <tr>

                                        {{-- EMPLOYEE --}}
                                        <td>

                                            <div class="fw-semibold">

                                                {{
                                                    $employee
                                                        ->full_name
                                                }}

                                            </div>

                                            <div
                                                class="
                                                    small
                                                    text-muted
                                                "
                                            >

                                                {{
                                                    $employee
                                                        ->employee_id
                                                }}

                                            </div>

                                        </td>


                                        {{-- FUND --}}
                                        <td>

                                            {{
                                                $superannuation
                                                    ?->fund_name
                                                ?? '—'
                                            }}

                                        </td>


                                        {{-- MEMBER --}}
                                        <td>

                                            {{
                                                $superannuation
                                                    ?->member_number
                                                ?? '—'
                                            }}

                                        </td>


                                        {{-- USI --}}
                                        <td>

                                            {{
                                                $superannuation
                                                    ?->usi
                                                ?? '—'
                                            }}

                                        </td>


                                        {{-- EMPLOYEE CONTRIBUTION --}}
                                        <td class="text-end">

                                            ₹{{
                                                number_format(
                                                    (float) (
                                                        $superannuation
                                                            ?->employee_contribution
                                                        ?? 0
                                                    ),
                                                    2
                                                )
                                            }}

                                        </td>


                                        {{-- EMPLOYER CONTRIBUTION --}}
                                        <td class="text-end">

                                            ₹{{
                                                number_format(
                                                    (float) (
                                                        $superannuation
                                                            ?->employer_contribution
                                                        ?? 0
                                                    ),
                                                    2
                                                )
                                            }}

                                        </td>


                                        {{-- STATUS --}}
                                        <td>

                                            @if(
                                                !$superannuation
                                            )

                                                <span
                                                    class="
                                                        badge
                                                        bg-secondary
                                                    "
                                                >
                                                    Not configured
                                                </span>

                                            @elseif(
                                                $superannuation
                                                    ->status
                                                === 'Active'
                                            )

                                                <span
                                                    class="
                                                        badge
                                                        bg-success
                                                    "
                                                >
                                                    Active
                                                </span>

                                            @else

                                                <span
                                                    class="
                                                        badge
                                                        bg-secondary
                                                    "
                                                >
                                                    Inactive
                                                </span>

                                            @endif

                                        </td>

                                    </tr>


                                @empty

                                    <tr>

                                        <td
                                            colspan="7"
                                            class="
                                                text-center
                                                text-muted
                                                py-5
                                            "
                                        >
                                            No employees found.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection