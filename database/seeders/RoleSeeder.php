<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('Development seeders are disabled in production to protect live Hostinger data.');
        }

        Role::updateOrCreate(
            ['name' => 'Admin'],
            ['description' => 'System Administrator']
        );

        Role::updateOrCreate(
            ['name' => 'Manager'],
            ['description' => 'Department Manager']
        );

        Role::updateOrCreate(
            ['name' => 'Employee'],
            ['description' => 'Regular Employee']
        );
    }
}