@extends('layouts.app')

@section('title', $player->exists ? 'Edit Player' : 'Add Player')

@section('content')
    <h1 class="text-2xl font-bold">{{ $player->exists ? 'Edit Player' : 'Add Player' }}</h1>

    <form action="{{ $player->exists ? route('players.update', $player) : route('players.store') }}" method="POST" class="mt-4">
        @csrf
        @if($player->exists)
            @method('PUT')
        @endif

        <div class="form-control">
            <label class="label">Name</label>
            <input type="text" name="name" class="input input-bordered" value="{{ old('name', $player->name) }}" required>
        </div>

        <div class="form-control mt-4">
            <label class="label">Faction</label>
            <select name="faction_id" class="select select-bordered">
                <option value="">None</option>
                @foreach($factions as $faction)
                    <option value="{{ $faction->id }}" {{ $faction->id == old('faction_id', $player->faction_id) ? 'selected' : '' }}>
                        {{ $faction->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-control mt-4">
            <label class="label">Is Spy</label>
            <input type="checkbox" name="is_spy" class="checkbox" {{ old('is_spy', $player->is_spy) ? 'checked' : '' }}>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">{{ $player->exists ? 'Update' : 'Create' }}</button>
            <a href="{{ route('players.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
@endsection
