<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Task;
use App\Models\TaskAssignment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $departments = Department::count();
        $employees = Employee::count();
        $tasks = Task::count();

        $pendingTasks = TaskAssignment::where('status', 'Pending')->count();

        $inProgressTasks = TaskAssignment::where('status', 'In Progress')->count();

        $completedTasks = TaskAssignment::where('status', 'Completed')->count();
        $pendingReviews = TaskAssignment::where('review_status', 'Pending')->count();

        $overdueTasks = TaskAssignment::where('status', '!=', 'Completed')
            ->whereHas('task', function ($query) {
                $query->whereDate('due_date', '<', now());
            })
            ->count();

        $recentTasks = Task::with([
            'assignments.employee.department'
        ])->latest()->take(5)->get();

        $recentEmployees = Employee::with('department')
            ->latest()
            ->take(5)
            ->get();

        $upcomingDeadlines = Task::with([
            'assignments.employee'
        ])
            ->whereDate('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        return view('manager.dashboard', compact(
    'departments',
    'employees',
    'tasks',
    'pendingTasks',
    'pendingReviews',
    'inProgressTasks',
    'completedTasks',
    'overdueTasks',
    'recentTasks',
    'recentEmployees',
    'upcomingDeadlines'
));
    }
}