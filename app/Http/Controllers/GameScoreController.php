<?php

namespace App\Http\Controllers;

use App\Models\GameScore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameScoreController extends Controller
{
    public const GAMES = [
        'whack-a-mole' => ['name' => 'Mole Mayhem', 'unit' => 'pts', 'mode' => 'high'],
        'galaxy-bowling' => ['name' => 'Galaxy Bowling', 'unit' => 'pts', 'mode' => 'high'],
        'platformer' => ['name' => 'Sky Runner', 'unit' => 'm', 'mode' => 'high'],
        'tic-tac-toe' => ['name' => 'Neon Grid', 'unit' => 'wins', 'mode' => 'cumulative'],
        'uno' => ['name' => 'Cosmic UNO', 'unit' => 'wins', 'mode' => 'cumulative'],
    ];

    public function show(string $game): JsonResponse
    {
        $config = $this->gameConfig($game);

        $top = GameScore::query()
            ->where('game', $game)
            ->with('user:id,name')
            ->orderByDesc('score')
            ->orderBy('updated_at')
            ->limit(3)
            ->get()
            ->values()
            ->map(fn (GameScore $row, int $index) => [
                'rank' => $index + 1,
                'name' => $row->user->name,
                'score' => $row->score,
            ]);

        $you = null;
        if (auth()->check()) {
            $mine = GameScore::query()
                ->where('game', $game)
                ->where('user_id', auth()->id())
                ->first();

            if ($mine) {
                $rank = GameScore::query()
                    ->where('game', $game)
                    ->where('score', '>', $mine->score)
                    ->count() + 1;

                $you = [
                    'rank' => $rank,
                    'name' => auth()->user()->name,
                    'score' => $mine->score,
                ];
            } else {
                $you = [
                    'rank' => null,
                    'name' => auth()->user()->name,
                    'score' => 0,
                ];
            }
        }

        return response()->json([
            'game' => $game,
            'name' => $config['name'],
            'unit' => $config['unit'],
            'top' => $top,
            'you' => $you,
            'logged_in' => auth()->check(),
        ]);
    }

    public function store(Request $request, string $game): JsonResponse
    {
        $config = $this->gameConfig($game);

        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:999999'],
        ]);

        $user = $request->user();
        $record = GameScore::query()->firstOrNew([
            'user_id' => $user->id,
            'game' => $game,
        ]);

        if ($config['mode'] === 'cumulative') {
            $record->score = ($record->exists ? $record->score : 0) + 1;
            $record->save();
        } else {
            if (! $record->exists || $validated['score'] > $record->score) {
                $record->score = $validated['score'];
                $record->save();
            }
        }

        return $this->show($game);
    }

    private function gameConfig(string $game): array
    {
        if (! isset(self::GAMES[$game])) {
            abort(404);
        }

        return self::GAMES[$game];
    }
}
