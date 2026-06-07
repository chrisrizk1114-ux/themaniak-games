<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FriendController extends Controller
{
    public function index(): View
    {
        return $this->friendsView(auth()->user());
    }

    public function search(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $query = trim($data['name']);
        $user = auth()->user();

        if (strtolower($query) === strtolower(trim($user->name))) {
            return back()->withErrors(['name' => 'You cannot add yourself as a friend.']);
        }

        $matches = User::query()
            ->where('id', '!=', $user->id)
            ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($query).'%'])
            ->orderBy('name')
            ->limit(20)
            ->get();

        if ($matches->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'No players found matching "'.$query.'".']);
        }

        return $this->friendsView($user, [
            'searchQuery' => $query,
            'searchResults' => $matches,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'friend_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = auth()->user();
        $friend = User::findOrFail($data['friend_id']);

        if ($friend->id === $user->id) {
            return back()->withErrors(['friends' => 'You cannot add yourself as a friend.']);
        }

        if ($user->isFriendsWith($friend)) {
            return back()->withErrors(['friends' => 'You are already friends with '.$friend->name.'.']);
        }

        if ($user->hasPendingWith($friend)) {
            return back()->withErrors(['friends' => 'A friend request is already pending with this player.']);
        }

        Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $friend->id,
            'status' => Friendship::STATUS_PENDING,
        ]);

        return redirect()->route('friends.index')
            ->with('success', 'Friend request sent to '.$friend->name.'!');
    }

    public function accept(Friendship $friendship): RedirectResponse
    {
        $this->authorizeFriendship($friendship);

        if ($friendship->friend_id !== auth()->id()) {
            abort(403);
        }

        if (! $friendship->isPending()) {
            return back()->withErrors(['friends' => 'This request is no longer pending.']);
        }

        $friendship->update(['status' => Friendship::STATUS_ACCEPTED]);

        return back()->with('success', 'You are now friends with '.$friendship->sender->name.'!');
    }

    public function destroy(Friendship $friendship): RedirectResponse
    {
        $this->authorizeFriendship($friendship);

        $userId = auth()->id();
        $wasAccepted = $friendship->isAccepted();
        $wasIncoming = $friendship->isPending() && $friendship->friend_id === $userId;

        $name = $friendship->user_id === $userId
            ? $friendship->recipient->name
            : $friendship->sender->name;

        $friendship->delete();

        if ($wasAccepted) {
            $message = 'Removed '.$name.' from your friends list.';
        } elseif ($wasIncoming) {
            $message = 'Declined friend request from '.$name.'.';
        } else {
            $message = 'Friend request cancelled.';
        }

        return back()->with('success', $message);
    }

    private function authorizeFriendship(Friendship $friendship): void
    {
        $userId = auth()->id();

        if ($friendship->user_id !== $userId && $friendship->friend_id !== $userId) {
            abort(403);
        }
    }

    private function friendsView(User $user, array $extra = []): View
    {
        $acceptedFriendships = Friendship::query()
            ->where('status', Friendship::STATUS_ACCEPTED)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('friend_id', $user->id);
            })
            ->with(['sender', 'recipient'])
            ->latest()
            ->get();

        return view('friends.index', array_merge([
            'acceptedFriendships' => $acceptedFriendships,
            'incoming' => $user->pendingIncoming(),
            'outgoing' => $user->pendingOutgoing(),
            'searchQuery' => null,
            'searchResults' => collect(),
        ], $extra));
    }
}
