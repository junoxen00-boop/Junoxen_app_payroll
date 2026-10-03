<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalaryStructureRequest;
use App\Models\Employee;
use App\Models\EmployeeSalaryStructure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class SalaryStructureController extends Controller
{
    public function index(Request $request): View
    {
        $query = EmployeeSalaryStructure::with('employee.department')->latest('effective_from');
        if ($request->filled('employee_id')) $query->where('employee_id', $request->integer('employee_id'));
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        return view('admin.salary-structures.index', [
            'structures' => $query->paginate(15)->withQueryString(),
            'employees' => Employee::orderBy('full_name')->get(),
            'pageTitle' => 'Salary Structures',
        ]);
    }

    public function create(): View
    {
        return view('admin.salary-structures.create', [
            'employees' => Employee::where('status', 'Active')->orderBy('full_name')->get(),
            'pageTitle' => 'Create Salary Structure',
        ]);
    }

    public function store(StoreSalaryStructureRequest $request): RedirectResponse
    {
        $this->ensureNoOverlap($request);
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        EmployeeSalaryStructure::create($data);
        return redirect()->route('admin.salary-structures.index')->with('success', 'Salary structure created successfully.');
    }

    public function edit(EmployeeSalaryStructure $salaryStructure): View
    {
        return view('admin.salary-structures.edit', [
            'salaryStructure' => $salaryStructure,
            'employees' => Employee::orderBy('full_name')->get(),
            'pageTitle' => 'Edit Salary Structure',
        ]);
    }

    public function update(StoreSalaryStructureRequest $request, EmployeeSalaryStructure $salaryStructure): RedirectResponse
    {
        $this->ensureNoOverlap($request, $salaryStructure);
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;
        $salaryStructure->update($data);
        return redirect()->route('admin.salary-structures.index')->with('success', 'Salary structure updated successfully.');
    }

    public function destroy(EmployeeSalaryStructure $salaryStructure): RedirectResponse
    {
        $salaryStructure->delete();
        return back()->with('success', 'Salary structure deleted successfully.');
    }

    private function ensureNoOverlap(StoreSalaryStructureRequest $request, ?EmployeeSalaryStructure $ignore = null): void
    {
        if ($request->input('status') !== 'Active') return;
        $from = $request->date('effective_from');
        $to = $request->filled('effective_to') ? $request->date('effective_to') : null;

        $overlap = EmployeeSalaryStructure::where('employee_id', $request->integer('employee_id'))
            ->where('status', 'Active')
            ->when($ignore, fn ($q) => $q->where('id', '!=', $ignore->id))
            ->where(function ($q) use ($from, $to) {
                $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $from);
            })
            ->when($to, fn ($q) => $q->whereDate('effective_from', '<=', $to), fn ($q) => $q->whereDate('effective_from', '<=', '9999-12-31'))
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages(['effective_from' => 'An active salary structure already overlaps this effective period for the employee.']);
        }
    }
}
