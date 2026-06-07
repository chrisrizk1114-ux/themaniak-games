@extends('layouts.app')

@section('content')
@include('owner.partials.styles')

<div class="owner-page">
    <header class="owner-header">
        <div class="owner-badge">👑 Owner Panel</div>
        <h1 class="owner-title">Users</h1>
        <p class="owner-subtitle">Search, promote, demote, or remove player accounts.</p>
    </header>

    @include('owner.partials.nav')

    @if (session('success'))
        <div class="owner-alert owner-alert--success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="owner-alert owner-alert--error">{{ $errors->first() }}</div>
    @endif

    <section class="owner-card">
        <form class="owner-search" method="GET" action="{{ route('owner.users') }}">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search by name or email…">
            <button type="submit">Search</button>
        </form>

        <div class="owner-table-wrap">
            <table class="owner-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="owner-role-badge owner-role-badge--{{ $user->isOwner() ? 'owner' : 'user' }}">
                                {{ $user->isOwner() ? 'Owner' : 'User' }}
                            </span>
                        </td>
                        <td>{{ $user->isOnline() ? '🟢 Online' : '⚫ Offline' }}</td>
                        <td>{{ $user->created_at->format('M j, Y') }}</td>
                        <td>
                            <div class="owner-actions">
                                @if ($user->isOwner())
                                    @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('owner.users.role', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="role" value="user">
                                        <button type="submit" class="owner-btn owner-btn--cyan">Demote</button>
                                    </form>
                                    @endif
                                @else
                                    <form method="POST" action="{{ route('owner.users.role', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="role" value="owner">
                                        <button type="submit" class="owner-btn owner-btn--gold">Make owner</button>
                                    </form>
                                @endif

                                @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('owner.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="owner-btn owner-btn--danger">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="color:rgba(255,255,255,0.45);">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="owner-pagination">
            {{ $users->links() }}
        </div>
    </section>
</div>
@endsection
