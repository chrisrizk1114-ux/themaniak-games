<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function create(): View
    {
        $user = Auth::user();

        return view('feedback.create', [
            'name' => $user?->name ?? old('name', ''),
            'email' => $user?->email ?? old('email', ''),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $rules = [
            'subject' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];

        if ($user) {
            $rules['name'] = ['nullable', 'string', 'max:255'];
            $rules['email'] = ['nullable', 'email', 'max:255'];
        } else {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'email', 'max:255'];
        }

        $data = $request->validate($rules);

        Feedback::create([
            'user_id' => $user?->id,
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
        ]);

        return redirect()
            ->route('feedback.create')
            ->with('success', 'Thanks! Your feedback was sent to the site owner.');
    }
}
