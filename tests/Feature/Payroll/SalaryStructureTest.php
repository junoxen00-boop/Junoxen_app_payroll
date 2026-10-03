<?php

use App\Models\EmployeeSalaryStructure;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->admin = User::where('email','admin@junoxen.com')->firstOrFail();
    $this->employee = User::where('email','employee@junoxen.com')->firstOrFail()->employee;
});

test('admin can create salary structure', function () {
    EmployeeSalaryStructure::where('employee_id',$this->employee->id)->update(['status'=>'Inactive']);
    $response = $this->actingAs($this->admin)->post(route('admin.salary-structures.store'), [
        'employee_id'=>$this->employee->id,'basic_salary'=>35000,'hra'=>12000,'conveyance_allowance'=>2000,
        'medical_allowance'=>1500,'special_allowance'=>4000,'other_allowance'=>0,'overtime_rate'=>250,
        'pf_applicable'=>1,'pf_wage_basis'=>35000,'effective_from'=>'2026-10-01','status'=>'Active',
    ]);
    $response->assertRedirect(route('admin.salary-structures.index'));
    $this->assertDatabaseHas('employee_salary_structures', ['employee_id'=>$this->employee->id,'basic_salary'=>35000,'effective_from'=>'2026-10-01']);
});
