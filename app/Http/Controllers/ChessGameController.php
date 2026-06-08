<?php

namespace App\Http\Controllers;

use App\Models\ChessGame;
use App\Models\ChessGameMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChessGameController extends Controller
{
    public function pending(): JsonResponse
    {
        $user = auth()->user();

        $incoming = ChessGame::query()
            ->where('status', ChessGame::STATUS_PENDING)
            ->where('black_user_id', $user->id)
            ->with(['whitePlayer', 'blackPlayer'])
            ->latest()
            ->get()
            ->map(fn (ChessGame $game) => $this->inviteSummary($game, $user));

        $outgoing = ChessGame::query()
            ->where('status', ChessGame::STATUS_PENDING)
            ->where('invited_by_user_id', $user->id)
            ->with(['whitePlayer', 'blackPlayer'])
            ->latest()
            ->get()
            ->map(fn (ChessGame $game) => $this->inviteSummary($game, $user));

        $active = ChessGame::query()
            ->where('status', ChessGame::STATUS_ACTIVE)
            ->where(function ($query) use ($user) {
                $query->where('white_user_id', $user->id)
                    ->orWhere('black_user_id', $user->id);
            })
            ->with(['whitePlayer', 'blackPlayer'])
            ->latest('last_activity_at')
            ->get()
            ->map(fn (ChessGame $game) => $this->inviteSummary($game, $user));

        return response()->json([
            'incoming' => $incoming,
            'outgoing' => $outgoing,
            'active' => $active,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'friend_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = auth()->user();
        $friend = User::findOrFail($data['friend_id']);

        if ($friend->id === $user->id) {
            return response()->json(['message' => 'You cannot invite yourself.'], 422);
        }

        if (! $user->isFriendsWith($friend)) {
            return response()->json(['message' => 'You can only invite friends.'], 422);
        }

        $existing = ChessGame::query()
            ->whereIn('status', [ChessGame::STATUS_PENDING, ChessGame::STATUS_ACTIVE])
            ->where(function ($query) use ($user, $friend) {
                $query->where(function ($pair) use ($user, $friend) {
                    $pair->where('white_user_id', $user->id)->where('black_user_id', $friend->id);
                })->orWhere(function ($pair) use ($user, $friend) {
                    $pair->where('white_user_id', $friend->id)->where('black_user_id', $user->id);
                });
            })
            ->latest()
            ->first();

        if ($existing) {
            return response()->json(array_merge(
                $existing->toSyncPayload($user),
                ['play_url' => url('/chess?game='.$existing->token)]
            ));
        }

        $game = ChessGame::create([
            'white_user_id' => $user->id,
            'black_user_id' => $friend->id,
            'invited_by_user_id' => $user->id,
            'status' => ChessGame::STATUS_PENDING,
            'last_activity_at' => now(),
        ]);

        return response()->json(array_merge(
            $game->load(['whitePlayer', 'blackPlayer'])->toSyncPayload($user),
            ['play_url' => url('/chess?game='.$game->token)]
        ), 201);
    }

    public function show(ChessGame $chessGame): JsonResponse
    {
        $this->authorizeGame($chessGame);

        return response()->json($chessGame->load(['whitePlayer', 'blackPlayer'])->toSyncPayload(auth()->user()));
    }

    public function accept(ChessGame $chessGame): JsonResponse
    {
        $user = auth()->user();
        $this->authorizeGame($chessGame);

        if ($chessGame->black_user_id !== $user->id) {
            return response()->json(['message' => 'Only the invited player can accept.'], 403);
        }

        if ($chessGame->status !== ChessGame::STATUS_PENDING) {
            return response()->json(['message' => 'This invite is no longer pending.'], 422);
        }

        $chessGame->update([
            'status' => ChessGame::STATUS_ACTIVE,
            'state' => ChessGame::initialState(),
            'version' => 1,
            'last_activity_at' => now(),
        ]);

        ChessGameMessage::create([
            'chess_game_id' => $chessGame->id,
            'user_id' => $user->id,
            'body' => $user->name.' joined the match.',
            'created_at' => now(),
        ]);

        return response()->json($chessGame->fresh(['whitePlayer', 'blackPlayer'])->toSyncPayload($user));
    }

    public function sync(Request $request, ChessGame $chessGame): JsonResponse
    {
        $this->authorizeGame($chessGame);

        $sinceMessageId = (int) $request->query('since_message', 0);

        $messages = $chessGame->messages()
            ->with('user:id,name')
            ->when($sinceMessageId > 0, fn ($q) => $q->where('id', '>', $sinceMessageId))
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->map(fn (ChessGameMessage $msg) => [
                'id' => $msg->id,
                'user_id' => $msg->user_id,
                'user_name' => $msg->user->name,
                'body' => $msg->body,
                'mine' => $msg->user_id === auth()->id(),
                'created_at' => $msg->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'game' => $chessGame->load(['whitePlayer', 'blackPlayer'])->toSyncPayload(auth()->user()),
            'messages' => $messages,
        ]);
    }

    public function move(Request $request, ChessGame $chessGame): JsonResponse
    {
        $user = auth()->user();
        $this->authorizeGame($chessGame);

        if ($chessGame->status !== ChessGame::STATUS_ACTIVE) {
            return response()->json(['message' => 'Game is not active.'], 422);
        }

        if ($chessGame->game_over) {
            return response()->json(['message' => 'Game is already over.'], 422);
        }

        $data = $request->validate([
            'version' => ['required', 'integer', 'min:0'],
            'state' => ['required', 'array'],
            'game_over' => ['sometimes', 'boolean'],
            'winner' => ['nullable', 'string', 'in:white,black,draw'],
            'game_over_title' => ['nullable', 'string', 'max:120'],
            'game_over_msg' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['version'] !== $chessGame->version) {
            return response()->json([
                'message' => 'Board updated elsewhere. Refreshing…',
                'game' => $chessGame->fresh(['whitePlayer', 'blackPlayer'])->toSyncPayload($user),
            ], 409);
        }

        $myColor = $chessGame->colorForUser($user);
        $serverPlayer = $chessGame->state['currentPlayer'] ?? 'white';

        if ($serverPlayer !== $myColor) {
            return response()->json(['message' => 'It is not your turn.'], 403);
        }

        $newState = $data['state'];
        $nextPlayer = $newState['currentPlayer'] ?? null;

        if ($nextPlayer === $serverPlayer) {
            return response()->json(['message' => 'Invalid move state.'], 422);
        }

        $gameOver = (bool) ($data['game_over'] ?? ($newState['gameOver'] ?? false));

        $chessGame->update([
            'state' => $newState,
            'version' => $chessGame->version + 1,
            'game_over' => $gameOver,
            'winner' => $gameOver ? ($data['winner'] ?? null) : null,
            'game_over_title' => $gameOver ? ($data['game_over_title'] ?? null) : null,
            'game_over_msg' => $gameOver ? ($data['game_over_msg'] ?? null) : null,
            'status' => $gameOver ? ChessGame::STATUS_FINISHED : ChessGame::STATUS_ACTIVE,
            'last_activity_at' => now(),
        ]);

        return response()->json($chessGame->fresh(['whitePlayer', 'blackPlayer'])->toSyncPayload($user));
    }

    public function chat(Request $request, ChessGame $chessGame): JsonResponse
    {
        $user = auth()->user();
        $this->authorizeGame($chessGame);

        if (! in_array($chessGame->status, [ChessGame::STATUS_PENDING, ChessGame::STATUS_ACTIVE], true)) {
            return response()->json(['message' => 'Chat is closed for this game.'], 422);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $message = ChessGameMessage::create([
            'chess_game_id' => $chessGame->id,
            'user_id' => $user->id,
            'body' => trim($data['message']),
            'created_at' => now(),
        ]);

        $chessGame->touch('last_activity_at');

        return response()->json([
            'message' => [
                'id' => $message->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'body' => $message->body,
                'mine' => true,
                'created_at' => $message->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    private function authorizeGame(ChessGame $chessGame): void
    {
        if (! $chessGame->involvesUser(auth()->user())) {
            abort(403);
        }
    }

    private function inviteSummary(ChessGame $game, User $user): array
    {
        return array_merge($game->toSyncPayload($user), [
            'play_url' => url('/chess?game='.$game->token),
        ]);
    }
}
