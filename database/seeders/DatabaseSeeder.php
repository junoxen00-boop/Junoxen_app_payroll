<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('Development seeders are disabled in production to protect live Hostinger data.');
        }

        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
            EmployeeSeeder::class,
            EmployeeSalaryStructureSeeder::class,
        ]);
    }
}
