<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Junoxen payroll policy
    |--------------------------------------------------------------------------
    |
    | LOP is prorated using calendar days because the existing Junoxen payroll
    | implementation already used calendar-day proration. If company policy
    | changes, update this centrally and adjust the corresponding tests.
    |
    */
    'lop_basis' => env('PAYROLL_LOP_BASIS', 'calendar_days'),

    /*
    |--------------------------------------------------------------------------
    | Telangana Professional Tax
    |--------------------------------------------------------------------------
    |
    | Authority: Telangana Commercial Taxes Department, First Schedule to the
    | Telangana Tax on Professions, Trades, Callings and Employments Act, 1987.
    | Verified 2026-10-03 from the official Telangana Commercial Taxes portal.
    |
    */
    'professional_tax' => [
        'state' => 'Telangana',
        'effective_from' => '1987-06-15',
        'slabs' => [
            ['up_to' => 15000, 'tax' => 0],
            ['up_to' => 20000, 'tax' => 150],
            ['up_to' => null, 'tax' => 200],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Employee Provident Fund contribution
    |--------------------------------------------------------------------------
    |
    | Employee contribution defaults to 12%. EPFO also identifies categories
    | where a 10% rate can apply; therefore the rate is configurable via env.
    |
    | Wage ceiling history (S.O. 5109(E) for the 2026 revision):
    | - Rs 15,000 from 2014-09-01.
    | - Rs 25,000 from 2026-09-17 (Gazette notification dated 2026-09-17;
    |   Ministry/PIB announcement effective 2026-09-17).
    |
    | The project has no DA/retaining-allowance fields. The statutory PF wage
    | basis therefore uses salary_structure.pf_wage_basis when provided, and
    | otherwise falls back to Basic Salary. Payroll/accounting should confirm
    | that this reflects Junoxen's actual PF wage definition.
    |
    */
    'provident_fund' => [
        'employee_rate_percent' => (string) env('PAYROLL_PF_EMPLOYEE_RATE_PERCENT', '12'),
        'wage_ceiling_history' => [
            ['effective_from' => '2014-09-01', 'amount' => 15000],
            ['effective_from' => '2026-09-17', 'amount' => 25000],
        ],
        'round_to_nearest_rupee' => true,
    ],
];

Route::middleware([
    'auth',
    'role:Admin',
])
->prefix('admin')
->name('admin.')
->group(function () {

    Route::get(
        '/payroll/dashboard',
        [
            AdminPayrollController::class,
            'dashboard',
        ]
    )->name(
        'payroll.dashboard'
    );

    Route::get(
        '/payroll/reports',
        [
            AdminPayrollController::class,
            'reports',
        ]
    )->name(
        'payroll.reports'
    );

    Route::post(
        '/payroll/{payroll}/mark-paid',
        [
            AdminPayrollController::class,
            'markPaid',
        ]
    )->name(
        'payroll.mark-paid'
    );

    Route::post(
        '/payroll/{payroll}/cancel',
        [
            AdminPayrollController::class,
            'cancel',
        ]
    )->name(
        'payroll.cancel'
    );

    Route::get(
        '/payroll-export.csv',
        [
            AdminPayrollController::class,
            'export',
        ]
    )->name(
        'payroll.export'
    );

    Route::resource(
        'payroll',
        AdminPayrollController::class
    )->parameters([
        'payroll' =>
            'payroll',
    ]);
});