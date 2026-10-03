@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h3 class="fw-bold mb-1">Payslip</h3><p class="text-muted mb-0">{{ $payroll->payroll_number }} · {{ DateTime::createFromFormat('!m',$payroll->payroll_month)->format('F') }} {{ $payroll->payroll_year }}</p></div><a target="_blank" href="{{ route('employee.payroll.print',$payroll) }}" class="btn btn-outline-primary"><i class="bi bi-printer me-1"></i> Print Payslip</a></div>
@include('payroll._payslip-card',['payroll'=>$payroll])
@endsection
