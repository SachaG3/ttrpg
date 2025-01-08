@extends('layouts.app')

@section('title', 'Add Faction')

@section('content')
    <h1 class="text-2xl font-bold">Add Faction</h1>

    <form action="{{ route('factions.store') }}" method="POST" class="mt-4">
        @csrf

        <div class="form-control">
            <label class="label">Name</label>
            <input type="text" name="name" class="input input-bordered" placeholder="Faction Name" value="{{ old('name') }}" required>
        </div>

        <div class="form-control mt-4">
            <label class="label">Score</label>
            <input type="number" name="score" class="input input-bordered" placeholder="Score" value="{{ old('score', 0) }}">
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('factions.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
@endsection
