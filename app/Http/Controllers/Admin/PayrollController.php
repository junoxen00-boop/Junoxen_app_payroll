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
    ) {}

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
            'pageTitle' =>
                'Payroll Dashboard',

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
    public function index(Request $request): View
    {
        $query = Payroll::with('employee.department')
            ->latest('payroll_year')
            ->latest('payroll_month');

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

        if ($request->filled('department_id')) {
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

        return view('admin.payroll.index', [
            'payrolls' => $query
                ->paginate(20)
                ->withQueryString(),

            'employees' => Employee::orderBy(
                'full_name'
            )->get(),

            'departments' => Department::orderBy(
                'name'
            )->get(),

            'pageTitle' => 'All Payrolls',
        ]);
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
                ->with('department')
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
     *
     * Admin enters Bonus, Leave Days and LOP Days. Statutory PF,
     * Professional Tax, LOP deduction, totals and net payable are
     * calculated server-side by PayrollService.
     */
    public function store(
        StorePayrollRequest $request
    ): RedirectResponse {
        $employee = Employee::findOrFail(
            $request->integer('employee_id')
        );

        $payroll = $this->payrollService->generate(
            $employee,
            $request->integer('payroll_month'),
            $request->integer('payroll_year'),
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
     * Display generated payslip.
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
                'payroll' => $payroll,
                'pageTitle' =>
                    'Payroll ' .
                    $payroll->payroll_number,
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
                ['Paid', 'Cancelled'],
                true
            ),
            403,
            'Paid or cancelled payroll cannot be edited.'
        );

        return view(
            'admin.payroll.edit',
            [
                'payroll' => $payroll->load(
                    'employee'
                ),

                'pageTitle' => 'Edit Payroll',
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
        $this->payrollService->update(
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
            $payroll->status === 'Draft',
            422,
            'Only draft payroll can be deleted.'
        );

        DB::transaction(
            function () use (
                $payroll,
                $request
            ) {
                $this->payrollService->audit(
                    $payroll,
                    $request->user(),
                    'payroll_deleted',
                    $payroll->toArray(),
                    null,
                    $request->ip()
                );

                $payroll->delete();
            }
        );

        return redirect()
            ->route('admin.payroll.index')
            ->with(
                'success',
                'Draft payroll deleted successfully.'
            );
    }

    /**
     * Mark payroll as paid.
     */
    public function markPaid(
        Request $request,
        Payroll $payroll
    ): RedirectResponse {
        $data = $request->validate([
            'payment_date' => [
                'required',
                'date',
            ],

            'payment_method' => [
                'required',
                Rule::in([
                    'Bank Transfer',
                    'Cash',
                    'Cheque',
                    'Other',
                ]),
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
        ]);

        $this->payrollService->markPaid(
            $payroll,
            $data,
            $request->user(),
            $request->ip()
        );

        return back()->with(
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
            $payroll->status === 'Paid',
            422,
            'Paid payroll cannot be cancelled.'
        );

        DB::transaction(
            function () use (
                $payroll,
                $request
            ) {
                $old = $payroll->toArray();

                $payroll->update([
                    'status' => 'Cancelled',
                ]);

                $this->payrollService->audit(
                    $payroll,
                    $request->user(),
                    'payroll_cancelled',
                    $old,
                    $payroll->fresh()->toArray(),
                    $request->ip()
                );
            }
        );

        return back()->with(
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
        $query = Payroll::with(
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
            if ($request->filled($field)) {
                $query->where(
                    $field,
                    $request->input($field)
                );
            }
        }

        if ($request->filled(
            'department_id'
        )) {
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
            now()->format('Ymd-His') .
            '.csv';

        return response()->streamDownload(
            function () use ($query) {
                $out = fopen(
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

                                        $p->bonus,

                                        $p->pf_deduction,

                                        $p->professional_tax,

                                        $p->leave_days ?? 0,

                                        $p->lop_days ?? 0,

                                        $p->lop_deduction ?? 0,

                                        $p->paid_days ?? 0,

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
     * Bulk payroll page.
     */
    public function bulkCreate(): View
    {
        return view(
            'admin.payroll.bulk',
            [
                'pageTitle' =>
                    'Monthly Payroll',

                'employees' =>
                    Employee::where(
                        'status',
                        'Active'
                    )
                        ->with(
                            'salaryStructures'
                        )
                        ->orderBy(
                            'full_name'
                        )
                        ->get(),
            ]
        );
    }

    /**
     * Generate monthly payroll
     * for all active employees.
     */
    public function bulkStore(
        Request $request
    ): RedirectResponse {
        $data = $request->validate([
            'payroll_month' => [
                'required',
                'integer',
                'between:1,12',
            ],

            'payroll_year' => [
                'required',
                'integer',
                'between:2000,2100',
            ],
        ]);

        $generated = 0;

        $skipped = [];

        foreach (
            Employee::where(
                'status',
                'Active'
            )->get()
            as $employee
        ) {
            try {
                $this->payrollService
                    ->generate(
                        $employee,
                        (int) $data[
                            'payroll_month'
                        ],
                        (int) $data[
                            'payroll_year'
                        ],
                        [],
                        $request->user(),
                        $request->ip()
                    );

                $generated++;
            } catch (\Illuminate\Validation\ValidationException $e) {
                $message = collect($e->errors())->flatten()->first() ?: 'Payroll validation failed.';
                $skipped[] = $employee->full_name . ': ' . $message;
            } catch (\Throwable $e) {
                report($e);
                $skipped[] = $employee->full_name . ': Unable to generate payroll. Check the salary structure and application log.';
            }
        }

        return redirect()
            ->route(
                'admin.payroll.index',
                [
                    'payroll_month' =>
                        $data[
                            'payroll_month'
                        ],

                    'payroll_year' =>
                        $data[
                            'payroll_year'
                        ],
                ]
            )
            ->with(
                'success',
                "Generated {$generated} payroll record(s)."
            )
            ->with(
                'warning',
                $skipped
                    ? 'Skipped: ' .
                        implode(
                            ' | ',
                            $skipped
                        )
                    : null
            );
    }

    /**
     * Payroll reports.
     */
    public function reports(
        Request $request
    ): View {
        $query = Payroll::with(
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
            if ($request->filled($field)) {
                $query->where(
                    $field,
                    $request->input($field)
                );
            }
        }

        if ($request->filled(
            'department_id'
        )) {
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

        $rows = (clone $query)
            ->latest('payroll_year')
            ->latest('payroll_month')
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