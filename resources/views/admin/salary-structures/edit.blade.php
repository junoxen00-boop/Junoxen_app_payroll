@extends('layouts.admin')
@section('content')
<div class="mb-4"><h3 class="fw-bold mb-1">Edit Salary Structure</h3><p class="text-muted mb-0">Update effective salary components carefully.</p></div>
<div class="card"><div class="card-body p-4"><form method="POST" action="{{ route('admin.salary-structures.update',$salaryStructure) }}">@include('admin.salary-structures._form')</form></div></div>
@endsection
