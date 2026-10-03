<?php
namespace Database\Seeders;
use App\Models\Department;
use Illuminate\Database\Seeder;
class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('Development seeders are disabled in production to protect live Hostinger data.');
        }

        foreach ([['name'=>'Marketing','description'=>'Digital marketing and growth'],['name'=>'Operations','description'=>'Business operations'],['name'=>'Human Resources','description'=>'People and culture']] as $department) {
            Department::updateOrCreate(['name'=>$department['name']], $department + ['status'=>true]);
        }
    }
}
