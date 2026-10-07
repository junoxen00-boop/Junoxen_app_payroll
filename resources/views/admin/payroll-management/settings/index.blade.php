@extends('layouts.admin')

@section('content')
<div class="container-fluid"><div class="row g-4"><div class="col-xl-3 col-lg-4">@include('admin.payroll-management.partials.nav')</div><div class="col-xl-9 col-lg-8">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="card"><div class="card-body"><h1 class="h3 mb-1">Payroll Settings</h1><p class="text-muted mb-4">Administrative payroll defaults only. Secrets and credentials do not belong here.</p><form method="POST" action="{{ route('admin.payroll-management.settings.update') }}" class="row g-3">@csrf @method('PUT')
<div class="col-md-6"><label class="form-label">Company Name</label><input name="company_name" class="form-control" value="{{ old('company_name',$settings->company_name) }}" required></div>
<div class="col-md-3"><label class="form-label">Pay Frequency</label><select name="pay_frequency" class="form-select">@foreach(['Weekly','Fortnightly','Monthly'] as $v)<option @selected($settings->pay_frequency===$v)>{{ $v }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Currency</label><input name="currency" class="form-control" value="{{ old('currency',$settings->currency) }}" required></div>
<div class="col-md-4"><label class="form-label">Default Payment Method</label><select name="default_payment_method" class="form-select">@foreach(['Bank Transfer','Cash','Cheque','Other'] as $v)<option @selected($settings->default_payment_method===$v)>{{ $v }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">LOP Calculation Basis</label><select name="lop_calculation_basis" class="form-select"><option selected>Calendar Days</option></select></div>
<div class="col-md-4"><label class="form-label">Payroll Year</label><input type="number" name="payroll_year" min="2000" max="2100" class="form-control" value="{{ old('payroll_year',$settings->payroll_year) }}"></div>
<div class="col-12"><label class="form-label">Payslip Footer</label><textarea name="payslip_footer" class="form-control" rows="3">{{ old('payslip_footer',$settings->payslip_footer) }}</textarea></div>
<div class="col-12"><button class="btn btn-primary">Save Payroll Settings</button></div></form></div></div>
</div></div></div>
@endsection
