@extends('layouts.app-admin')
@section('title', 'Admin Panel')

@section('content')
    <div id="admin-panel" class="p-6 bg-base-200 min-h-screen">
        <div class="container mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row items-center justify-between mb-8">
                <h1 class="text-4xl font-extrabold text-primary mb-6 md:mb-0">Panneau d'Administration</h1>
                <div class="flex flex-wrap gap-4">
                    <!-- Start Game Button -->
                    <button id="start-game" class="btn btn-success flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.868v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Démarrer le Jeu
                    </button>

                    <!-- Stop Game Button -->
                    <button id="stop-game" class="btn btn-error flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Arrêter le Jeu
                    </button>

                    <!-- Create Heroes Button -->
                    <button id="create-heroes" class="btn btn-primary flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4" />
                        </svg>
                        Créer des Héros
                    </button>
                    <button id="melanger" class="btn btn-primary flex items-center">
                        distribuer les missions
                    </button>
                </div>
            </div>

            <!-- Players Grid -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-3xl font-semibold text-secondary mb-6">Liste des Joueurs</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
                    @foreach($players as $player)
                        <div class="card bg-base-100 shadow-md hover:shadow-lg transition-shadow duration-300">
                            <div class="card-body flex flex-col items-center">
                                <!-- Avatar Centré et Agrandi -->
                                <div class="mb-4 flex justify-center items-center">
                                    <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center">
                                        <!-- SVG Agrandi et Centré -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Nom du Joueur -->
                                <h3 class="text-xl font-bold text-center">{{ $player->name }}</h3>
                                <!-- Statut du Joueur -->
                                <div class="mt-2">
                                    @if($player->is_finish)
                                        <span class="badge badge-success text-white">Terminé</span>
                                    @else
                                        <span class="badge badge-warning text-white">En Cours</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @if($players->isEmpty())
                        <div class="col-span-full text-center text-gray-500">
                            Aucun joueur disponible.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notifications (Optional) -->
            <div id="notification" class="toast toast-top toast-end hidden">
                <div class="alert alert-info">
                    <span id="notification-message">Message</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Fonction pour afficher les notifications
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const messageSpan = document.getElementById('notification-message');
            messageSpan.textContent = message;

            // Modifier le type de notification si nécessaire
            notification.classList.remove('alert-info', 'alert-success', 'alert-error', 'alert-warning');
            switch(type) {
                case 'success':
                    notification.classList.add('alert-success');
                    break;
                case 'error':
                    notification.classList.add('alert-error');
                    break;
                case 'warning':
                    notification.classList.add('alert-warning');
                    break;
                default:
                    notification.classList.add('alert-info');
            }

            notification.classList.remove('hidden');
            // Masquer la notification après 3 secondes
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }

        // Gestion des boutons
        document.getElementById('start-game').addEventListener('click', function () {
            fetch('{{ route("admin.startGame") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => showNotification(data.message, 'success'))
                .catch(() => showNotification('Erreur lors du démarrage du jeu.', 'error'));
        });
        document.getElementById('melanger').addEventListener('click', function () {
            fetch('{{ route("admin.rendomize") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => showNotification(data.message, 'success'))
                .catch(() => showNotification('Erreur lors du démarrage du jeu.', 'error'));
        });

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

        document.getElementById('create-heroes').addEventListener('click', function () {
            fetch('{{ route("admin.createHeroes") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Héros créés avec succès!', 'success');
                        window.location.href = '{{ route("admin.panel.hero") }}';
                    } else {
                        showNotification(data.message || 'Erreur inconnue.', 'error');
                    }
                })
                .catch(() => {
                    showNotification('Erreur lors de la création des héros.', 'error');
                });
        });

        function refreshPlayers() {
            fetch('{{ route("admin.panel") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
                .then(response => response.json())
                .then(data => {
                    // Mettre à jour la liste des joueurs
                    const playersGrid = document.querySelector('.grid');
                    playersGrid.innerHTML = '';

                    data.players.forEach(player => {
                        const playerCard = document.createElement('div');
                        playerCard.classList.add(
                            'card',
                            'bg-base-100',
                            'shadow-md',
                            'hover:shadow-lg',
                            'transition-shadow',
                            'duration-300'
                        );

                        playerCard.innerHTML = `
                        <div class="card-body flex flex-col items-center">
                            <div class="mb-4 flex justify-center items-center">
                                <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-center">${player.name}</h3>
                            <div class="mt-2">
                                <span class="badge ${
                            player.is_finish
                                ? 'badge-success text-white'
                                : 'badge-warning text-white'
                        }">
                                    ${
                            player.is_finish
                                ? 'Terminé'
                                : 'En Cours'
                        }
                                </span>
                            </div>
                        </div>
                    `;

                        playersGrid.appendChild(playerCard);
                    });

                    // Afficher un message si aucun joueur
                    if (data.players.length === 0) {
                        const noPlayersMessage = document.createElement('div');
                        noPlayersMessage.classList.add(
                            'col-span-full',
                            'text-center',
                            'text-gray-500'
                        );
                        noPlayersMessage.textContent = 'Aucun joueur disponible.';
                        playersGrid.appendChild(noPlayersMessage);
                    }
                })
                .catch(() => showNotification('Erreur lors de l\'actualisation des joueurs.', 'error'));
        }

        // Actualisation automatique toutes les 3 secondes
        setInterval(refreshPlayers, 3000);
    </script>
@endsection
