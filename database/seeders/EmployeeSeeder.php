<?php
namespace Database\Seeders;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('Development seeders are disabled in production to protect live Hostinger data.');
        }

        $user = User::where('email','employee@junoxen.com')->firstOrFail();
        $department = Department::where('name','Marketing')->firstOrFail();
        Employee::updateOrCreate(['email'=>'employee@junoxen.com'], [
            'user_id'=>$user->id,'employee_id'=>'JNX001','full_name'=>'Junoxen Employee','mobile_number'=>'9000000001',
            'department_id'=>$department->id,'designation'=>'Digital Marketing Executive','joining_date'=>'2026-01-05','status'=>'Active',
        ]);
    }
}
