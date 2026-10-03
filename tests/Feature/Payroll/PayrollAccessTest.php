<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeSalaryStructure;
use App\Models\Payroll;
use App\Models\Role;
use App\Models\User;
use App\Services\PayrollService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->admin = User::where('email', 'admin@junoxen.com')->firstOrFail();
    $this->manager = User::where('email', 'manager@junoxen.com')->firstOrFail();
    $this->employeeUser = User::where('email', 'employee@junoxen.com')->firstOrFail();
    $this->employee = $this->employeeUser->employee;
});

test('admin can access payroll dashboard', function () {
    $this->actingAs($this->admin)->get(route('admin.payroll.dashboard'))->assertOk();
});

test('manager cannot access payroll administration', function () {
    $this->actingAs($this->manager)->get(route('admin.payroll.dashboard'))->assertForbidden();
});

test('employee cannot create edit or delete payroll', function () {
    $service = app(PayrollService::class);
    $payroll = $service->generate($this->employee, 5, 2026, ['status'=>'Draft'], $this->admin, '127.0.0.1');

    $this->actingAs($this->employeeUser)->get(route('admin.payroll.create'))->assertForbidden();
    $this->actingAs($this->employeeUser)->get(route('admin.payroll.edit',$payroll))->assertForbidden();
    $this->actingAs($this->employeeUser)->delete(route('admin.payroll.destroy',$payroll))->assertForbidden();
});

test('employee can view own payroll but not another employees payroll', function () {
    $service = app(PayrollService::class);
    $own = $service->generate($this->employee, 9, 2026, [], $this->admin, '127.0.0.1');

    $department = Department::firstOrFail();
    $otherUser = User::create([
        'name'=>'Other Employee','email'=>'other@junoxen.test','password'=>Hash::make('Password@123'),
        'role_id'=>Role::where('name','Employee')->value('id'),'must_change_password'=>false,
    ]);
    $otherEmployee = Employee::create([
        'user_id'=>$otherUser->id,'employee_id'=>'JNX002','full_name'=>'Other Employee','email'=>'other@junoxen.test',
        'mobile_number'=>'9000000002','department_id'=>$department->id,'designation'=>'Executive','joining_date'=>'2026-01-01','status'=>'Active',
    ]);
    EmployeeSalaryStructure::create([
        'employee_id'=>$otherEmployee->id,'basic_salary'=>20000,'effective_from'=>'2026-01-01','status'=>'Active','created_by'=>$this->admin->id,'updated_by'=>$this->admin->id,
    ]);
    $otherPayroll = $service->generate($otherEmployee, 9, 2026, [], $this->admin, '127.0.0.1');

    $this->actingAs($this->employeeUser)->get(route('employee.payroll.show',$own))->assertOk();
    $this->actingAs($this->employeeUser)->get(route('employee.payroll.show',$otherPayroll))->assertForbidden();
});

test('unauthenticated payroll request redirects to login', function () {
    $this->get(route('employee.payroll.index'))->assertRedirect('/login');
});
