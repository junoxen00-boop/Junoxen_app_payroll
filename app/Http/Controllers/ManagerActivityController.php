<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignment;
use Illuminate\View\View;

class ManagerActivityController extends Controller
{
    public function index(): View
    {
        $activities = TaskAssignment::with([
            'task',
            'employee',
            'reviewer',
        ])
        ->whereNotNull('reviewed_by')
        ->latest('reviewed_at')
        ->get();

        return view('admin.manager-activity.index', compact('activities'));
    }
}