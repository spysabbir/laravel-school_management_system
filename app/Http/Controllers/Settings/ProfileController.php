<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
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
        return Inertia::render('settings/Profile', [
            'currentUser' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Fill the user model with validated data
        $user->fill($request->validated());

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if it exists
            if ($user->avatar) {
                $oldAvatarPath = public_path('uploads/avatars/') . $user->avatar;
                if (file_exists($oldAvatarPath)) {
                    unlink($oldAvatarPath);
                }
            }

            // Generate a unique name for the new avatar
            $avatarName = $user->id . '_avatar.' . $request->file('avatar')->getClientOriginalExtension();

            // Resize and save the new avatar
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('avatar'));
            $image->scale(width: 180, height: 180);
            $image->toPng()->save(public_path('uploads/avatars/') . $avatarName);

            // Update the user's avatar field
            $user->avatar = $avatarName;
        }

        // Save the updated user data
        $user->save();

        // Redirect back with a success message
        return to_route('profile.edit')->with('status', 'Profile updated successfully.');
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
