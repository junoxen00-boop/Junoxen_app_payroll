@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="row mb-4">

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Total Employees</h6>
                    <h2>{{ $totalEmployees }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Total Tasks</h6>
                    <h2>{{ $totalTasks }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Pending</h6>
                    <h2 class="text-warning">{{ $pendingTasks }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>In Progress</h6>
                    <h2 class="text-primary">{{ $inProgressTasks }}</h2>
                </div>
            </div>
        </div>

    </div>

    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Completed</h6>
                    <h2 class="text-success">{{ $completedTasks }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Pending Review</h6>
                    <h2 class="text-warning">{{ $pendingReview }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Approved Tasks</h6>
                    <h2 class="text-success">{{ $approvedTasks }}</h2>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm border-0">

        <div class="card-header bg-dark text-white">

            <h5 class="mb-0">Task Review Summary</h5>

        </div>

        <div class="card-body">

            <table class="table table-bordered align-middle">

                <thead>

                <tr>

                    <th>Review Status</th>

                    <th>Total</th>

                </tr>

                </thead>

                <tbody>

                <tr>

                    <td>Pending Review</td>

                    <td>{{ $pendingReview }}</td>

                </tr>

                <tr>

                    <td>Approved</td>

                    <td>{{ $approvedTasks }}</td>

                </tr>

                <tr>

                    <td>Changes Requested</td>

                    <td>{{ $changesRequested }}</td>

                </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<div class="card shadow-sm border-0 mt-4">

    <div class="card-header bg-success text-white">

        <h5 class="mb-0">Task Status Overview</h5>

    </div>

    <div class="card-body">

    <div class="mx-auto" style="max-width: 350px; height: 350px;">

        <canvas id="taskChart"></canvas>

    </div>

</div>

</div>

<div class="card shadow-sm border-0 mt-4">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">Employee Performance Report</h5>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th class="text-center">Assigned</th>
                        <th class="text-center">Completed</th>
                        <th class="text-center">Pending</th>
                        <th class="text-center">In Progress</th>
                        <th class="text-center">Completion %</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($employeePerformance as $employee)

                        @php
                            $percentage = $employee->assigned_tasks > 0
                                ? round(($employee->completed_tasks / $employee->assigned_tasks) * 100)
                                : 0;
                        @endphp

                        <tr>

                            <td>{{ $employee->full_name }}</td>

                            <td>
                                {{ $employee->department->name ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ $employee->assigned_tasks }}
                            </td>

                            <td class="text-center text-success fw-bold">
                                {{ $employee->completed_tasks }}
                            </td>

                            <td class="text-center text-warning fw-bold">
                                {{ $employee->pending_tasks }}
                            </td>

                            <td class="text-center text-primary fw-bold">
                                {{ $employee->in_progress_tasks }}
                            </td>

                            <td>

                                <div class="progress" style="height:22px;">

                                    <div
                                        class="progress-bar"
                                        role="progressbar"
                                        style="width: {{ $percentage }}%;"
                                        aria-valuenow="{{ $percentage }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">

                                        {{ $percentage }}%

                                    </div>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="text-center">

                                No employee records found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

<div class="card shadow-sm border-0 mt-4">

    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Department Performance Report</h5>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th>Department</th>
                        <th class="text-center">Employees</th>
                        <th class="text-center">Assigned Tasks</th>
                        <th class="text-center">Completed</th>
                        <th class="text-center">Pending</th>
                        <th class="text-center">Completion %</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($departmentPerformance as $department)

                        @php

                            $employeeCount = $department->employees->count();

                           $departmentAssignedTasks = 0;
                           $departmentCompletedTasks = 0;
                           $departmentPendingTasks = 0;
                            foreach ($department->employees as $employee) {

                               $departmentAssignedTasks += $employee->taskAssignments->count();

$departmentCompletedTasks += $employee->taskAssignments
    ->where('status', 'Completed')
    ->count();

$departmentPendingTasks += $employee->taskAssignments
    ->where('status', 'Pending')
    ->count();
                            }

                            $percentage = $departmentAssignedTasks > 0
    ? round(($departmentCompletedTasks / $departmentAssignedTasks) * 100)
    : 0;

                        @endphp

                        <tr>

                            <td>{{ $department->name }}</td>

                            <td class="text-center">
                                {{ $employeeCount }}
                            </td>

                            <td class="text-center">
    {{ $departmentAssignedTasks }}
</td>

<td class="text-center text-success fw-bold">
    {{ $departmentCompletedTasks }}
</td>

<td class="text-center text-warning fw-bold">
    {{ $departmentPendingTasks }}
</td>

                            <td>

                                <div class="progress" style="height:22px;">

                                    <div
                                        class="progress-bar bg-success"
                                        role="progressbar"
                                        style="width: {{ $percentage }}%;">

                                        {{ $percentage }}%

                                    </div>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="text-center">

                                No departments found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@push('scripts')

<script>

    console.log(
    'Pending:', {{ $pendingTasks }},
    'In Progress:', {{ $inProgressTasks }},
    'Completed:', {{ $completedTasks }}
);

const ctx = document.getElementById('taskChart');

new Chart(ctx, {

    type: 'doughnut',

    data: {

        labels: [

            'Pending',

            'In Progress',

            'Completed'

        ],

       datasets: [{

    data: [

        {{ $pendingTasks }},

        {{ $inProgressTasks }},

        {{ $completedTasks }}

    ],

    backgroundColor: [

        '#f59e0b', // Pending
        '#3b82f6', // In Progress
        '#10b981'  // Completed

    ],

    borderColor: '#ffffff',

    borderWidth: 2

}]

    },

   options: {

    responsive: true,

    maintainAspectRatio: false,

    plugins: {

        legend: {
            position: 'bottom'
        }

    }

}

});

</script>

@endpush

@endsection