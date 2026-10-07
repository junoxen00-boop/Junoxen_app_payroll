<?php

use App\Http\Controllers\Admin\ItSupportController;
use App\Http\Controllers\ItSupport\EmployeeTicketController;
use App\Http\Controllers\ItSupport\ItTicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Employee IT Support
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Employee'])
    ->prefix('employee/it-support')
    ->name('employee.it-support.')
    ->group(function () {
        Route::get('/', [EmployeeTicketController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeTicketController::class, 'create'])->name('create');
        Route::post('/', [EmployeeTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [EmployeeTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/comments', [EmployeeTicketController::class, 'comment'])->name('comments.store');
    });

/*
|--------------------------------------------------------------------------
| IT Technician Queue
|--------------------------------------------------------------------------
|
| Role middleware allows Employee accounts into this route group. Each
| controller action additionally verifies that the authenticated employee
| belongs to the IT department before exposing technician functionality.
|
*/

Route::middleware(['auth', 'role:Employee'])
    ->prefix('it-support')
    ->name('it-support.')
    ->group(function () {
        Route::get('/dashboard', [ItTicketController::class, 'dashboard'])->name('dashboard');
        Route::get('/open', [ItTicketController::class, 'open'])->name('open');
        Route::get('/assigned', [ItTicketController::class, 'assigned'])->name('assigned');
        Route::get('/resolved', [ItTicketController::class, 'resolved'])->name('resolved');
        Route::get('/tickets/{ticket}', [ItTicketController::class, 'show'])->name('show');
        Route::post('/tickets/{ticket}/assign-self', [ItTicketController::class, 'assignSelf'])->name('assign-self');
        Route::patch('/tickets/{ticket}/status', [ItTicketController::class, 'updateStatus'])->name('status.update');
        Route::post('/tickets/{ticket}/resolve', [ItTicketController::class, 'resolve'])->name('resolve');
        Route::post('/tickets/{ticket}/comments', [ItTicketController::class, 'comment'])->name('comments.store');
    });

/*
|--------------------------------------------------------------------------
| Admin IT Support
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin/it-support')
    ->name('admin.it-support.')
    ->group(function () {
        Route::get('/', [ItSupportController::class, 'index'])->name('index');
        Route::get('/{ticket}', [ItSupportController::class, 'show'])->name('show');
        Route::post('/{ticket}/assign', [ItSupportController::class, 'assign'])->name('assign');
        Route::patch('/{ticket}', [ItSupportController::class, 'update'])->name('update');
        Route::post('/{ticket}/resolve', [ItSupportController::class, 'resolve'])->name('resolve');
        Route::post('/{ticket}/reopen', [ItSupportController::class, 'reopen'])->name('reopen');
        Route::post('/{ticket}/comments', [ItSupportController::class, 'comment'])->name('comments.store');
    });
