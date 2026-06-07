@extends('layouts.app')

@section('content')
<div class="py-8">
    <h1 class="text-4xl font-bold mb-8 text-center bg-gradient-to-r from-pink-500 via-red-500 to-yellow-500 bg-clip-text text-transparent">
        All Games
    </h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ url('/platformer') }}" class="group rounded-xl bg-gray-800 p-6 border border-gray-700 hover:border-pink-500 transition-all duration-300 hover:shadow-[0_0_30px_rgba(233,69,96,0.3)]">
            <h2 class="text-2xl font-semibold mb-2 text-amber-300">🎮 Platformer</h2>
            <p class="text-gray-300">Jump, collect coins, and explore!</p>
        </a>
        <a href="{{ url('/tic-tac-toe') }}" class="group rounded-xl bg-gray-800 p-6 border border-gray-700 hover:border-pink-500 transition-all duration-300 hover:shadow-[0_0_30px_rgba(233,69,96,0.3)]">
            <h2 class="text-2xl font-semibold mb-2 text-amber-300">⭕ Tic Tac Toe</h2>
            <p class="text-gray-300">Classic 3x3 game!</p>
        </a>
        <a href="{{ url('/board-game') }}" class="group rounded-xl bg-gray-800 p-6 border border-gray-700 hover:border-pink-500 transition-all duration-300 hover:shadow-[0_0_30px_rgba(233,69,96,0.3)]">
            <h2 class="text-2xl font-semibold mb-2 text-amber-300">🎲 Board Game</h2>
            <p class="text-gray-300">Roll the dice and play!</p>
        </a>
        <a href="{{ url('/chess') }}" class="group rounded-xl bg-gray-800 p-6 border border-gray-700 hover:border-pink-500 transition-all duration-300 hover:shadow-[0_0_30px_rgba(233,69,96,0.3)]">
            <h2 class="text-2xl font-semibold mb-2 text-amber-300">♟️ Chess</h2>
            <p class="text-gray-300">Strategic board game!</p>
        </a>
        <a href="{{ url('/whack-a-mole') }}" class="group rounded-xl bg-gray-800 p-6 border border-gray-700 hover:border-pink-500 transition-all duration-300 hover:shadow-[0_0_30px_rgba(233,69,96,0.3)]">
            <h2 class="text-2xl font-semibold mb-2 text-amber-300">🔨 Whack-a-Mole</h2>
            <p class="text-gray-300">Whack those moles fast!</p>
        </a>
    </div>
</div>
@endsection