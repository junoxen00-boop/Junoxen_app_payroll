@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold">Manager Activity</h2>

            <p class="text-muted mb-0">
                Monitor all manager reviews and approvals.
            </p>
        </div>

    </div>

    <div class="card shadow-sm border-0">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">
                Recent Manager Activity
            </h5>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                <tr>

                    <th>#</th>
                    <th>Date</th>
                    <th>Manager</th>
                    <th>Employee</th>
                    <th>Task</th>
                    <th>Review</th>
                    <th>Progress</th>
                    <th>Comment</th>

                </tr>

                </thead>

                <tbody>

                @forelse($activities as $index => $activity)

                    <tr>

                        <td>{{ $index + 1 }}</td>

                        <td>
                            {{ optional($activity->reviewed_at)->format('d M Y H:i') }}
                        </td>

                        <td>
                            {{ $activity->reviewer->name ?? '-' }}
                        </td>

                        <td>
                            {{ $activity->employee->full_name ?? '-' }}
                        </td>

                        <td>
                            {{ $activity->task->title ?? '-' }}
                        </td>

                        <td>

                            @if($activity->review_status == 'Approved')

    <span class="badge bg-success">
        Approved
    </span>

@elseif($activity->review_status == 'Changes Requested')

    <span class="badge bg-danger">
        Changes Requested
    </span>

@elseif($activity->review_status == 'Advice')

    <span class="badge bg-info text-dark">
        Advice
    </span>

@else

    <span class="badge bg-warning text-dark">
        Pending
    </span>

@endif

                        </td>

                        <td>

                            <div class="progress" style="height:18px">

                                <div class="progress-bar"

                                    style="width: {{ $activity->progress }}%">

                                    {{ $activity->progress }}%

                                </div>

                            </div>

                        </td>

                       <td>

    @if($activity->review_status == 'Advice')

        {{ $activity->manager_advice ?? '-' }}

    @else

        {{ $activity->review_comment ?? $activity->manager_advice ?? '-' }}

    @endif

</td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="8" class="text-center py-5">

                            <h5>No manager activity found.</h5>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection