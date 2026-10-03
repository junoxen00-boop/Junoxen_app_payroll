<?php
namespace Database\Seeders;
use App\Models\Setting;
use Illuminate\Database\Seeder;
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('Development seeders are disabled in production to protect live Hostinger data.');
        }

        Setting::updateOrCreate(['id'=>1], [
            'company_name'=>'Junoxen PVT LTD',
            'company_email'=>'admin@junoxen.com',
            'timezone'=>'Asia/Kolkata',
            'date_format'=>'d-m-Y',
            'sender_name'=>'Junoxen PVT LTD',
            'sender_email'=>'admin@junoxen.com',
            'email_notifications'=>true,
        ]);
    }
}
