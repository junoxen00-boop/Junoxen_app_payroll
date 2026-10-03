<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\TaskAssignment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display Employee Tasks
     */
 public function index(Request $request): View
{
    $employee = Auth::user()->employee;

    if (! $employee) {
        abort(403, 'Employee record not found.');
    }

    $query = TaskAssignment::with([
        'task',
        'task.creator',
    ])->where('employee_id', $employee->id);

    // Search Task Title
    if ($request->filled('search')) {
        $query->whereHas('task', function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%');
        });
    }

    // Filter Priority
    if ($request->filled('priority')) {
        $query->whereHas('task', function ($q) use ($request) {
            $q->where('priority', $request->priority);
        });
    }

    // Filter Status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $assignments = $query->get();

    // Sort High → Medium → Low
    $assignments = $assignments->sortBy(function ($assignment) {

        return match ($assignment->task->priority) {

            'High' => 1,
            'Medium' => 2,
            'Low' => 3,

            default => 4,
        };

    });

    return view('employee.tasks.index', [
        'assignments' => $assignments,
    ]);
}

    /**
     * Show Task Details
     */
    public function show(TaskAssignment $assignment): View
    {
        $assignment->load([
            'task.creator',
            'employee.department',
        ]);

        $employee = Auth::user()->employee;

        if (! $employee || $assignment->employee_id !== $employee->id) {
            abort(403, 'Unauthorized access.');
        }

        return view('employee.tasks.show', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Employee Update Task
     */
    public function update(
        Request $request,
        TaskAssignment $assignment
    ): RedirectResponse
    {
    
        $employee = Auth::user()->employee;

        if (! $employee || $assignment->employee_id !== $employee->id) {
            abort(403, 'Unauthorized access.');
        }

        if ($assignment->review_status === 'Approved') {
            return back()->with(
                'error',
                'This task has already been approved by the manager.'
            );
        }

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'Pending',
                    'In Progress',
                    'Completed',
                ]),
            ],

            'progress' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        if ($request->hasFile('attachment')) {

            if (
                $assignment->attachment &&
                Storage::disk('public')->exists($assignment->attachment)
            ) {
                Storage::disk('public')->delete($assignment->attachment);
            }

            $validated['attachment'] = $request
                ->file('attachment')
                ->store('task-attachments', 'public');

        } else {

            $validated['attachment'] = $assignment->attachment;
        }

        if ((int) $validated['progress'] >= 100) {
            $validated['progress'] = 100;
            $validated['status'] = 'Completed';
        }

        $assignment->update([
            'status' => $validated['status'],
            'progress' => $validated['progress'],
            'remarks' => $validated['remarks'],
            'attachment' => $validated['attachment'],
            'review_status' => 'Pending',
            'review_comment' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

       


          $managers = \App\Models\User::whereHas('role', function ($query) {
            $query->where('name', 'Manager');
        })->get();

        foreach ($managers as $manager) {

            if ($assignment->status === 'Completed') {

                // Notification when task is completed
                Notification::create([
                    'user_id' => $manager->id,
                    'title' => 'Task Completed',
                    'message' => $employee->full_name
                        . ' completed the task: '
                        . $assignment->task->title,
                    'type' => 'info',
                    'url' => route('manager.reviews.show', $assignment),
                ]);

            } else {

                // Notification for progress/status update
                Notification::create([
                    'user_id' => $manager->id,
                    'title' => 'Task Updated',
                    'message' => $employee->full_name
                        . ' updated the task "' . $assignment->task->title
                        . '" — Progress: ' . $assignment->progress
                        . '%, Status: ' . $assignment->status . '.',
                    'type' => 'info',
                    'url' => route('manager.reviews.show', $assignment),
                ]);
            }
        }
               return redirect()
            ->route('employee.tasks.show', $assignment)
            ->with(
                'success',
                'Task updated successfully.'
            );
    }
}
