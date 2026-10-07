<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSuperannuation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollSuperannuationController extends Controller
{
    /**
     * Display employee superannuation records.
     */
    public function index(): View
    {
        return view(
            'admin.payroll-management.superannuation.index',
            [
                'pageTitle' =>
                    'Superannuation',

                'employees' =>
                    Employee::with(
                        'superannuation'
                    )
                        ->orderBy(
                            'full_name'
                        )
                        ->get(),
            ]
        );
    }


    /**
     * Create or update an employee's
     * superannuation details.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $data =
            $request->validate([
                'employee_id' => [
                    'required',
                    'integer',
                    'exists:employees,id',
                ],

                'fund_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'member_number' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'usi' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'employee_contribution' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:9999999999.99',
                ],

                'employer_contribution' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:9999999999.99',
                ],

                'status' => [
                    'required',
                    'in:Active,Inactive',
                ],
            ]);


        $data['employee_contribution'] =
            $data['employee_contribution']
            ?? 0;


        $data['employer_contribution'] =
            $data['employer_contribution']
            ?? 0;


        EmployeeSuperannuation::updateOrCreate(
            [
                'employee_id' =>
                    $data['employee_id'],
            ],
            $data
        );


        return back()->with(
            'success',
            'Superannuation details saved successfully.'
        );
    }
}