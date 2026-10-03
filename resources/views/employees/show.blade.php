@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <style>
        .profile-card,
        .stat-card,
        .table-card,
        .chart-card {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.06);
        }

        .stat-card {
            transition: all 0.25s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        }

        .profile-avatar {
            width: 86px;
            height: 86px;
            border-radius: 24px;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            color: #ffffff;
            font-weight: 700;
        }

        .info-label {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .info-value {
            font-weight: 600;
            color: #212529;
        }

        .chart-box {
            position: relative;
            min-height: 280px;
        }

        .table th {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #6c757d;
        }

        .breadcrumb {
            margin-bottom: 0;
        }

        @media (max-width: 767px) {
            .chart-box {
                min-height: 240px;
            }

            .profile-avatar {
                width: 70px;
                height: 70px;
                font-size: 28px;
            }
        }
    </style>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/dashboard') }}" class="text-decoration-none">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('employees.index') }}" class="text-decoration-none">Employees</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Employee Profile
                    </li>
                </ol>
            </nav>

            <h1 class="h3 mb-1">Employee Profile</h1>
            <p class="text-muted mb-0">View employee details, task performance, and assigned work.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                Back to Employees
            </a>

            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary">
                Edit Employee
            </a>
        </div>
    </div>

    <div class="card profile-card mb-4">
        <div class="card-body p-4">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-lg-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="profile-avatar">
                            {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                        </div>

                        <div>
                            <h4 class="mb-1">{{ $employee->full_name }}</h4>
                            <div class="text-muted">{{ $employee->employee_id }}</div>

                            @if ($employee->status === 'Active')
                                <span class="badge bg-success mt-2">Active</span>
                            @else
                                <span class="badge bg-secondary mt-2">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $employee->email }}</div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="info-label">Mobile Number</div>
                            <div class="info-value">{{ $employee->mobile_number }}</div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="info-label">Department</div>
                            <div class="info-value">{{ $employee->department?->name ?? '—' }}</div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="info-label">Designation</div>
                            <div class="info-value">{{ $employee->designation }}</div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="info-label">Joining Date</div>
                            <div class="info-value">
                                {{ $employee->joining_date?->format('d M Y') }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="info-label">Created Date</div>
                            <div class="info-value">
                                {{ $employee->created_at?->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $statCards = [
            [
                'title' => 'Total Tasks',
                'count' => $taskStats['total_tasks'] ?? 0,
                'class' => 'bg-primary',
                'icon' => '📋',
            ],
            [
                'title' => 'Pending Tasks',
                'count' => $taskStats['pending_tasks'] ?? 0,
                'class' => 'bg-warning',
                'icon' => '⏳',
            ],
            [
                'title' => 'In Progress',
                'count' => $taskStats['in_progress_tasks'] ?? 0,
                'class' => 'bg-info',
                'icon' => '🚀',
            ],
            [
                'title' => 'Completed',
                'count' => $taskStats['completed_tasks'] ?? 0,
                'class' => 'bg-success',
                'icon' => '✅',
            ],
            [
                'title' => 'Overdue',
                'count' => $taskStats['overdue_tasks'] ?? 0,
                'class' => 'bg-danger',
                'icon' => '⚠️',
            ],
        ];
    @endphp

    <div class="row g-4 mb-4">
        @foreach ($statCards as $card)
            <div class="col-12 col-sm-6 col-xl">
                <div class="card stat-card {{ $card['class'] }} text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-2 opacity-75">{{ $card['title'] }}</p>
                                <h3 class="fw-bold mb-0">{{ $card['count'] }}</h3>
                            </div>

                            <div class="fs-2">
                                {{ $card['icon'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-5">
            <div class="card chart-card h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-1">Task Progress Chart</h5>
                    <p class="text-muted small mb-0">Employee task status breakdown.</p>
                </div>

                <div class="card-body">
                    <div class="chart-box">
                        <canvas id="employeeProgressChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="card table-card h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Assigned Tasks</h5>
                        <p class="text-muted small mb-0">All tasks assigned to {{ $employee->full_name }}.</p>
                    </div>

                    <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">
                        + Add Task
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Task</th>
                                    <th>Priority</th>
                                    <th>Start Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($employeeTasks as $task)
                                    <tr>
                                        <td>
                                            <a href="{{ route('tasks.show',$task->task) }}" class="fw-semibold text-decoration-none">
                                                {{ $task->task->title }}
                                            </a>
                                            <div class="small text-muted">
                                                {{ $task->task_id }}
                                            </div>
                                        </td>

                                        <td>
                                            @if ($task->task->priority=== 'High')
                                                <span class="badge bg-danger">High</span>
                                            @elseif ($task->task->priority === 'Medium')
                                                <span class="badge bg-warning text-dark">Medium</span>
                                            @else
                                                <span class="badge bg-info text-dark">Low</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ $task->task->start_date?->format('d M Y') }}
                                        </td>

                                        <td>
                                            {{ $task->task->due_date?->format('d M Y') }}
                                        </td>

                                        <td>
                                            @if ($task->status === 'Completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif ($task->status === 'In Progress')
                                                <span class="badge bg-primary">In Progress</span>
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            No tasks assigned to this employee yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($employeeTasks->hasPages())
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-center">
                            {{ $employeeTasks->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('employeeProgressChart');

new Chart(ctx, {

    type: 'doughnut',

    data: {

        labels: [
            'Pending',
            'In Progress',
            'Completed',
            'Overdue'
        ],

        datasets: [{

            data: [

                {{ $taskStats['pending_tasks'] }},
                {{ $taskStats['in_progress_tasks'] }},
                {{ $taskStats['completed_tasks'] }},
                {{ $taskStats['overdue_tasks'] }}

            ],

            backgroundColor: [

                '#6c757d',
                '#0d6efd',
                '#198754',
                '#dc3545'

            ],

            borderWidth:2

        }]

    },

    options:{

        responsive:true,
        maintainAspectRatio:false,

        plugins:{
            legend:{
                position:'bottom'
            }
        }

    }

});

</script>
@endsection