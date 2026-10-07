<?php

use App\Http\Controllers\Admin\PayrollController as AdminPayrollController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\PayrollController as EmployeePayrollController;
use App\Http\Controllers\Employee\TaskController as EmployeeTaskController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Manager\ReviewController;
use App\Http\Controllers\ManagerActivityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


/*
|--------------------------------------------------------------------------
| Root
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    function () {
        return redirect()
            ->route('login');
    }
);


/*
|--------------------------------------------------------------------------
| Dashboards
|--------------------------------------------------------------------------
*/

Route::get(
    '/dashboard',
    \App\Http\Controllers\Admin\DashboardController::class
)
    ->middleware(
        [
            'auth',
            'role:Admin',
        ]
    )
    ->name('dashboard');


Route::get(
    '/manager/dashboard',
    ManagerDashboardController::class
)
    ->middleware(
        [
            'auth',
            'role:Manager',
        ]
    )
    ->name(
        'manager.dashboard'
    );


Route::get(
    '/employee/dashboard',
    EmployeeDashboardController::class
)
    ->middleware(
        [
            'auth',
            'role:Employee',
        ]
    )
    ->name(
        'employee.dashboard'
    );


/*
|--------------------------------------------------------------------------
| Manager reviews
|--------------------------------------------------------------------------
*/

Route::get(
    '/manager/reviews',
    [
        ReviewController::class,
        'index',
    ]
)
    ->middleware(
        [
            'auth',
            'role:Manager',
        ]
    )
    ->name(
        'manager.reviews.index'
    );


Route::get(
    '/manager/reviews/{assignment}',
    [
        ReviewController::class,
        'show',
    ]
)
    ->middleware(
        [
            'auth',
            'role:Manager',
        ]
    )
    ->name(
        'manager.reviews.show'
    );


Route::put(
    '/manager/reviews/{assignment}',
    [
        ReviewController::class,
        'update',
    ]
)
    ->middleware(
        [
            'auth',
            'role:Manager',
        ]
    )
    ->name(
        'manager.reviews.update'
    );


/*
|--------------------------------------------------------------------------
| Authenticated application routes
|--------------------------------------------------------------------------
*/

Route::middleware(
    'auth'
)->group(
    function () {

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/users',
            [
                UserController::class,
                'index',
            ]
        )
            ->middleware(
                'role:Admin'
            )
            ->name(
                'users.index'
            );


        Route::get(
            '/users/{user}/edit',
            [
                UserController::class,
                'edit',
            ]
        )
            ->middleware(
                'role:Admin'
            )
            ->name(
                'users.edit'
            );


        Route::put(
            '/users/{user}',
            [
                UserController::class,
                'update',
            ]
        )
            ->middleware(
                'role:Admin'
            )
            ->name(
                'users.update'
            );


        /*
        |--------------------------------------------------------------------------
        | Manager activity
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/manager-activity',
            [
                ManagerActivityController::class,
                'index',
            ]
        )
            ->middleware(
                'role:Admin'
            )
            ->name(
                'manager.activity.index'
            );


        /*
        |--------------------------------------------------------------------------
        | Notifications
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/notifications',
            [
                NotificationController::class,
                'index',
            ]
        )
            ->name(
                'notifications.index'
            );


        Route::patch(
            '/notifications/{notification}/read',
            [
                NotificationController::class,
                'markAsRead',
            ]
        )
            ->name(
                'notifications.read'
            );


        Route::post(
            '/notifications/read-all',
            [
                NotificationController::class,
                'markAllAsRead',
            ]
        )
            ->name(
                'notifications.readAll'
            );


        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/settings',
            [
                SettingsController::class,
                'index',
            ]
        )
            ->middleware(
                'role:Admin'
            )
            ->name(
                'settings.index'
            );


        Route::post(
            '/settings',
            [
                SettingsController::class,
                'update',
            ]
        )
            ->middleware(
                'role:Admin'
            )
            ->name(
                'settings.update'
            );


        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/change-password',
            [
                PasswordController::class,
                'showChangeForm',
            ]
        )
            ->name(
                'password.change'
            );


        Route::post(
            '/change-password',
            [
                PasswordController::class,
                'updatePassword',
            ]
        )
            ->name(
                'password.update'
            );


        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/profile',
            [
                ProfileController::class,
                'edit',
            ]
        )
            ->name(
                'profile.edit'
            );


        Route::patch(
            '/profile',
            [
                ProfileController::class,
                'update',
            ]
        )
            ->name(
                'profile.update'
            );


        Route::delete(
            '/profile',
            [
                ProfileController::class,
                'destroy',
            ]
        )
            ->name(
                'profile.destroy'
            );


        /*
        |--------------------------------------------------------------------------
        | Main Resources
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'departments',
            DepartmentController::class
        );


        Route::resource(
            'employees',
            EmployeeController::class
        );


        Route::resource(
            'tasks',
            TaskController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports',
            [
                \App\Http\Controllers\ReportController::class,
                'index',
            ]
        )
            ->middleware(
                'role:Admin'
            )
            ->name(
                'reports.index'
            );


        /*
        |--------------------------------------------------------------------------
        | Imports
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/imports',
            [
                ImportController::class,
                'index',
            ]
        )
            ->middleware(
                [
                    'auth',
                    'role:Admin,Manager',
                ]
            )
            ->name(
                'imports.index'
            );


        Route::post(
            '/imports/preview',
            [
                ImportController::class,
                'preview',
            ]
        )
            ->middleware(
                [
                    'auth',
                    'role:Admin,Manager',
                ]
            )
            ->name(
                'imports.preview'
            );


        Route::post(
            '/imports/import',
            [
                ImportController::class,
                'import',
            ]
        )
            ->middleware(
                [
                    'auth',
                    'role:Admin,Manager',
                ]
            )
            ->name(
                'imports.import'
            );


        /*
        |--------------------------------------------------------------------------
        | Manager reports
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/manager/reports',
            [
                \App\Http\Controllers\ReportController::class,
                'index',
            ]
        )
            ->middleware(
                'role:Manager'
            )
            ->name(
                'manager.reports.index'
            );


        /*
        |--------------------------------------------------------------------------
        | Employee Attachment
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/employee/attachment/{assignment}',
            function (
                \App\Models\TaskAssignment $assignment
            ) {
                abort_unless(
                    auth()
                        ->user()
                        ->employee
                        ?->id ===
                    $assignment
                        ->employee_id,
                    403
                );

                return Storage::disk(
                    'public'
                )->response(
                    $assignment
                        ->attachment
                );
            }
        )
            ->name(
                'employee.attachment'
            );


        /*
        |--------------------------------------------------------------------------
        | Manager Attachment
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/manager/attachment/{assignment}',
            function (
                \App\Models\TaskAssignment $assignment
            ) {
                abort_unless(
                    auth()
                        ->user()
                        ->role
                        ->name ===
                    'Manager',
                    403
                );

                return Storage::disk(
                    'public'
                )->download(
                    $assignment
                        ->attachment
                );
            }
        )
            ->middleware(
                'role:Manager'
            )
            ->name(
                'manager.attachment'
            );


        /*
        |--------------------------------------------------------------------------
        | Employee Tasks
        |--------------------------------------------------------------------------
        */

        Route::middleware(
            'role:Employee'
        )->group(
            function () {

                Route::get(
                    '/employee/tasks/{assignment}',
                    [
                        EmployeeTaskController::class,
                        'show',
                    ]
                )
                    ->name(
                        'employee.tasks.show'
                    );


                Route::put(
                    '/employee/tasks/{assignment}',
                    [
                        EmployeeTaskController::class,
                        'update',
                    ]
                )
                    ->name(
                        'employee.tasks.update'
                    );


                Route::get(
                    '/employee/tasks',
                    [
                        EmployeeTaskController::class,
                        'index',
                    ]
                )
                    ->name(
                        'employee.tasks.index'
                    );
            }
        );
    }
);


/*
|--------------------------------------------------------------------------
| Development Diagnostics
|--------------------------------------------------------------------------
*/

if (
    app()->environment(
        'local'
    )
) {
    Route::get(
        '/test-route',
        function () {
            return
                'Test Route Working';
        }
    )
        ->name(
            'test.route'
        );


    Route::get(
        '/check-role',
        function () {
            return response()
                ->json(
                    [
                        'user' =>
                            auth()
                                ->user()
                                ->name,

                        'role' =>
                            auth()
                                ->user()
                                ->role
                                ->name,

                        'role_id' =>
                            auth()
                                ->user()
                                ->role_id,
                    ]
                );
        }
    )
        ->middleware(
            'auth'
        );
}


/*
|--------------------------------------------------------------------------
| JUNOXEN PAYROLL - ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(
    [
        'auth',
        'role:Admin',
    ]
)
    ->prefix('admin')
    ->name('admin.')
    ->group(
        function () {

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/payroll/dashboard',
                [
                    AdminPayrollController::class,
                    'dashboard',
                ]
            )
                ->name(
                    'payroll.dashboard'
                );


            /*
            |--------------------------------------------------------------------------
            | Reports
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/payroll/reports',
                [
                    AdminPayrollController::class,
                    'reports',
                ]
            )
                ->name(
                    'payroll.reports'
                );


            /*
            |--------------------------------------------------------------------------
            | Approve all pending payrolls at once
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | This must appear BEFORE the dynamic
            | /payroll/{payroll} resource route.
            |
            */

            Route::post(
                '/payroll/mark-all-paid',
                [
                    AdminPayrollController::class,
                    'markAllPaid',
                ]
            )
                ->name(
                    'payroll.mark-all-paid'
                );


            /*
            |--------------------------------------------------------------------------
            | Single payroll payment
            |--------------------------------------------------------------------------
            */

            Route::post(
                '/payroll/{payroll}/mark-paid',
                [
                    AdminPayrollController::class,
                    'markPaid',
                ]
            )
                ->name(
                    'payroll.mark-paid'
                );


            /*
            |--------------------------------------------------------------------------
            | Cancel payroll
            |--------------------------------------------------------------------------
            */

            Route::post(
                '/payroll/{payroll}/cancel',
                [
                    AdminPayrollController::class,
                    'cancel',
                ]
            )
                ->name(
                    'payroll.cancel'
                );


            /*
            |--------------------------------------------------------------------------
            | Export
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/payroll-export.csv',
                [
                    AdminPayrollController::class,
                    'export',
                ]
            )
                ->name(
                    'payroll.export'
                );


            /*
            |--------------------------------------------------------------------------
            | Payroll Resource
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'payroll',
                AdminPayrollController::class
            )
                ->parameters(
                    [
                        'payroll' =>
                            'payroll',
                    ]
                );
        }
    );


/*
|--------------------------------------------------------------------------
| EMPLOYEE PAYROLL - READ ONLY
|--------------------------------------------------------------------------
*/

Route::middleware(
    [
        'auth',
        'role:Employee',
    ]
)
    ->prefix('employee')
    ->name('employee.')
    ->group(
        function () {

            Route::get(
                '/payroll',
                [
                    EmployeePayrollController::class,
                    'index',
                ]
            )
                ->name(
                    'payroll.index'
                );


            Route::get(
                '/payroll/{payroll}',
                [
                    EmployeePayrollController::class,
                    'show',
                ]
            )
                ->name(
                    'payroll.show'
                );


            Route::get(
                '/payroll/{payroll}/print',
                [
                    EmployeePayrollController::class,
                    'print',
                ]
            )
                ->name(
                    'payroll.print'
                );
        }
    );


/*
|--------------------------------------------------------------------------
| Authentication routes
|--------------------------------------------------------------------------
*/

require __DIR__ .
    '/it_support.php';
    
require __DIR__.'/payroll_management.php';

require __DIR__ .
    '/auth.php';