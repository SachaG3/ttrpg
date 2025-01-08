<?php
$items = [
    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/>
        </svg>'
    ,
    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>'
    ,
    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dice-1"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><path d="M12 12h.01"/></svg>'
    ,
    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dice-2"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><path d="M15 9h.01"/><path d="M9 15h.01"/></svg>'
    ,
    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-help"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>'
];

    $items = [
        ['name' => 'Book', 'description' => 'A magical book that contains spells.', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>'],
        ['name' => 'Sword', 'description' => 'A sharp blade for combat.', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sword"><path d="M19 2l3 3L8 19H5v-3L19 2zM6 11l2 2"/></svg>'],
        null, null, null, null, null, null
    ];

?>
<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<div x-data="{ open: false, selectedItem: null }" class="w-full">
    <!-- Titre de l'inventaire -->
    <h2 class="text-lg font-bold text-center text-gray-100 mb-2">Inventory</h2>

    <!-- Grille de l'inventaire -->
    <div class="grid grid-cols-4 gap-1 p-2 border border-dotted border-gray-700 bg-opacity-50 bg-black rounded-lg w-full">
        @for ($i = 0; $i < 8; $i++)
            <div
                class="relative w-full h-12 border border-dashed border-gray-500 bg-transparent flex justify-center items-center cursor-pointer hover:border-yellow-500"
                @click="open = true; selectedItem = {{ isset($items[$i]) ? json_encode($items[$i]) : 'null' }}">
                @if(isset($items[$i]))
                    {!! $items[$i]['icon'] !!}
                @else
                    <span class="text-gray-400 text-sm italic">Vide</span>
                @endif
            </div>
        @endfor
    </div>

    <!-- Détails d'un item (modale) -->
    <div
        x-show="open"
        x-cloak
        class=" fixed inset-0 bg-black bg-opacity-70 flex justify-center items-center"
        @click.away="open = false">
        <div class="relative bg-yellow-100 p-4 rounded-lg shadow-2xl border-4 border-yellow-800 w-72">
            <h2 class="text-xl font-bold text-center text-yellow-900" x-text="selectedItem ? selectedItem.name : 'Aucun objet'"></h2>
            <p class="mt-2 text-yellow-800 text-center italic text-sm" x-text="selectedItem ? selectedItem.description : ''"></p>
            <button class="mt-4 bg-yellow-800 text-yellow-200 py-2 px-4 rounded hover:bg-yellow-700 w-full" @click="open = false">
                Fermer
            </button>
        </div>
    </div>

</div>



