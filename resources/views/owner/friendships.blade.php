@extends('layouts.app')

@section('content')
@include('owner.partials.styles')

<div class="owner-page">
    <header class="owner-header">
        <div class="owner-badge">👑 Owner Panel</div>
        <h1 class="owner-title">Friendships</h1>
        <p class="owner-subtitle">All friend connections and pending requests on The Maniak.</p>
    </header>

    @include('owner.partials.nav')

    @if (session('success'))
        <div class="owner-alert owner-alert--success">{{ session('success') }}</div>
    @endif

    <section class="owner-card">
        <div class="owner-table-wrap">
            <table class="owner-table">
                <thead>
                    <tr>
                        <th>Player A</th>
                        <th>Player B</th>
                        <th>Status</th>
                        <th>Since</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($friendships as $friendship)
                    <tr>
                        <td>{{ $friendship->sender->name }}<br><small style="color:rgba(255,255,255,0.45);">{{ $friendship->sender->email }}</small></td>
                        <td>{{ $friendship->recipient->name }}<br><small style="color:rgba(255,255,255,0.45);">{{ $friendship->recipient->email }}</small></td>
                        <td>
                            @if ($friendship->isAccepted())
                                <span class="owner-role-badge owner-role-badge--user">Accepted</span>
                            @else
                                <span class="owner-role-badge owner-role-badge--owner">Pending</span>
                            @endif
                        </td>
                        <td>{{ $friendship->created_at->diffForHumans() }}</td>
                        <td>
                            <form method="POST" action="{{ route('owner.friendships.destroy', $friendship) }}" onsubmit="return confirm('Remove this friendship?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="owner-btn owner-btn--danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="color:rgba(255,255,255,0.45);">No friendships yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="owner-pagination">
            {{ $friendships->links() }}
        </div>
    </section>
</div>
@endsection
