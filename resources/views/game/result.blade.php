@extends('layouts.app-main')
@section('title', 'Result')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>

    <div id="waiting" class="p-6 bg-base-200 min-h-screen flex flex-col items-center justify-center">
        <h1 class="text-4xl font-extrabold text-primary mb-6">En attente des résultats...</h1>
        <p class="text-gray-600 mb-8">Veuillez patienter pendant que les autres joueurs effectuent leurs lancers.</p>

        <div class="loader w-16 h-16 border-4 border-gray-300 border-t-primary rounded-full animate-spin"></div>
    </div>

    <div id="dice-display" class="hidden">
        <div id="de4" class="hidden">
            @include('component.de4')
        </div>
        <div id="de6" class="hidden">
            @include('component.de6')
        </div>
        <div id="de20" class="hidden">
            @include('component.de20')
        </div>
    </div>

    <script>
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

        function checkRollStatus() {
            fetch('{{ route("game.checkRollStatus") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.completed) {
                        // Cachez la section "waiting"
                        document.getElementById('waiting').classList.add('hidden');

                        // Affichez la section "dice-display"
                        const diceDisplay = document.getElementById('dice-display');
                        diceDisplay.classList.remove('hidden');

                        // Masquez tous les composants
                        document.getElementById('de4').classList.add('hidden');
                        document.getElementById('de6').classList.add('hidden');
                        document.getElementById('de20').classList.add('hidden');

                        // Affichez le composant correspondant
                        document.getElementById(data.dice_type).classList.remove('hidden');
                    } else {
                        console.log("No roll completed yet.");
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                });
        }

        // Appel répété toutes les 3 secondes
        setInterval(checkRollStatus, 3000);



    </script>

@endsection

