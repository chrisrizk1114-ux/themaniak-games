<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerController extends Controller
{
    public function index(): View
    {
        $onlineMinutes = 5;

        $stats = [
            'users' => User::query()->count(),
            'owners' => User::query()->where('role', User::ROLE_OWNER)->count(),
            'online' => User::query()
                ->where('last_seen_at', '>=', now()->subMinutes($onlineMinutes))
                ->count(),
            'friendships' => Friendship::query()->where('status', Friendship::STATUS_ACCEPTED)->count(),
            'pending_requests' => Friendship::query()->where('status', Friendship::STATUS_PENDING)->count(),
            'registered_today' => User::query()->whereDate('created_at', today())->count(),
        ];

        $recentUsers = User::query()
            ->latest()
            ->limit(8)
            ->get();

        return view('owner.index', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'onlineMinutes' => $onlineMinutes,
        ]);
    }

    public function users(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('owner.users', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:user,owner'],
        ]);

        if ($user->id === auth()->id() && $data['role'] !== User::ROLE_OWNER) {
            return back()->withErrors(['role' => 'You cannot remove your own owner access.']);
        }

        if ($user->isOwner() && $data['role'] === User::ROLE_USER) {
            $ownerCount = User::query()->where('role', User::ROLE_OWNER)->count();
            if ($ownerCount <= 1) {
                return back()->withErrors(['role' => 'At least one owner account must remain.']);
            }
        }

        $user->update(['role' => $data['role']]);

        $label = $data['role'] === User::ROLE_OWNER ? 'promoted to owner' : 'set as regular user';

        return back()->with('success', "{$user->name} was {$label}.");
    }

    public function deleteUser(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account from the owner panel.']);
        }

        if ($user->isOwner()) {
            $ownerCount = User::query()->where('role', User::ROLE_OWNER)->count();
            if ($ownerCount <= 1) {
                return back()->withErrors(['user' => 'Cannot delete the last owner account.']);
            }
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Deleted user {$name}.");
    }

    public function friendships(): View
    {
        $friendships = Friendship::query()
            ->with(['sender', 'recipient'])
            ->latest()
            ->paginate(25);

        return view('owner.friendships', [
            'friendships' => $friendships,
        ]);
    }

    public function deleteFriendship(Friendship $friendship): RedirectResponse
    {
        $friendship->delete();

        return back()->with('success', 'Friendship removed.');
    }
}
