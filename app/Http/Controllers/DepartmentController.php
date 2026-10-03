<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments with search and pagination.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $departments = Department::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('departments.index', compact('departments', 'search'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): View
    {
        return view('departments.create');
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateDepartment($request);

        $department = new Department();
        $department->name = $validated['name'];
        $department->description = $validated['description'] ?? null;
        $department->save();

        return Redirect::route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Redirect show page to edit page because separate show page is not required.
     */
    public function show(Department $department): RedirectResponse
    {
        return Redirect::route('departments.edit', $department);
    }

    /**
     * Show the form for editing a department.
     */
    public function edit(Department $department): View
    {
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the selected department.
     */
    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $this->validateDepartment($request, $department);

        $department->name = $validated['name'];
        $department->description = $validated['description'] ?? null;
        $department->save();

        return Redirect::route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Delete the selected department.
     */
    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();

        return Redirect::route('departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Validate department form data.
     */
    private function validateDepartment(Request $request, ?Department $department = null): array
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
            'description' => $request->filled('description')
                ? trim((string) $request->input('description'))
                : null,
        ]);

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('departments', 'name')->ignore($department?->id),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'name.required' => 'Department name is required.',
            'name.unique' => 'This department already exists.',
            'name.max' => 'Department name must not be more than 100 characters.',
            'description.max' => 'Description must not be more than 1000 characters.',
        ]);
    }
}