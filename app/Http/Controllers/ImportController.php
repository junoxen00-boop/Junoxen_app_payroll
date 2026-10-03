<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ImportController extends Controller
{
    public function index()
    {
        $pageTitle = 'Import Monthly To-Do List';

        $employees = Employee::orderBy('full_name')->get();

        return view('imports.index', compact(
            'pageTitle',
            'employees'
        ));
    }

  public function preview(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'month' => 'required',
        'file' => 'required|mimes:xlsx,xls,csv',
    ]);

    $rows = Excel::toArray([], $request->file('file'));

    return view('imports.preview', [
        'rows' => $rows[0],
        'employee' => Employee::findOrFail($request->employee_id),
        'month' => $request->month,
    ]);
}

public function import(Request $request)
{
    $employee = Employee::findOrFail($request->employee_id);

$currentSection = 'General';
$importedTasks = 0;

foreach ($request->rows as $row) {

    $row = json_decode($row, true);

    $taskName = trim($row[0] ?? '');
    $note = trim($row[1] ?? '');

    if ($taskName == '') {
        continue;
    }

    // Skip Excel header
    if (strtolower($taskName) == 'task') {
        continue;
    }

    // Detect section names
    if (in_array(strtoupper($taskName), [
        'DAILY',
        'WEEKLY',
        'MONTHLY',
        'YEARLY'
    ])) {

        $currentSection = ucfirst(strtolower($taskName));
        continue;
    }

    $task = Task::create([
        'title'       => $taskName,
        'description' => $note ?: null,
        'week'        => $currentSection,
        'category'    => $currentSection,
        'month'       => $request->month,
        'priority'    => 'Medium',
        'created_by'  => auth()->id(),
    ]);

    TaskAssignment::create([
        'task_id'     => $task->id,
        'employee_id' => $employee->id,
        'status'      => 'Pending',
        'progress'    => 0,
    ]);

    $importedTasks++;
}

       Mail::raw(
        "Hello {$employee->full_name},\n\n" .
        "Your monthly To-Do List for {$request->month} has been imported and assigned to you.\n\n" .
        "Please log in to the Employee Management System to view your assigned tasks.\n\n" .
        "Regards,\nJunoxen Employee Management",
        function ($message) use ($employee) {
            $message->to($employee->email)
                    ->subject('Monthly To-Do List Assigned');
        }
    );

   if ($employee->user_id && $importedTasks > 0) {
    Notification::create([
        'user_id'  => $employee->user_id,
        'title'    => 'New Tasks Assigned',
        'message'  => $importedTasks . ' new task(s) have been assigned to you.',
        'type'     => 'task',
        'url'      => route('tasks.index'),
        'is_read'  => false,
        'read_at'  => null,
    ]);
}

return redirect()
    ->route('imports.index')
    ->with(
        'success',
        "Tasks imported successfully. {$importedTasks} task(s) assigned and notification sent to the employee."
    );
}
}