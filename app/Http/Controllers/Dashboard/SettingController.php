<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\MailSetting;
use App\Models\PaymentSetting;
use App\Models\SmsSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\Response;

class SettingController extends Controller
{
    public function generalSetting()
    {
        $generalSetting = GeneralSetting::first();

        return Inertia::render('dashboard/setting/General', [
            'generalSetting' => $generalSetting,
        ]);
    }

    public function updateGeneral(Request $request): Response|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url|max:255',
            'app_email' => 'required|email|max:255',
            'app_phone' => 'required|string|max:255',
            'app_address' => 'required|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'app_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'app_timezone' => 'required|string|max:255',
        ]);

        $generalSetting = GeneralSetting::first();
        $data = $request->except(['app_logo', 'app_favicon']);

        // Handle logo
        if ($request->hasFile('app_logo')) {
            if ($generalSetting->app_logo) {
                $oldLogo = public_path('uploads/setting_photos/') . $generalSetting->app_logo;
                if (file_exists($oldLogo)) unlink($oldLogo);
            }
            $extension = $request->file('app_logo')->getClientOriginalExtension();
            $filename = 'app_logo.' . $extension;

            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('app_logo'));
            $image->scale(width: 180, height: 180);
            $image->toPng()->save(public_path('uploads/setting_photos/') . $filename);

            $data['app_logo'] = $filename;
        }

        // Handle favicon
        if ($request->hasFile('app_favicon')) {
            if ($generalSetting->app_favicon) {
                $oldFavicon = public_path('uploads/settings_photos/') . $generalSetting->app_favicon;
                if (file_exists($oldFavicon)) unlink($oldFavicon);
            }
            $extension = $request->file('app_favicon')->getClientOriginalExtension();
            $filename = 'app_favicon.' . $extension;

            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('app_favicon'));
            $image->scale(width: 16, height: 16);
            $image->toPng()->save(public_path('uploads/settings_photos/') . $filename);

            $data['app_favicon'] = $filename;
        }

        $generalSetting->update($data);

        return redirect()->route('general.setting')->with('success', 'Settings updated successfully.');
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
