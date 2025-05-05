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
        return Inertia::render('dashboard/account/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'date_of_birth' => ['nullable', 'date'],
            'blood_group' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'religion' => ['nullable', 'string', 'in:Islam,Hinduism,Christianity,Buddhism,Other'],
            'marital_status' => ['nullable', 'string', 'in:Single,Married,Divorced,Widowed,Separated,Other'],
            'phone' => ['nullable', 'string', 'max:20'],
            'present_address' => ['nullable', 'string', 'max:500'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->has('email') && $user->email !== $validated['email']) {
            $validated['email_verified_at'] = null;
        }

        if ($request->hasFile('profile_photo')) {
            // Delete old profile photo if exists
            if ($user->profile_photo) {
                $oldProfilePhoto = public_path('uploads/profile_photos/') . $user->profile_photo;
                if (file_exists($oldProfilePhoto)) {
                    unlink($oldProfilePhoto);
                }
            }

            // Process new profile photo
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

    /**
     * Delete the user's profile.
     */
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
