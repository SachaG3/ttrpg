@extends('layouts.app-main')

@section('content')
    <div class="container mx-auto max-w-md mt-10 p-6  rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-center mb-6">Login</h1>
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div class="form-control">
                <label for="nickname" class="label">
                    <span class="label-text font-medium">Nickname</span>
                </label>
                <input type="text" name="nickname" id="nickname" class="input input-bordered w-full" required placeholder="Enter your nickname">
            </div>
            <button type="submit" class="btn btn-primary w-full">Login</button>

        </form>
        <div class="text-center">
        <a class="font-medium  mb-6" href="{{route("players.create")}}">Créer un compte</a>
        </div>
    </div>
@endsection
