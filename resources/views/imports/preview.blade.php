@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Import Preview</h2>
            <p class="text-muted mb-0">
                Review tasks before importing.
            </p>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <div class="row">

                <div class="col-md-6">
                    <strong>Employee</strong><br>
                    {{ $employee->full_name }}
                </div>

                <div class="col-md-6">
                    <strong>Month</strong><br>
                    {{ $month }}
                </div>

            </div>

        </div>
    </div>

    <div class="card shadow-sm border-0">

        <div class="card-header">
            <strong>Tasks Found</strong>
        </div>

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0">

                <thead class="table-light">

                   <tr>
    <th width="180">Section</th>
    <th>Task</th>
    <th width="300">Note</th>
</tr>

                </thead>

              <tbody>

@php
    $currentSection = 'General';
@endphp

@foreach($rows as $row)

    @php
        $task = trim($row[0] ?? '');
        $note = trim($row[1] ?? '');
    @endphp

    @if($task == '')
        @continue
    @endif

    @if(strtolower($task) == 'task')
        @continue
    @endif

    @if(in_array(strtoupper($task), ['DAILY','WEEKLY','MONTHLY','YEARLY']))

        @php
            $currentSection = ucfirst(strtolower($task));
        @endphp

        @continue

    @endif

    <tr>

        <td>{{ $currentSection }}</td>

        <td>{{ $task }}</td>

        <td>{{ $note }}</td>

    </tr>

@endforeach

</tbody>

            </table>

        </div>

    </div>

    <div class="mt-4">

    <form action="{{ route('imports.import') }}" method="POST">

        @csrf

        <input type="hidden"
               name="employee_id"
               value="{{ $employee->id }}">

        <input type="hidden"
               name="month"
               value="{{ $month }}">

        @foreach($rows as $row)

            <input
                type="hidden"
                name="rows[]"
                value="{{ json_encode($row) }}">

        @endforeach

        <button
            type="submit"
            class="btn btn-success btn-lg">

            Import Tasks

        </button>

    </form>

</div>

</div>

@endsection