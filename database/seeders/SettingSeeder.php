<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use App\Models\MailSetting;
use App\Models\PaymentSetting;
use App\Models\SmsSetting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // GeneralSetting
        GeneralSetting::create([
            'app_name' => 'My Website',
            'app_url' => 'http://localhost',
            'app_email' => 'info@gmail.com',
            'app_phone' => '+1234567890',
            'app_address' => '123 Main St, City, Country',
            'app_logo' => 'logo.png',
            'app_favicon' => 'favicon.ico',
            'app_timezone' => 'UTC',
        ]);


        // MailSetting
        MailSetting::create([
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.mailtrap.io',
            'mail_port' => 2525,
            'mail_username' => 'your_username',
            'mail_password' => 'your_password',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreplay@gmail.com',
        ]);

        // SmsSetting
        SmsSetting::create([
            'sms_driver' => 'twilio',
            'sms_api_url' => 'https://api.twilio.com/2010-04-01/Accounts/your_account_sid/Messages.json',
            'sms_api_key' => 'your_api_key',
            'sms_api_secret' => 'your_api_secret',
            'sms_from_number' => '+1234567890',
        ]);

        // PaymentSetting
        PaymentSetting::create([
            'payment_gateway' => 'stripe',
            'payment_api_key' => 'your_api_key',
            'payment_api_secret' => 'your_api_secret',
        ]);
    }
}
