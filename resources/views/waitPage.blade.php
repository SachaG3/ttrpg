@extends('layouts.app-main')
@section('title', 'Waiting for the Game to Start')

@section('content')
    <div class="flex items-center justify-center h-screen">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">En attente du début de la partie...</h1>
            <p class="text-lg text-gray-600">Veuillez patienter, le jeu commencera bientôt.</p>
        </div>
    </div>

    <script>
        function checkGameStart() {
            fetch('{{ route("game.checkStart") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.is_start) {
                        window.location.href = '{{ route("game.start") }}'; // Redirection si le jeu a commencé
                    }
                })
                .catch(() => {
                    console.error('Erreur lors de la vérification du statut du jeu.');
                });
        }

        setInterval(checkGameStart, 3000);
    </script>
@endsection
