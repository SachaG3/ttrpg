@extends('layouts.app')

@section('title', 'Players')

@section('content')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Players</h1>
        <a href="{{ route('players.create') }}" class="btn btn-primary">Add Player</a>
    </div>

    <div class="mt-4">
        <table class="table w-full">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Faction</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($players as $player)
                <tr>
                    <td>{{ $player->id }}</td>
                    <td>{{ $player->name }}</td>
                    <td>{{ $player->faction->name ?? 'None' }}</td>
                    <td>
                        <a href="{{ route('players.show', $player) }}" class="btn btn-sm">View</a>
                        <a href="{{ route('players.edit', $player) }}" class="btn btn-sm">Edit</a>
                        <form action="{{ route('players.destroy', $player) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-error">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
