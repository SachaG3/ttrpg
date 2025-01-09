@extends('layouts.app-admin')
@section('title', 'Admin Panel')

@section('content')
    <div id="admin-panel" class="p-6 bg-base-200 min-h-screen">
        <div class="container mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row items-center justify-between mb-8">
                <h1 class="text-4xl font-extrabold text-primary mb-6 md:mb-0">Panneau d'Administration - Héros</h1>
                <div class="flex gap-4">
                    <!-- Stop Game Button -->
                    <button id="stop-game" class="btn btn-error flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Arrêter le Jeu
                    </button>
                </div>
            </div>

            <!-- Heroes Section -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-3xl font-semibold text-secondary mb-6">Les Héros</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($players as $hero)
                        <div class="card bg-base-100 shadow-md hover:shadow-lg transition-shadow duration-300">
                            <div class="card-body flex flex-col items-center">
                                <!-- Hero Avatar -->
                                <div class="mb-4 flex justify-center items-center">
                                    <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-gray-500"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Hero Name -->
                                <h3 class="text-xl font-bold text-center text-white">{{ $hero->name }}</h3>
                                <!-- Hero Stats -->
                                <div class="mt-4 w-full">
                                    <p class="text-sm font-medium text-white">
                                        Force : <span class="text-white font-bold">{{ $hero->strength }}</span>
                                    </p>
                                    <p class="text-sm font-medium text-white">
                                        Dextérité : <span class="text-white font-bold">{{ $hero->dexterity }}</span>
                                    </p>
                                    <p class="text-sm font-medium text-white">
                                        Intelligence : <span class="text-white font-bold">{{ $hero->intelligence }}</span>
                                    </p>
                                    <p class="text-sm font-medium text-white">
                                        Sagesse : <span class="text-white font-bold">{{ $hero->wisdom }}</span>
                                    </p>
                                </div>
                                <!-- Placeholder Button -->
                                <div class="mt-4 w-full">
                                    <button class="btn btn-warning mt-4" onclick="requestDiceRoll({{ $hero->id }}, 20)">
                                        Demander un lancer de dé
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($players->isEmpty())
                    <div class="text-center text-gray-500 mt-6">
                        Aucun héros disponible.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="toast toast-top toast-end hidden">
        <div class="alert alert-info">
            <span id="notification-message">Message</span>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Fonction pour afficher les notifications
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const messageSpan = document.getElementById('notification-message');
            messageSpan.textContent = message;

            notification.classList.remove('alert-info', 'alert-success', 'alert-error', 'alert-warning');
            notification.classList.add(`alert-${type}`);
            notification.classList.remove('hidden');

            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }

        // Gestion du bouton Arrêter le Jeu
        document.getElementById('stop-game').addEventListener('click', function () {
            fetch('{{ route("admin.stopGame") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => showNotification(data.message, 'warning'))
                .catch(() => showNotification('Erreur lors de l\'arrêt du jeu.', 'error'));
        });
        function requestDiceRoll(heroId, diceType) {
            fetch('{{ route("admin.gamede.requestRoll") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    hero_id: heroId,
                    dice_type: diceType,
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Lancer de dé demandé avec succès.');
                    } else {
                        alert('Erreur : ' + data.message);
                    }
                })
                .catch(err => alert('Erreur réseau.'));
        }
    </script>
@endsection
