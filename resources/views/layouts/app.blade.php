<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RPG Manager')</title>
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="container mx-auto p-4">
    <nav class="flex justify-between bg-white shadow-md p-4 rounded">
        <a href="{{ route('players.index') }}" class="btn btn-outline">Players</a>
        <a href="{{ route('factions.index') }}" class="btn btn-outline">Factions</a>
        <a href="{{ route('items.index') }}" class="btn btn-outline">Items</a>
        <a href="{{ route('groups.index') }}" class="btn btn-outline">Groups</a>
        <a href="{{ route('missions.index') }}" class="btn btn-outline">Missions</a>
    </nav>

    <div class="mt-4">
        @yield('content')
    </div>
</div>
</body>
</html>
