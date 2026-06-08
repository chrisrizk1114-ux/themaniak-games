<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChessGameController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FriendChatController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PresenceController;
use App\Models\ChessGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/db-ping', function () {
    try {
        DB::select('SELECT 1 AS ok');

        return response()->json(['database' => 'connected']);
    } catch (\Throwable $e) {
        return response()->json(['database' => 'unavailable'], 503);
    }
});

Route::get('/reset-session', function (Request $request) {
    if ($request->hasSession()) {
        $request->session()->flush();
    }

    $cookieName = config('session.cookie', 'laravel_session');
    $domain = config('session.domain');

    return redirect('/')
        ->withCookie(cookie()->forget($cookieName, '/', $domain))
        ->withCookie(cookie()->forget('XSRF-TOKEN', '/', $domain))
        ->with('status', 'Session cleared. You can log in again.');
})->name('reset-session');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store')->middleware('throttle:6,1');

    Route::get('/friends', [FriendController::class, 'index'])->name('friends.index');
    Route::post('/friends/search', [FriendController::class, 'search'])->name('friends.search');
    Route::post('/friends', [FriendController::class, 'store'])->name('friends.store');
    Route::patch('/friends/{friendship}', [FriendController::class, 'accept'])->name('friends.accept');
    Route::delete('/friends/{friendship}', [FriendController::class, 'destroy'])->name('friends.destroy');
    Route::post('/presence/ping', [PresenceController::class, 'ping'])->name('presence.ping');
    Route::get('/presence/friends', [PresenceController::class, 'friends'])->name('presence.friends');

    Route::get('/chat', [FriendChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/poll', [FriendChatController::class, 'poll'])->name('chat.poll');
    Route::get('/chat/messages/{friend}', [FriendChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/messages/{friend}', [FriendChatController::class, 'store'])->name('chat.store');

    Route::prefix('chess')->name('chess.')->group(function () {
        Route::get('/games/pending', [ChessGameController::class, 'pending'])->name('games.pending');
        Route::get('/games/invites/check', [ChessGameController::class, 'inviteCheck'])->name('games.invites.check');
        Route::post('/rooms', [ChessGameController::class, 'createRoom'])->name('rooms.store');
        Route::post('/rooms/join', [ChessGameController::class, 'joinRoom'])->name('rooms.join');
        Route::get('/games/{chessGame:token}', [ChessGameController::class, 'show'])->name('games.show');
        Route::get('/games/{chessGame:token}/sync', [ChessGameController::class, 'sync'])->name('games.sync');
        Route::post('/games/{chessGame:token}/accept', [ChessGameController::class, 'accept'])->name('games.accept');
        Route::post('/games/{chessGame:token}/move', [ChessGameController::class, 'move'])->name('games.move');
        Route::post('/games/{chessGame:token}/chat', [ChessGameController::class, 'chat'])->name('games.chat');
    });
});

Route::middleware(['auth', 'owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/', [OwnerController::class, 'index'])->name('index');
    Route::get('/users', [OwnerController::class, 'users'])->name('users');
    Route::patch('/users/{user}/role', [OwnerController::class, 'updateUserRole'])->name('users.role');
    Route::delete('/users/{user}', [OwnerController::class, 'deleteUser'])->name('users.destroy');
    Route::get('/friendships', [OwnerController::class, 'friendships'])->name('friendships');
    Route::delete('/friendships/{friendship}', [OwnerController::class, 'deleteFriendship'])->name('friendships.destroy');
    Route::get('/feedback/check', [OwnerController::class, 'checkFeedback'])->name('feedback.check');
    Route::get('/feedback', [OwnerController::class, 'feedbacks'])->name('feedback');
    Route::patch('/feedback/read-all', [OwnerController::class, 'markAllFeedbackRead'])->name('feedback.read-all');
    Route::patch('/feedback/{feedback}/read', [OwnerController::class, 'markFeedbackRead'])->name('feedback.read');
    Route::delete('/feedback/{feedback}', [OwnerController::class, 'deleteFeedback'])->name('feedback.destroy');
});

Route::get('/four-hundred', function () {
    return view('four-hundred');
});

Route::get('/400', function () {
    return redirect('/four-hundred');
});

Route::get('/galaxy-bowling', function () {
    return view('galaxy-bowling');
});

Route::get('/game', function () {
    return redirect('/');
});

Route::get('/platformer', function () {
    return view('platformer');
});

Route::get('/tic-tac-toe', function () {
    return view('tic-tac-toe');
});

Route::get('/board-game', function () {
    return view('board-game');
});

Route::get('/whack-a-mole', function () {
    return view('whack-a-mole');
});

Route::get('/chess', function () {
    $user = auth()->user();
    $friends = $user
        ? $user->friends()->map(fn ($friend) => [
            'id' => $friend->id,
            'name' => $friend->name,
            'online' => $friend->isOnline(),
        ])->values()
        : collect();

    $incomingChessInvites = collect();
    $outgoingChessInvites = collect();
    $openChessRooms = collect();
    if ($user) {
        $incomingChessInvites = ChessGame::query()
            ->where('status', ChessGame::STATUS_PENDING)
            ->where('black_user_id', $user->id)
            ->with('whitePlayer')
            ->latest()
            ->get();
        $outgoingChessInvites = ChessGame::query()
            ->where('status', ChessGame::STATUS_PENDING)
            ->where('invited_by_user_id', $user->id)
            ->whereNotNull('black_user_id')
            ->with('blackPlayer')
            ->latest()
            ->get();
        $openChessRooms = ChessGame::query()
            ->where('status', ChessGame::STATUS_PENDING)
            ->where('white_user_id', $user->id)
            ->whereNull('black_user_id')
            ->whereNotNull('room_code')
            ->latest()
            ->get();
    }

    return view('chess', [
        'friends' => $friends,
        'chessUserId' => $user?->id,
        'chessUserName' => $user?->name,
        'incomingChessInvites' => $incomingChessInvites,
        'outgoingChessInvites' => $outgoingChessInvites,
        'openChessRooms' => $openChessRooms,
    ]);
});

Route::get('/uno', function () {
    return view('uno');
});

Route::get('/bowling', function () {
    return redirect('/galaxy-bowling');
});
