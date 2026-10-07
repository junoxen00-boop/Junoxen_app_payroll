<?php

use App\Http\Controllers\Admin\PayrollEmployeeController;
use App\Http\Controllers\Admin\PayrollManagementController;
use App\Http\Controllers\Admin\PayrollLeaveController;
use App\Http\Controllers\Admin\PayrollTimesheetController;
use App\Http\Controllers\Admin\PayrollSuperannuationController;
use App\Http\Controllers\Admin\PayrollStpController;
use App\Http\Controllers\Admin\PayrollManagementSettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'role:Admin',
])
    ->prefix('admin/payroll-management')
    ->name('admin.payroll-management.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Payroll Management Overview
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [PayrollManagementController::class, 'index']
        )->name('index');


        /*
        |--------------------------------------------------------------------------
        | Employee Payroll Cards
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/employees',
            [PayrollEmployeeController::class, 'index']
        )->name('employees.index');

        Route::get(
            '/employees/{employee}',
            [PayrollEmployeeController::class, 'show']
        )->name('employees.show');

        Route::put(
            '/employees/{employee}',
            [PayrollEmployeeController::class, 'update']
        )->name('employees.update');


        /*
        |--------------------------------------------------------------------------
        | Leave
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/leave',
            [PayrollLeaveController::class, 'index']
        )->name('leave.index');

        Route::post(
            '/leave',
            [PayrollLeaveController::class, 'store']
        )->name('leave.store');

        Route::delete(
            '/leave/{leave}',
            [PayrollLeaveController::class, 'destroy']
        )->name('leave.destroy');


        /*
        |--------------------------------------------------------------------------
        | Timesheets
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/timesheets',
            [PayrollTimesheetController::class, 'index']
        )->name('timesheets.index');

        Route::post(
            '/timesheets',
            [PayrollTimesheetController::class, 'store']
        )->name('timesheets.store');

        Route::delete(
            '/timesheets/{timesheet}',
            [PayrollTimesheetController::class, 'destroy']
        )->name('timesheets.destroy');


        /*
        |--------------------------------------------------------------------------
        | Superannuation
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/superannuation',
            [PayrollSuperannuationController::class, 'index']
        )->name('superannuation.index');

        Route::post(
            '/superannuation',
            [PayrollSuperannuationController::class, 'store']
        )->name('superannuation.store');


        /*
        |--------------------------------------------------------------------------
        | Single Touch Payroll
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/stp',
            [PayrollStpController::class, 'index']
        )->name('stp.index');

        Route::post(
            '/stp',
            [PayrollStpController::class, 'store']
        )->name('stp.store');


        /*
        |--------------------------------------------------------------------------
        | Payroll Settings
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/settings',
            [PayrollManagementSettingsController::class, 'index']
        )->name('settings.index');

        Route::post(
            '/settings',
            [PayrollManagementSettingsController::class, 'update']
        )->name('settings.update');
    });