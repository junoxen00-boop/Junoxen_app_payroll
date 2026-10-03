<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\TaskAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $pendingReviews = TaskAssignment::with([
                'employee.department',
                'task',
            ])
            ->where('review_status', 'Pending')
            ->where(function ($query) {
                $query->where('progress', '>', 0)
                      ->orWhere('status', 'In Progress')
                      ->orWhere('status', 'Completed');
            })
            ->latest()
            ->paginate(10);

        return view('manager.reviews.index', compact('pendingReviews'));
    }

    public function show(TaskAssignment $assignment): View
    {
        $assignment->load([
            'employee.department',
            'task',
            'reviewer',
        ]);

        return view('manager.reviews.show', compact('assignment'));
    }

   public function update(Request $request, TaskAssignment $assignment): RedirectResponse
{
    
    $validated = $request->validate([
        'review_status'  => 'required|in:Advice,Approved,Changes Requested',
        'review_comment' => 'nullable|string|max:1000',
        'manager_advice' => 'nullable|string|max:2000',
    ]);

    if ($validated['review_status'] === 'Advice' && empty($validated['manager_advice'])) {
        return back()->withErrors([
            'manager_advice' => 'Manager advice is required.'
        ])->withInput();
    }

    if (
        in_array($validated['review_status'], ['Approved', 'Changes Requested']) &&
        empty($validated['review_comment'])
    ) {
        return back()->withErrors([
            'review_comment' => 'Review comment is required.'
        ])->withInput();
    }

  if ($validated['review_status'] === 'Advice') {

    $assignment->update([
        'review_status'  => 'Pending',
        'manager_advice' => $validated['manager_advice'],
        'reviewed_by'    => auth()->id(),
        'reviewed_at'    => now(),
    ]);

    

}  



else {

        $assignment->update([
            'review_status'  => $validated['review_status'],
            'review_comment' => $validated['review_comment'],
            'manager_advice' => $validated['manager_advice'],
            'reviewed_by'    => auth()->id(),
            'reviewed_at'    => now(),
        ]);

        $assignment->refresh();



    }

    if ($assignment->employee && $assignment->employee->user) {

    $notificationTitle = match ($validated['review_status']) {
        'Advice' => 'Manager Advice',
        'Approved' => 'Task Approved',
        'Changes Requested' => 'Changes Requested',
    };

    $notificationMessage = match ($validated['review_status']) {
        'Advice' => $validated['manager_advice'],
        'Approved' => $validated['review_comment'],
        'Changes Requested' => $validated['review_comment'],
    };

    Notification::create([
        'user_id' => $assignment->employee->user->id,
        'title' => $notificationTitle,
        'message' => $notificationMessage,
        'type' => 'info',
        'url' => route('employee.tasks.show', $assignment),
    ]);
}

    return redirect()
        ->route('manager.reviews.index')
        ->with('success', 'Review submitted successfully.');
}
}