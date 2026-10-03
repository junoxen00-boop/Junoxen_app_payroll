<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\TaskAssignment;

class ReportController extends Controller
{
    public function index()
    {
        $pageTitle = 'Reports';

        $totalEmployees = Employee::count();

        $totalTasks = TaskAssignment::count();

        $pendingTasks = TaskAssignment::where('status', 'Pending')->count();

        $inProgressTasks = TaskAssignment::where('status', 'In Progress')->count();

        $completedTasks = TaskAssignment::where('status', 'Completed')->count();

        $pendingReview = TaskAssignment::where('review_status', 'Pending')->count();

        $approvedTasks = TaskAssignment::where('review_status', 'Approved')->count();

        $changesRequested = TaskAssignment::where('review_status', 'Changes Requested')->count();

        $employeePerformance = Employee::with('department')
            ->withCount([
                'taskAssignments as assigned_tasks',
                'taskAssignments as completed_tasks' => function ($query) {
                    $query->where('status', 'Completed');
                },
                'taskAssignments as pending_tasks' => function ($query) {
                    $query->where('status', 'Pending');
                },
                'taskAssignments as in_progress_tasks' => function ($query) {
                    $query->where('status', 'In Progress');
                },
            ])
            ->get();

       $departmentPerformance = Department::with('employees.taskAssignments')->get();

        return view('reports.index', compact(
            'pageTitle',
            'totalEmployees',
            'totalTasks',
            'pendingTasks',
            'inProgressTasks',
            'completedTasks',
            'pendingReview',
            'approvedTasks',
            'changesRequested',
            'employeePerformance',
            'departmentPerformance'
        ));
    }
}