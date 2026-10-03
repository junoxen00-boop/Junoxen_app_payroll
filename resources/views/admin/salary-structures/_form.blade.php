@csrf
@if(isset($salaryStructure))
    @method('PUT')
@endif

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Employee *</label>
        <select name="employee_id" class="form-select" required>
            <option value="">Select employee</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" @selected(old('employee_id', $salaryStructure->employee_id ?? request('employee_id')) == $employee->id)>
                    {{ $employee->employee_id }} - {{ $employee->full_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Effective From *</label>
        <input type="date" name="effective_from" class="form-control" required
               value="{{ old('effective_from', isset($salaryStructure) ? $salaryStructure->effective_from?->format('Y-m-d') : now()->startOfMonth()->format('Y-m-d')) }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Effective To</label>
        <input type="date" name="effective_to" class="form-control"
               value="{{ old('effective_to', isset($salaryStructure) ? $salaryStructure->effective_to?->format('Y-m-d') : '') }}">
    </div>

    @php
        $earningFields = [
            'basic_salary' => 'Basic Salary *',
            'hra' => 'HRA',
            'conveyance_allowance' => 'Conveyance Allowance',
            'medical_allowance' => 'Medical Allowance',
            'special_allowance' => 'Special Allowance',
            'other_allowance' => 'Other Allowance',
        ];
    @endphp

    <div class="col-12 mt-4">
        <h5 class="fw-bold mb-1">Recurring Earnings</h5>
        <p class="text-muted small mb-0">These values form the fixed monthly earnings used by payroll.</p>
    </div>

    @foreach($earningFields as $name => $label)
        <div class="col-md-4">
            <label class="form-label">{{ $label }}</label>
            <div class="input-group">
                <span class="input-group-text">₹</span>
                <input type="number" step="0.01" min="0" name="{{ $name }}" class="form-control"
                       value="{{ old($name, $salaryStructure->{$name} ?? 0) }}"
                       {{ $name === 'basic_salary' ? 'required' : '' }}>
            </div>
        </div>
    @endforeach

    <div class="col-12 mt-4">
        <h5 class="fw-bold mb-1">Provident Fund Settings</h5>
        <p class="text-muted small mb-0">
            PF is calculated by the server from the configured statutory rate and wage ceiling. Professional Tax is also calculated automatically and is not entered here.
        </p>
    </div>

    <div class="col-md-4">
        <label class="form-label d-block">PF Applicability</label>
        <input type="hidden" name="pf_applicable" value="0">
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="pf_applicable" value="1" id="pfApplicable"
                   @checked((bool) old('pf_applicable', $salaryStructure->pf_applicable ?? true))>
            <label class="form-check-label" for="pfApplicable">Employee is covered for PF</label>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">PF Wage Basis</label>
        <div class="input-group">
            <span class="input-group-text">₹</span>
            <input type="number" step="0.01" min="0" name="pf_wage_basis" class="form-control"
                   value="{{ old('pf_wage_basis', $salaryStructure->pf_wage_basis ?? '') }}"
                   placeholder="Defaults to Basic Salary">
        </div>
        <div class="form-text">Use the applicable PF wage basis. If blank, Basic Salary is used.</div>
    </div>

    <div class="col-md-4">
        <label class="form-label">Status *</label>
        <select name="status" class="form-select" required>
            <option value="Active" @selected(old('status', $salaryStructure->status ?? 'Active') === 'Active')>Active</option>
            <option value="Inactive" @selected(old('status', $salaryStructure->status ?? '') === 'Inactive')>Inactive</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $salaryStructure->notes ?? '') }}</textarea>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger mt-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="d-flex gap-2 mt-4">
    <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Salary Structure</button>
    <a href="{{ route('admin.salary-structures.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
