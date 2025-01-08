<?php
$currentQuestion = [
    'text' => 'Que faites-vous face au gobelin ?',
    'choices' => [
        ['text' => 'Le prendre par surprise', 'response' => '+1 en dextérité'],
        ['text' => 'Le combattre face à face', 'response' => '+1 en force'],
        ['text' => 'Essayer de le soigner', 'response' => '+1 en sagesse'],
        ['text' => 'Utiliser un sort pour l’étudier', 'response' => '+1 en intelligence'],
    ],
];

?>

<div x-data="{ selectedChoice: null }" class="w-full">
    <h2 class="text-xl font-bold text-center text-gray-100 mb-4">
        {{ $currentQuestion['text'] }}
    </h2>

    <!-- Tableau des choix -->
    <div class="grid grid-cols-2 gap-4 p-4 border-2 border-dotted border-gray-700 bg-opacity-50 bg-black rounded-lg w-full">
        @foreach($currentQuestion['choices'] as $index => $choice)
            <div
                class="relative w-full h-16 border-2 border-dashed border-gray-500 bg-transparent flex justify-center items-center cursor-pointer hover:border-green-500"
                @click="selectedChoice = {{ json_encode($choice['response']) }}">
                <template x-if="selectedChoice === '{{ $choice['response'] }}'">
                    <span class="text-green-400 text-center font-medium italic" x-text="selectedChoice"></span>
                </template>
                <template x-if="selectedChoice !== '{{ $choice['response'] }}'">
                    <span class="text-white text-center font-medium">{{ $choice['text'] }}</span>
                </template>
            </div>
        @endforeach
    </div>
</div>

