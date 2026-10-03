@extends('layouts.admin')
@section('content')
<div class="mb-4"><h3 class="fw-bold mb-1">Edit Payroll</h3><p class="text-muted mb-0">{{ $payroll->payroll_number }} · {{ $payroll->employee->full_name }} · {{ DateTime::createFromFormat('!m',$payroll->payroll_month)->format('F') }} {{ $payroll->payroll_year }}</p></div>
<div class="card"><div class="card-body p-4"><form method="POST" action="{{ route('admin.payroll.update',$payroll) }}">@csrf @method('PUT') @include('admin.payroll._adjustments')
@if($errors->any())<div class="alert alert-danger mt-3"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="mt-4 d-flex gap-2"><button class="btn btn-primary">Recalculate & Save</button><a href="{{ route('admin.payroll.show',$payroll) }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
