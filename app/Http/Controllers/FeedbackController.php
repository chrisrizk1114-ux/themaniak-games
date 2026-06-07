<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public const GAMES = [
        'general' => 'General / Site',
        'galaxy-bowling' => 'Galaxy Bowling',
        'platformer' => 'Sky Runner',
        'tic-tac-toe' => 'Neon Grid',
        'board-game' => 'Snakes & Ladders',
        'chess' => 'Royal Chess',
        'whack-a-mole' => 'Mole Mayhem',
        'four-hundred' => 'Lebanese 400',
        'uno' => 'Cosmic UNO',
    ];

    public const CATEGORIES = [
        'bug' => 'Bug report',
        'game_issue' => 'Game issue',
        'suggestion' => 'Suggestion / idea',
        'compliment' => 'Compliment',
        'other' => 'Other',
    ];

    public function create(): View|RedirectResponse
    {
        if (auth()->user()->isOwner()) {
            return redirect()->route('owner.feedback');
        }

        $user = auth()->user();

        return view('feedback.create', [
            'categories' => self::CATEGORIES,
            'games' => self::GAMES,
            'playerInfo' => $this->playerInfo($user),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->isOwner()) {
            return redirect()->route('owner.feedback');
        }

        $user = auth()->user();

        $data = $request->validate([
            'category' => ['required', 'in:'.implode(',', array_keys(self::CATEGORIES))],
            'game' => ['nullable', 'string', 'max:80'],
            'subject' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'special_details' => ['nullable', 'string', 'max:3000'],
        ]);

        Feedback::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'category' => $data['category'],
            'game' => $data['game'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'special_details' => $data['special_details'] ?? null,
            'player_info' => $this->playerInfo($user),
        ]);

        return redirect()
            ->route('feedback.create')
            ->with('success', 'Thanks! Your feedback was sent to the site owner.');
    }

    private function playerInfo($user): array
    {
        return [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'member_since' => $user->created_at?->toDateTimeString(),
            'friends_count' => $user->friends()->count(),
            'online' => $user->isOnline(),
            'role' => $user->role ?? 'user',
        ];
    }
}
