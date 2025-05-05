<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\MailSetting;
use App\Models\PaymentSetting;
use App\Models\SmsSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function generalSetting()
    {
        $generalSetting = GeneralSetting::first();

        return Inertia::render('dashboard/setting/General', [
            'generalSetting' => $generalSetting,
        ]);
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_url' => 'required|url|max:255',
            'site_email' => 'required|email|max:255',
            'site_phone' => 'required|string|max:255',
            'site_address' => 'required|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'site_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'site_timezone' => 'required|string|max:255',
        ]);

        $generalSetting = GeneralSetting::first();
        $data = $request->except(['site_logo', 'site_favicon']);

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $data['site_logo'] = $request->file('site_logo')->store('settings', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('site_favicon')) {
            $data['site_favicon'] = $request->file('site_favicon')->store('settings', 'public');
        }

        $generalSetting->update($data);

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function mailSetting()
    {
        $mailSetting = MailSetting::first();

        return Inertia::render('dashboard/setting/Mail', [
            'mailSetting' => $mailSetting,
        ]);
    }

    public function updateMail(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required|string|max:255',
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string|max:255',
            'mail_password' => 'required|string|max:255',
            'mail_encryption' => 'required|string|max:255',
            'mail_from_address' => 'required|email|max:255',
        ]);

        $mailSetting = MailSetting::first();
        $mailSetting->update($request->all());

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function smsSetting()
    {
        $smsSetting = SmsSetting::first();

        return Inertia::render('dashboard/setting/Sms', [
            'smsSetting' => $smsSetting,
        ]);
    }

    public function updateSms(Request $request)
    {
        $request->validate([
            'sms_provider' => 'required|string|max:255',
            'sms_api_key' => 'required|string|max:255',
            'sms_api_secret' => 'required|string|max:255',
            'sms_from_number' => 'required|string|max:255',
        ]);

        $smsSetting = SmsSetting::first();
        $smsSetting->update($request->all());

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function paymentSetting()
    {
        $paymentSetting = PaymentSetting::first();

        return Inertia::render('dashboard/setting/Payment', [
            'paymentSetting' => $paymentSetting,
        ]);
    }

    public function updatePayment(Request $request)
    {
        $request->validate([
            'payment_gateway' => 'required|string|max:255',
            'payment_api_key' => 'required|string|max:255',
            'payment_api_secret' => 'required|string|max:255',
        ]);

        $paymentSetting = PaymentSetting::first();
        $paymentSetting->update($request->all());

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
