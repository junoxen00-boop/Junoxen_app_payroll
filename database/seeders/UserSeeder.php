<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('Development seeders are disabled in production to protect live Hostinger data.');
        }

        $accounts = [
            ['name'=>'Junoxen Admin','email'=>'admin@junoxen.com','password'=>'JunoxenAdmin@2026','role'=>'Admin'],
            ['name'=>'Junoxen Manager','email'=>'manager@junoxen.com','password'=>'JunoxenManager@2026','role'=>'Manager'],
            ['name'=>'Junoxen Employee','email'=>'employee@junoxen.com','password'=>'JunoxenEmployee@2026','role'=>'Employee'],
        ];
        foreach ($accounts as $account) {
            User::updateOrCreate(['email'=>$account['email']], [
                'name'=>$account['name'],
                'password'=>Hash::make($account['password']),
                'role_id'=>Role::where('name',$account['role'])->value('id'),
                'must_change_password'=>true,
            ]);
        }
    }
}
