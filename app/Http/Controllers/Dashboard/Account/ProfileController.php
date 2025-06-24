<?php

namespace App\Http\Controllers\Dashboard\Account;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('dashboard/account/Profile');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'blood_group' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
        ]);

        if (!$request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $user->profile_photo ?? 'default_profile_photo.png';
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && $user->profile_photo !== 'default_profile_photo.png') {
                $oldProfilePhoto = public_path('uploads/profile_photos/') . $user->profile_photo;
                if (file_exists($oldProfilePhoto)) {
                    unlink($oldProfilePhoto);
                }
            }

            $extension = $request->file('profile_photo')->getClientOriginalExtension();
            $filename = $user->id . '_profile_photo.' . $extension;

            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('profile_photo'));
            $image->scale(width: 180, height: 180);
            $image->toPng()->save(public_path('uploads/profile_photos/') . $filename);

            $validated['profile_photo'] = $filename;
        }

        $user->fill($validated);
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
