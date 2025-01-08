<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<div x-data="{ open: false, selectedMission: null }" class="w-full">
    <h2 class="text-xl font-bold text-center text-gray-100 mb-4 cursor-pointer" @click="open = true; selectedMission = {{ json_encode(['title' => $mission->title, 'choices' => $mission->choices]) }}">
        @php
            $title = json_decode($mission->title, true);
        @endphp
        @if(is_array($title) && isset($title['en']))
            {{ $title['en'] }}
        @else
            Mission title not defined
        @endif
    </h2>

    <!-- Tableau des choix -->
    <div class="grid grid-cols-2 gap-4 p-4 border-2 border-dotted border-gray-700 bg-opacity-50 bg-black rounded-lg w-full">
        @foreach($mission->choices as $choice)
            @php
                $optionText = json_decode($choice->option_text, true);
            @endphp
            <div
                class="relative w-full h-16 border-2 border-dashed border-gray-500 bg-transparent flex justify-center items-center cursor-pointer hover:border-green-500"
                @if(is_array($optionText) && isset($optionText['en']))
                    @click="submitChoice('{{ $choice->id }}')"
                @endif>
                @if(is_array($optionText) && isset($optionText['en']))
                    <span class="text-white text-center font-medium">{{ $optionText['en'] }}</span>
                @else
                    <span class="text-red-400 text-center font-medium">Choice text not defined</span>
                @endif
            </div>
        @endforeach
    </div>

    <form id="choiceForm" method="POST" action="{{ route('game.next') }}" class="hidden">
        @csrf
        <input type="hidden" name="choice_id" x-model="selectedChoice">
    </form>

    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-center"
        @click.away="open = false">
        <div class="relative bg-yellow-100 p-4 rounded-lg shadow-2xl border-4 border-yellow-800 w-96">
            <h2 class="text-xl font-bold text-center text-yellow-900">
                <span x-text="JSON.parse(selectedMission?.title)?.fr ?? 'Titre non défini'"></span>
            </h2>
            <div class="mt-4">
                <template x-for="(choice, index) in selectedMission?.choices" :key="index">
                    <div class="text-left mt-2 p-2 border border-gray-300 rounded bg-yellow-50">
                        <h3 class="font-bold text-gray-900">
                            <span x-text="JSON.parse(choice.option_text)?.fr ?? 'Option non définie'"></span>
                        </h3>
                    </div>
                </template>
            </div>
            <button class="mt-4 bg-yellow-800 text-yellow-200 py-2 px-4 rounded hover:bg-yellow-700 w-full" @click="open = false">
                Fermer
            </button>
        </div>
    </div>
</div>

<script>
    function submitChoice(choiceId) {
        document.querySelector('#choiceForm input[name="choice_id"]').value = choiceId;
        document.querySelector('#choiceForm').submit();
    }
</script>
