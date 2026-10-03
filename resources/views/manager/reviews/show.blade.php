@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="fw-bold mb-1">Task Review</h3>
            <p class="text-muted mb-0">
                Review employee task submission.
            </p>
        </div>

        <a href="{{ route('manager.reviews.index') }}"
           class="btn btn-secondary">
            Back
        </a>

    </div>

    <div class="row">

        <div class="col-lg-8">

            <div class="card shadow-sm mb-4">

                <div class="card-header fw-bold">
                    Task Information
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <strong>Task Code</strong><br>
                            {{ $assignment->task->task_code }}
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Priority</strong><br>
                            {{ $assignment->task->priority }}
                        </div>

                        <div class="col-md-12 mb-3">
                            <strong>Task Title</strong><br>
                            {{ $assignment->task->title }}
                        </div>

                        <div class="col-md-12 mb-3">
                            <strong>Description</strong><br>
                            {{ $assignment->task->description ?: 'No description available.' }}
                        </div>

                        <div class="col-md-6">
                            <strong>Start Date</strong><br>
                            {{ \Carbon\Carbon::parse($assignment->task->start_date)->format('d M Y') }}
                        </div>

                        <div class="col-md-6">
                            <strong>Due Date</strong><br>
                            {{ \Carbon\Carbon::parse($assignment->task->due_date)->format('d M Y') }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card shadow-sm">

                <div class="card-header fw-bold">
                    Employee Submission
                </div>

                <div class="card-body">

                    <p>
                        <strong>Employee</strong><br>
                        {{ $assignment->employee->full_name }}
                    </p>

                    <p>
                        <strong>Department</strong><br>
                        {{ $assignment->employee->department->name }}
                    </p>

                    <p>
                        <strong>Status</strong><br>
                        {{ $assignment->status }}
                    </p>

                    <p>
                        <strong>Progress</strong><br>
                        {{ $assignment->progress }}%
                    </p>

                    <p>
                        <strong>Employee Remarks</strong><br>
                        {{ $assignment->remarks ?: 'No remarks submitted.' }}
                    </p>

                    @if($assignment->attachment)
<p>
    <strong>Attachment</strong><br>

    <a href="{{ route('manager.attachment', $assignment) }}"
   class="btn btn-outline-primary btn-sm">

        Download Attachment

    </a>

</p>
@endif

                </div>

            </div>

        </div>

    </div>

    <div class="card shadow-sm">

        <div class="card-header fw-bold">
            Manager Review
        </div>

        <div class="card-body">

            <form method="POST"
                  action="{{ route('manager.reviews.update', $assignment) }}">


@csrf
@method('PUT')

<div class="mb-3">
    <label class="form-label">Review Decision</label>

    <select
        id="review_status"
        name="review_status"
        class="form-select"
        required>

        <option value="">Select</option>
        <option value="Advice">Manager Advice / Instructions</option>
        <option value="Approved">Approve Task</option>
        <option value="Changes Requested">Request Changes</option>

    </select>
</div>

<div class="mb-3" id="adviceBox" style="display:none;">
    <label class="form-label">Manager Advice / Instructions</label>

    <textarea
        name="manager_advice"
        rows="4"
        class="form-control"
        placeholder="Write instructions for the employee..."></textarea>
</div>

<div class="mb-3" id="commentBox" style="display:none;">
    <label class="form-label">Review Comment</label>

    <textarea
        name="review_comment"
        rows="4"
        class="form-control"></textarea>
</div>

<button type="submit" class="btn btn-success">
    Submit Review
</button>
</form>
        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const reviewStatus = document.getElementById('review_status');
    const adviceBox = document.getElementById('adviceBox');
    const commentBox = document.getElementById('commentBox');

    function toggleFields() {

        adviceBox.style.display = 'none';
        commentBox.style.display = 'none';

        if (reviewStatus.value === 'Advice') {
            adviceBox.style.display = 'block';
        } else if (
            reviewStatus.value === 'Approved' ||
            reviewStatus.value === 'Changes Requested'
        ) {
            commentBox.style.display = 'block';
        }
    }

    reviewStatus.addEventListener('change', toggleFields);
    toggleFields();

});
</script>

@endpush