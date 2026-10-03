@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                Pending Task Reviews
            </h3>

            <p class="text-muted mb-0">
                Review employee task submissions.
            </p>

        </div>

    </div>

    <div class="card shadow-sm border-0">

        <div class="card-body">

            @if($pendingReviews->count())

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Task Code</th>

                            <th>Task</th>

                            <th>Employee</th>

                            <th>Department</th>

                            <th>Status</th>

                            <th>Progress</th>

                            <th>Submitted</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($pendingReviews as $review)

                        <tr>

                            <td>{{ $review->task->task_code }}</td>

                            <td>{{ $review->task->title }}</td>

                            <td>{{ $review->employee->full_name }}</td>

                            <td>{{ $review->employee->department->name }}</td>

                            <td>

                                <span class="badge bg-warning">

                                    {{ $review->status }}

                                </span>

                            </td>

                            <td>

                                {{ $review->progress }}%

                            </td>

                            <td>

                                {{ $review->updated_at->format('d M Y') }}

                            </td>

                            <td>

                                <a href="{{ route('manager.reviews.show', $review) }}"
                                 class="btn btn-primary btn-sm">

                                     Review

                                   </a>

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            {{ $pendingReviews->links() }}

            @else

            <div class="text-center py-5">

                <i class="bi bi-check-circle display-3 text-success"></i>

                <h4 class="mt-3">
                    No Pending Reviews
                </h4>

                <p class="text-muted">
                    All employee task submissions have been reviewed.
                </p>

            </div>

            @endif

        </div>

    </div>

</div>

@endsection