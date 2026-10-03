<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Task;
use App\Models\TaskAssignment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ==========================
        // Dashboard Statistics
        // ==========================

        $departments = Department::count();

        $employees = Employee::count();

        $tasks = Task::count();

        $pendingTasks = TaskAssignment::where('status', 'Pending')->count();

        $inProgressTasks = TaskAssignment::where('status', 'In Progress')->count();

        $completedTasks = TaskAssignment::where('status', 'Completed')->count();

        $overdueTasks = TaskAssignment::where('status', '!=', 'Completed')
            ->whereHas('task', function ($query) {
                $query->whereDate('due_date', '<', now());
            })
            ->count();

        // ==========================
        // Recent Tasks
        // ==========================

        $recentTasks = Task::with([
            'assignments.employee.department'
        ])
        ->latest()
        ->take(5)
        ->get();

        // ==========================
        // Recent Employees
        // ==========================

        $recentEmployees = Employee::with('department')
            ->latest()
            ->take(5)
            ->get();

        // ==========================
        // Upcoming Deadlines
        // ==========================

        $upcomingDeadlines = Task::with([
            'assignments.employee'
        ])
        ->whereDate('due_date', '>=', now())
        ->orderBy('due_date')
        ->take(5)
        ->get();
                // ==========================
        // Task Status Summary
        // ==========================

        $taskStatus = [
            'Pending' => $pendingTasks,
            'In Progress' => $inProgressTasks,
            'Completed' => $completedTasks,
            'Overdue' => $overdueTasks,
        ];

        // ==========================
        // Task Priority Summary
        // ==========================

        $taskPriority = [

            'Low' => Task::where('priority', 'Low')->count(),

            'Medium' => Task::where('priority', 'Medium')->count(),

            'High' => Task::where('priority', 'High')->count(),

            'Critical' => Task::where('priority', 'Critical')->count(),

        ];

        return view('dashboard', compact(

            'departments',

            'employees',

            'tasks',

            'pendingTasks',

            'inProgressTasks',

            'completedTasks',

            'overdueTasks',

            'recentTasks',

            'recentEmployees',

            'upcomingDeadlines',

            'taskStatus',

            'taskPriority'

        ));
    }
}