@extends('layouts.app')

@section('title', 'Player Details')

@section('content')
    <h1 class="text-2xl font-bold">{{ $player->name }}</h1>

    <p><strong>Faction:</strong> {{ $player->faction->name ?? 'None' }}</p>
    <p><strong>Is Spy:</strong> {{ $player->is_spy ? 'Yes' : 'No' }}</p>

    <a href="{{ route('players.index') }}" class="btn btn-outline">Back to List</a>
@endsection
