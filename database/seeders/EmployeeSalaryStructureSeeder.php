<?php
namespace Database\Seeders;
use App\Models\Employee;
use App\Models\EmployeeSalaryStructure;
use App\Models\User;
use Illuminate\Database\Seeder;
class EmployeeSalaryStructureSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('Development seeders are disabled in production to protect live Hostinger data.');
        }

        $employee = Employee::where('employee_id','JNX001')->firstOrFail();
        $admin = User::where('email','admin@junoxen.com')->firstOrFail();
        EmployeeSalaryStructure::updateOrCreate(
            ['employee_id'=>$employee->id,'effective_from'=>'2026-01-01'],
            ['basic_salary'=>30000,'hra'=>10000,'conveyance_allowance'=>2000,'medical_allowance'=>1500,'special_allowance'=>5000,
             'other_allowance'=>0,'overtime_rate'=>250,'pf_applicable'=>true,'pf_wage_basis'=>30000,'pf_deduction'=>0,'esi_deduction'=>0,'professional_tax'=>0,'tds'=>0,
             'loan_deduction'=>0,'other_deduction'=>0,'effective_to'=>null,'status'=>'Active','notes'=>'Seeded development salary structure',
             'created_by'=>$admin->id,'updated_by'=>$admin->id]
        );
    }
}
