<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('profile.show', [
            'user' => auth()->user(),
        ]);
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ]);

        $user = auth()->user();
        $extension = $data['avatar']->getClientOriginalExtension();
        $path = 'avatars/'.$user->id.'.'.strtolower($extension);

        if ($user->avatar_path && $user->avatar_path !== $path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $data['avatar']->storeAs('avatars', $user->id.'.'.strtolower($extension), 'public');

        $user->update(['avatar_path' => $path]);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile picture updated.');
    }

    public function destroyAvatar(): RedirectResponse
    {
        $user = auth()->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile picture removed.');
    }
}
