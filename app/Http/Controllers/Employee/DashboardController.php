<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\TaskAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $employee = Auth::user()->employee;

        $assignedTasks = 0;
        $pendingTasks = 0;
        $inProgressTasks = 0;
        $completedTasks = 0;
        $completion = 0;

        $recentTasks = collect();

        if ($employee) {

            $assignedTasks = TaskAssignment::where('employee_id', $employee->id)->count();

            $pendingTasks = TaskAssignment::where('employee_id', $employee->id)
                ->where('status', 'Pending')
                ->count();

            $inProgressTasks = TaskAssignment::where('employee_id', $employee->id)
                ->where('status', 'In Progress')
                ->count();

            $completedTasks = TaskAssignment::where('employee_id', $employee->id)
                ->where('status', 'Completed')
                ->count();

            if ($assignedTasks > 0) {
                $completion = round(($completedTasks / $assignedTasks) * 100);
            }

            $recentTasks = TaskAssignment::with('task')
                ->where('employee_id', $employee->id)
                ->latest()
                ->take(5)
                ->get();
        }

return view('employee.dashboard', compact(
    'assignedTasks',
    'pendingTasks',
    'inProgressTasks',
    'completedTasks',
    'completion',
    'recentTasks'
));
    }
}