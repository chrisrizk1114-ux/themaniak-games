<?php

namespace App\Http\Controllers;

use App\Models\FriendMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FriendChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $friends = $user->friends()->map(fn (User $friend) => [
            'id' => $friend->id,
            'name' => $friend->name,
            'avatar_url' => $friend->avatarUrl(),
            'initial' => $friend->avatarInitial(),
            'online' => $friend->isOnline(),
            'unread' => FriendMessage::query()
                ->where('sender_id', $friend->id)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->count(),
        ])->sortByDesc('online')->values();

        $activeFriendId = (int) $request->query('friend', $friends->first()['id'] ?? 0);
        $activeFriend = $friends->firstWhere('id', $activeFriendId);

        return view('chat.index', [
            'friends' => $friends,
            'activeFriendId' => $activeFriend ? $activeFriendId : null,
            'activeFriend' => $activeFriend,
        ]);
    }

    public function messages(User $friend): JsonResponse
    {
        $user = auth()->user();

        if (! $user->isFriendsWith($friend)) {
            return response()->json(['message' => 'You can only chat with friends.'], 403);
        }

        FriendMessage::query()
            ->where('sender_id', $friend->id)
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = FriendMessage::query()
            ->where(function ($query) use ($user, $friend) {
                $query->where(function ($pair) use ($user, $friend) {
                    $pair->where('sender_id', $user->id)->where('recipient_id', $friend->id);
                })->orWhere(function ($pair) use ($user, $friend) {
                    $pair->where('sender_id', $friend->id)->where('recipient_id', $user->id);
                });
            })
            ->orderBy('id')
            ->limit(200)
            ->get()
            ->map(fn (FriendMessage $msg) => [
                'id' => $msg->id,
                'body' => $msg->body,
                'mine' => $msg->sender_id === $user->id,
                'user_name' => $msg->sender_id === $user->id ? $user->name : $friend->name,
                'created_at' => $msg->created_at?->toIso8601String(),
            ]);

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request, User $friend): JsonResponse
    {
        $user = auth()->user();

        if (! $user->isFriendsWith($friend)) {
            return response()->json(['message' => 'You can only chat with friends.'], 403);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = FriendMessage::create([
            'sender_id' => $user->id,
            'recipient_id' => $friend->id,
            'body' => trim($data['message']),
        ]);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'body' => $message->body,
                'mine' => true,
                'user_name' => $user->name,
                'created_at' => $message->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    public function poll(Request $request): JsonResponse
    {
        $user = auth()->user();
        $sinceId = (int) $request->query('since', 0);
        $friendId = (int) $request->query('friend', 0);

        $unreadTotal = FriendMessage::query()
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $payload = [
            'unread_total' => $unreadTotal,
            'messages' => [],
        ];

        if ($friendId > 0) {
            $friend = User::find($friendId);
            if ($friend && $user->isFriendsWith($friend)) {
                $payload['messages'] = FriendMessage::query()
                    ->where('id', '>', $sinceId)
                    ->where(function ($query) use ($user, $friend) {
                        $query->where(function ($pair) use ($user, $friend) {
                            $pair->where('sender_id', $user->id)->where('recipient_id', $friend->id);
                        })->orWhere(function ($pair) use ($user, $friend) {
                            $pair->where('sender_id', $friend->id)->where('recipient_id', $user->id);
                        });
                    })
                    ->orderBy('id')
                    ->limit(50)
                    ->get()
                    ->map(fn (FriendMessage $msg) => [
                        'id' => $msg->id,
                        'body' => $msg->body,
                        'mine' => $msg->sender_id === $user->id,
                        'user_name' => $msg->sender_id === $user->id ? $user->name : $friend->name,
                        'created_at' => $msg->created_at?->toIso8601String(),
                    ]);

                FriendMessage::query()
                    ->where('sender_id', $friend->id)
                    ->where('recipient_id', $user->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }
        }

        return response()->json($payload);
    }
}
