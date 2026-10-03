@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">

                {{ $assignment->task->title }}

            </h2>

            <small class="text-muted">

                {{ $assignment->task->task_code }}

            </small>

        </div>

        <a
            href="{{ route('employee.dashboard') }}"
            class="btn btn-outline-secondary">

            ← Back

        </a>

    </div>

    <div class="row">

        <div class="col-lg-5">

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">

                        Task Information

                    </h5>

                </div>

                <div class="card-body">

                    <table class="table table-borderless mb-0">

                        <tr>

                            <th width="160">

                                Task Code

                            </th>

                            <td>

                                {{ $assignment->task->task_code }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Title

                            </th>

                            <td>

                                {{ $assignment->task->title }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Description

                            </th>

                            <td>

                                {{ $assignment->task->description ?: 'N/A' }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Priority

                            </th>

                            <td>

                                @switch($assignment->task->priority)

                                    @case('Critical')

                                        <span class="badge bg-danger">

                                            Critical

                                        </span>

                                        @break

                                    @case('High')

                                        <span class="badge bg-warning text-dark">

                                            High

                                        </span>

                                        @break

                                    @case('Medium')

                                        <span class="badge bg-info text-dark">

                                            Medium

                                        </span>

                                        @break

                                    @default

                                        <span class="badge bg-secondary">

                                            Low

                                        </span>

                                @endswitch

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Start Date

                            </th>

                            <td>

                                {{ \Carbon\Carbon::parse($assignment->task->start_date)->format('d M Y') }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Due Date

                            </th>

                            <td>

                                {{ \Carbon\Carbon::parse($assignment->task->due_date)->format('d M Y') }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Created By

                            </th>

                            <td>

                                {{ $assignment->task->creator->name }}

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0">

    <div class="card-header bg-success text-white">

        <h5 class="mb-0">

            Update Task

        </h5>

    </div>

    <div class="card-body">

    @if($assignment->review_status === 'Approved')

<div class="alert alert-success mb-3">

    <strong>Task Approved!</strong><br>

    Your manager has approved this task.
    This task can no longer be modified.

</div>

@endif

        <form
            action="{{ route('employee.tasks.update', $assignment) }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="mb-3">

                <label class="form-label fw-semibold">

                    Status

                </label>

                <select
    name="status"
    class="form-select @error('status') is-invalid @enderror"
    @disabled($assignment->review_status === 'Approved')>

                    <option value="Pending"
                        @selected(old('status', $assignment->status) == 'Pending')>

                        Pending

                    </option>

                    <option value="In Progress"
                        @selected(old('status', $assignment->status) == 'In Progress')>

                        In Progress

                    </option>

                    <option value="Completed"
                        @selected(old('status', $assignment->status) == 'Completed')>

                        Completed

                    </option>

                </select>

                @error('status')

                    <div class="invalid-feedback">

                        {{ $message }}

                    </div>

                @enderror

            </div>

            <div class="mb-4">

                <label class="form-label fw-semibold">

                    Progress

                </label>

                <input
                    type="range"
                    name="progress"
                    id="progressRange"
                    class="form-range"
                    min="0"
                    max="100"
                    value="{{ old('progress', $assignment->progress) }}"
                    @disabled($assignment->review_status === 'Approved')>

                <div class="progress mt-2" style="height:20px;">

                    <div
                        id="progressBar"
                        class="progress-bar bg-success"
                        role="progressbar"
                        style="width: {{ old('progress', $assignment->progress) }}%;">

                        <span id="progressValue">

                            {{ old('progress', $assignment->progress) }}%

                        </span>

                    </div>

                </div>

                @error('progress')

                    <small class="text-danger">

                        {{ $message }}

                    </small>

                @enderror

            </div>

            <div class="mb-4">

                <label class="form-label fw-semibold">

                    Remarks

                </label>

                <textarea
                    name="remarks"
                    rows="5"
                    class="form-control @error('remarks') is-invalid @enderror"
                    placeholder="Write your work update..."
                    @disabled($assignment->review_status === 'Approved')
                    >{{ old('remarks', $assignment->remarks) }}</textarea>

                @error('remarks')

                    <div class="invalid-feedback">

                        {{ $message }}

                    </div>

                @enderror

            </div>

                        <div class="mb-4">

                <label class="form-label fw-semibold">

                    Attachment

                </label>

                <input
                    type="file"
                    name="attachment"
                    class="form-control @error('attachment') is-invalid @enderror"
                    @disabled($assignment->review_status === 'Approved')>

                @error('attachment')

                    <div class="invalid-feedback">

                        {{ $message }}

                    </div>

                @enderror

                @if($assignment->attachment)

                    <div class="mt-3">

                        <a
                            href="{{ route('employee.attachment', $assignment) }}"
                    
                            class="btn btn-outline-primary btn-sm">

                            📎 View Attachment

                        </a>

                    </div>

                @endif

            </div>

            <hr class="my-4">

            <h5 class="fw-bold mb-3">

                Manager Review

            </h5>

            <table class="table table-bordered align-middle">

                <tr>

                    <th width="180">

                        Review Status

                    </th>

                    <td>

                        @if($assignment->review_status == 'Approved')

    <span class="badge bg-success">
        Approved
    </span>

@elseif($assignment->review_status == 'Changes Requested')

    <span class="badge bg-danger">
        Changes Requested
    </span>

@else

    <span class="badge bg-warning text-dark">
        Pending Review
    </span>

@endif

                    </td>

                </tr>

                <tr>
    <th>Manager Comment</th>
    <td>
        @if($assignment->manager_advice)
            {{ $assignment->manager_advice }}
        @elseif($assignment->review_comment)
            {{ $assignment->review_comment }}
        @else
            No comments available.
        @endif
    </td>
</tr>

                <tr>

                    <th>

                        Reviewed On

                    </th>

                    <td>

                        {{ $assignment->reviewed_at ? \Carbon\Carbon::parse($assignment->reviewed_at)->format('d M Y h:i A') : 'Not Reviewed' }}

                    </td>

                </tr>

            </table>

                        <div class="d-flex justify-content-end mt-4">

                @if($assignment->review_status == 'Approved')

                    <button
                        type="button"
                        class="btn btn-success"
                        disabled>

                        Task Approved

                    </button>

                @else

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Save Update

                    </button>

                @endif

            </div>

        </form>

    </div>

</div>

        </div>

    </div>

</div>


@endsection

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const slider = document.getElementById('progressRange');
    const bar = document.getElementById('progressBar');
    const value = document.getElementById('progressValue');
    

    if (!slider) return;

    function updateProgress() {

        let progress = slider.value;

        bar.style.width = progress + "%";

        value.innerHTML = progress + "%";

    }

    updateProgress();

    slider.addEventListener("input", updateProgress);

});

</script>

@endpush