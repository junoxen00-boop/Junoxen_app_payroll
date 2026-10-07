<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollRequest;
use App\Http\Requests\UpdatePayrollRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(
        private PayrollService $payrollService
    ) {
    }

    /**
     * Payroll dashboard.
     */
    public function dashboard(): View
    {
        $month = now()->month;
        $year = now()->year;

        $current = Payroll::where(
            'payroll_month',
            $month
        )->where(
            'payroll_year',
            $year
        );

        return view(
            'admin.payroll.dashboard',
            [
                'pageTitle' => 'Payroll Dashboard',

                'totalEmployees' =>
                    Employee::where(
                        'status',
                        'Active'
                    )->count(),

                'generatedThisMonth' =>
                    (clone $current)->count(),

                'grossTotal' =>
                    (clone $current)->sum(
                        'gross_salary'
                    ),

                'deductionsTotal' =>
                    (clone $current)->sum(
                        'total_deductions'
                    ),

                'netTotal' =>
                    (clone $current)->sum(
                        'net_salary'
                    ),

                'paidCount' =>
                    (clone $current)
                        ->where(
                            'status',
                            'Paid'
                        )
                        ->count(),

                'pendingCount' =>
                    (clone $current)
                        ->whereIn(
                            'status',
                            [
                                'Draft',
                                'Generated',
                            ]
                        )
                        ->count(),

                'recentPayrolls' =>
                    Payroll::with(
                        'employee.department'
                    )
                        ->latest()
                        ->take(8)
                        ->get(),
            ]
        );
    }

    /**
     * Payroll listing.
     */
    public function index(
        Request $request
    ): View {
        $query = Payroll::with(
            'employee.department'
        )
            ->latest('payroll_year')
            ->latest('payroll_month')
            ->latest('id');

        foreach (
            [
                'employee_id',
                'payroll_month',
                'payroll_year',
                'status',
            ] as $field
        ) {
            if ($request->filled($field)) {
                $query->where(
                    $field,
                    $request->input($field)
                );
            }
        }

        if (
            $request->filled(
                'department_id'
            )
        ) {
            $query->whereHas(
                'employee',
                fn ($q) => $q->where(
                    'department_id',
                    $request->integer(
                        'department_id'
                    )
                )
            );
        }

        return view(
            'admin.payroll.index',
            [
                'payrolls' =>
                    $query
                        ->paginate(20)
                        ->withQueryString(),

                'employees' =>
                    Employee::orderBy(
                        'full_name'
                    )->get(),

                'departments' =>
                    Department::orderBy(
                        'name'
                    )->get(),

                'pageTitle' =>
                    'All Payrolls',
            ]
        );
    }

    /**
     * Payroll generation page.
     */
    public function create(): View
    {
        return view(
            'admin.payroll.create',
            [
                'employees' =>
                    Employee::where(
                        'status',
                        'Active'
                    )
                        ->orderBy(
                            'full_name'
                        )
                        ->get(),

                'pageTitle' =>
                    'Generate Payroll',
            ]
        );
    }

    /**
     * Generate payroll.
     */
    public function store(
        StorePayrollRequest $request
    ): RedirectResponse {
        $employee =
            Employee::findOrFail(
                $request->integer(
                    'employee_id'
                )
            );

        $payroll =
            $this->payrollService
                ->generate(
                    $employee,
                    $request->integer(
                        'payroll_month'
                    ),
                    $request->integer(
                        'payroll_year'
                    ),
                    $request->validated(),
                    $request->user(),
                    $request->ip()
                );

        return redirect()
            ->route(
                'admin.payroll.show',
                $payroll
            )
            ->with(
                'success',
                'Payroll generated successfully.'
            );
    }

    /**
     * Display payroll.
     */
    public function show(
        Payroll $payroll
    ): View {
        $payroll->load(
            'employee.department',
            'payments.recordedBy',
            'auditLogs.user'
        );

        return view(
            'admin.payroll.show',
            [
                'payroll' =>
                    $payroll,

                'pageTitle' =>
                    'Payroll ' .
                    $payroll
                        ->payroll_number,
            ]
        );
    }

    /**
     * Edit payroll.
     */
    public function edit(
        Payroll $payroll
    ): View {
        abort_if(
            in_array(
                $payroll->status,
                [
                    'Paid',
                    'Cancelled',
                ],
                true
            ),
            403,
            'Paid or cancelled payroll cannot be edited.'
        );

        return view(
            'admin.payroll.edit',
            [
                'payroll' =>
                    $payroll->load(
                        'employee'
                    ),

                'pageTitle' =>
                    'Edit Payroll',
            ]
        );
    }

    /**
     * Update payroll.
     */
    public function update(
        UpdatePayrollRequest $request,
        Payroll $payroll
    ): RedirectResponse {
        $this->payrollService
            ->update(
                $payroll,
                $request->validated(),
                $request->user(),
                $request->ip()
            );

        return redirect()
            ->route(
                'admin.payroll.show',
                $payroll
            )
            ->with(
                'success',
                'Payroll updated successfully.'
            );
    }

    /**
     * Delete draft payroll.
     */
    public function destroy(
        Request $request,
        Payroll $payroll
    ): RedirectResponse {
        abort_unless(
            $payroll->status ===
                'Draft',
            422,
            'Only draft payroll can be deleted.'
        );

        DB::transaction(
            function () use (
                $payroll,
                $request
            ) {
                $this->payrollService
                    ->audit(
                        $payroll,
                        $request->user(),
                        'payroll_deleted',
                        $payroll
                            ->toArray(),
                        null,
                        $request->ip()
                    );

                $payroll->delete();
            }
        );

        return redirect()
            ->route(
                'admin.payroll.index'
            )
            ->with(
                'success',
                'Draft payroll deleted successfully.'
            );
    }

    /**
     * Approve and mark all pending payrolls
     * as paid in one action.
     */
    public function markAllPaid(
        Request $request
    ): RedirectResponse {
        $month =
            $request->filled(
                'payroll_month'
            )
                ? $request->integer(
                    'payroll_month'
                )
                : now()->month;

        $year =
            $request->filled(
                'payroll_year'
            )
                ? $request->integer(
                    'payroll_year'
                )
                : now()->year;

        $query =
            Payroll::query()
                ->whereIn(
                    'status',
                    [
                        'Draft',
                        'Generated',
                    ]
                )
                ->where(
                    'payroll_month',
                    $month
                )
                ->where(
                    'payroll_year',
                    $year
                );

        /**
         * Apply employee filter when selected.
         */
        if (
            $request->filled(
                'employee_id'
            )
        ) {
            $query->where(
                'employee_id',
                $request->integer(
                    'employee_id'
                )
            );
        }

        /**
         * Apply department filter when selected.
         */
        if (
            $request->filled(
                'department_id'
            )
        ) {
            $query->whereHas(
                'employee',
                fn ($q) => $q->where(
                    'department_id',
                    $request->integer(
                        'department_id'
                    )
                )
            );
        }

        $payrollIds =
            $query
                ->orderBy('id')
                ->pluck('id');

        if (
            $payrollIds->isEmpty()
        ) {
            return redirect()
                ->route(
                    'admin.payroll.index',
                    $request->only(
                        [
                            'employee_id',
                            'department_id',
                            'payroll_month',
                            'payroll_year',
                            'status',
                        ]
                    )
                )
                ->with(
                    'bulk_paid_warning',
                    'No Draft or Generated payrolls were found for the selected payroll period.'
                );
        }

        $processed = 0;

        foreach (
            $payrollIds
            as $payrollId
        ) {
            $payroll =
                Payroll::find(
                    $payrollId
                );

            /**
             * Another request may have changed
             * the status after the initial query.
             */
            if (
                !$payroll ||
                !in_array(
                    $payroll->status,
                    [
                        'Draft',
                        'Generated',
                    ],
                    true
                )
            ) {
                continue;
            }

            $this->payrollService
                ->markPaid(
                    $payroll,
                    [
                        'payment_date' =>
                            now()
                                ->toDateString(),

                        'payment_method' =>
                            'Other',

                        'payment_reference' =>
                            null,

                        'payment_notes' =>
                            'Bulk Approved & Paid from All Payrolls.',
                    ],
                    $request->user(),
                    $request->ip()
                );

            $processed++;
        }

        if ($processed === 0) {
            return redirect()
                ->route(
                    'admin.payroll.index',
                    $request->only(
                        [
                            'employee_id',
                            'department_id',
                            'payroll_month',
                            'payroll_year',
                            'status',
                        ]
                    )
                )
                ->with(
                    'bulk_paid_warning',
                    'No pending payrolls were available to process.'
                );
        }

        return redirect()
            ->route(
                'admin.payroll.index',
                [
                    'employee_id' =>
                        $request->input(
                            'employee_id'
                        ),

                    'department_id' =>
                        $request->input(
                            'department_id'
                        ),

                    'payroll_month' =>
                        $month,

                    'payroll_year' =>
                        $year,

                    'status' =>
                        $request->input(
                            'status'
                        ),
                ]
            )
            ->with(
                'bulk_paid_success',
                $processed
            );
    }

    /**
     * Mark one payroll as paid.
     */
    public function markPaid(
        Request $request,
        Payroll $payroll
    ): RedirectResponse {
        $data =
            $request->validate(
                [
                    'payment_date' => [
                        'required',
                        'date',
                    ],

                    'payment_method' => [
                        'required',
                        Rule::in(
                            [
                                'Bank Transfer',
                                'Cash',
                                'Cheque',
                                'Other',
                            ]
                        ),
                    ],

                    'payment_reference' => [
                        'nullable',
                        'string',
                        'max:120',
                    ],

                    'payment_notes' => [
                        'nullable',
                        'string',
                        'max:2000',
                    ],
                ]
            );

        $this->payrollService
            ->markPaid(
                $payroll,
                $data,
                $request->user(),
                $request->ip()
            );

        return back()
            ->with(
                'success',
                'Payroll marked as paid successfully.'
            );
    }

    /**
     * Cancel payroll.
     */
    public function cancel(
        Request $request,
        Payroll $payroll
    ): RedirectResponse {
        abort_if(
            $payroll->status ===
                'Paid',
            422,
            'Paid payroll cannot be cancelled.'
        );

        DB::transaction(
            function () use (
                $payroll,
                $request
            ) {
                $old =
                    $payroll
                        ->toArray();

                $payroll->update(
                    [
                        'status' =>
                            'Cancelled',
                    ]
                );

                $this->payrollService
                    ->audit(
                        $payroll,
                        $request->user(),
                        'payroll_cancelled',
                        $old,
                        $payroll
                            ->fresh()
                            ->toArray(),
                        $request->ip()
                    );
            }
        );

        return back()
            ->with(
                'success',
                'Payroll cancelled successfully.'
            );
    }

    /**
     * Export payroll report.
     */
    public function export(
        Request $request
    ) {
        $query =
            Payroll::with(
                'employee.department'
            );

        foreach (
            [
                'payroll_month',
                'payroll_year',
                'status',
                'employee_id',
            ] as $field
        ) {
            if (
                $request->filled(
                    $field
                )
            ) {
                $query->where(
                    $field,
                    $request->input(
                        $field
                    )
                );
            }
        }

        if (
            $request->filled(
                'department_id'
            )
        ) {
            $query->whereHas(
                'employee',
                fn ($q) => $q->where(
                    'department_id',
                    $request->integer(
                        'department_id'
                    )
                )
            );
        }

        $filename =
            'junoxen-payroll-report-' .
            now()->format(
                'Ymd-His'
            ) .
            '.csv';

        return response()
            ->streamDownload(
                function () use (
                    $query
                ) {
                    $out =
                        fopen(
                            'php://output',
                            'w'
                        );

                    fputcsv(
                        $out,
                        [
                            'Payroll Number',
                            'Employee ID',
                            'Employee',
                            'Department',
                            'Month',
                            'Year',
                            'Basic Salary',
                            'Bonus',
                            'Provident Fund',
                            'Professional Tax',
                            'Leave Days',
                            'LOP Days',
                            'LOP Deduction',
                            'Paid Days',
                            'Total Deductions',
                            'Total Net Payable',
                            'Status',
                            'Payment Date',
                            'Payment Method',
                            'Payment Reference',
                        ]
                    );

                    $query
                        ->orderBy(
                            'payroll_year'
                        )
                        ->orderBy(
                            'payroll_month'
                        )
                        ->chunk(
                            200,
                            function (
                                $payrolls
                            ) use ($out) {
                                foreach (
                                    $payrolls
                                    as $p
                                ) {
                                    fputcsv(
                                        $out,
                                        [
                                            $p->payroll_number,

                                            $p->employee
                                                ->employee_id,

                                            $p->employee
                                                ->full_name,

                                            $p->employee
                                                ->department
                                                ?->name,

                                            $p->payroll_month,

                                            $p->payroll_year,

                                            $p->basic_salary,

                                            $p->bonus,

                                            $p->pf_deduction,

                                            $p->professional_tax,

                                            $p->leave_days
                                                ?? 0,

                                            $p->lop_days
                                                ?? 0,

                                            $p->lop_deduction
                                                ?? 0,

                                            $p->paid_days
                                                ?? 0,

                                            $p->total_deductions,

                                            $p->net_salary,

                                            $p->status,

                                            $p->payment_date
                                                ?->format(
                                                    'Y-m-d'
                                                ),

                                            $p->payment_method,

                                            $p->payment_reference,
                                        ]
                                    );
                                }
                            }
                        );

                    fclose($out);
                },
                $filename,
                [
                    'Content-Type' =>
                        'text/csv',
                ]
            );
    }

    /**
     * Payroll reports.
     */
    public function reports(
        Request $request
    ): View {
        $query =
            Payroll::with(
                'employee.department'
            );

        foreach (
            [
                'payroll_month',
                'payroll_year',
                'status',
                'employee_id',
            ] as $field
        ) {
            if (
                $request->filled(
                    $field
                )
            ) {
                $query->where(
                    $field,
                    $request->input(
                        $field
                    )
                );
            }
        }

        if (
            $request->filled(
                'department_id'
            )
        ) {
            $query->whereHas(
                'employee',
                fn ($q) => $q->where(
                    'department_id',
                    $request->integer(
                        'department_id'
                    )
                )
            );
        }

        $rows =
            (clone $query)
                ->latest(
                    'payroll_year'
                )
                ->latest(
                    'payroll_month'
                )
                ->paginate(30)
                ->withQueryString();

        return view(
            'admin.payroll.reports',
            [
                'pageTitle' =>
                    'Payroll Reports',

                'payrolls' =>
                    $rows,

                'employees' =>
                    Employee::orderBy(
                        'full_name'
                    )->get(),

                'departments' =>
                    Department::orderBy(
                        'name'
                    )->get(),

                'grossTotal' =>
                    (clone $query)
                        ->sum(
                            'gross_salary'
                        ),

                'deductionsTotal' =>
                    (clone $query)
                        ->sum(
                            'total_deductions'
                        ),

                'netTotal' =>
                    (clone $query)
                        ->sum(
                            'net_salary'
                        ),

                'paidTotal' =>
                    (clone $query)
                        ->where(
                            'status',
                            'Paid'
                        )
                        ->sum(
                            'net_salary'
                        ),

                'pendingTotal' =>
                    (clone $query)
                        ->whereIn(
                            'status',
                            [
                                'Draft',
                                'Generated',
                            ]
                        )
                        ->sum(
                            'net_salary'
                        ),
            ]
        );
    }
}