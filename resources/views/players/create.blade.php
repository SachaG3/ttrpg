@extends('layouts.app-main')

@section('title', 'Add Player')

@section('content')
    <div class="container mx-auto max-w-lg mt-10 p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">Create a New Character</h1>

        <form action="{{ route('players.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- First Name -->
            <div class="form-control">
                <label for="firstname" class="label">
                    <span class="label-text font-medium">First Name</span>
                </label>
                <input type="text" id="firstname" name="firstname" placeholder="Enter first name"
                       class="input input-bordered w-full" required>
                @error('firstname')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nickname -->
            <div class="form-control">
                <label for="nickname" class="label">
                    <span class="label-text font-medium">Nickname</span>
                </label>
                <input type="text" id="nickname" name="nickname" placeholder="Enter nickname"
                       class="input input-bordered w-full" required>
                @error('nickname')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit" class="btn btn-primary w-full py-2">Create Character</button>
            </div>

            <!-- Login Link -->
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-indigo-500 font-medium hover:underline">
                    Already have an account? Log in
                </a>
            </div>
        </form>
    </div>
@endsection
