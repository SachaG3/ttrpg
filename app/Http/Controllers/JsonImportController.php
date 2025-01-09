<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Mission;
use App\Models\Choice;
use Illuminate\Http\Request;

class JsonImportController extends Controller
{
    public function import()
    {
        set_time_limit(60);

        $jsonPath = storage_path('app/stories.json');
        $jsonData = json_decode(file_get_contents($jsonPath), true);

        $missions = []; // Pour mapper les IDs des missions JSON aux IDs de la base de données

        foreach ($jsonData['players'] as $playerData) {
            foreach ($playerData['story']['questions'] as $index => $question) {
                // Déterminer si cette mission est une mission de départ
                $isStartMission = $index === 0;

                // Créer la mission
                $mission = Mission::create([
                    'title' => json_encode([
                        'en' => $question['text']['en'],
                        'fr' => $question['text']['fr'],
                    ]),
                    'description' => json_encode([
                        'en' => $question['text']['en'],
                        'fr' => $question['text']['fr'],
                    ]),
                    'status' => 'pending',
                    'assigned_type' => 1, // Exemple : type joueur
                    'assigned_id' => 0,
                    'start_mission' => $isStartMission,
                ]);

                // Mapper l'ID JSON au nouveau ID
                $missions[$playerData['id']][$question['id']] = $mission->id;

                // Ajouter les choix pour la mission
                foreach ($question['choices'] as $choiceData) {
                    // Ajouter ou récupérer l'item associé au choix
                    $item = null;
                    if (isset($choiceData['item'])) {
                        $isfinal=false;
                        if ($question['id'] === 4) {
                            $isfinal=true;
                        }
                        $item = Item::firstOrCreate(
                            ['id' => $choiceData['item']['id']],
                            [
                                'name' => json_encode([
                                    'en' => $choiceData['item']['name'],
                                    'fr' => $choiceData['item']['name'],
                                ]),
                                'description' => json_encode([
                                    'en' => $choiceData['item']['description']['en'],
                                    'fr' => $choiceData['item']['description']['fr'],
                                ]),
                                'is_final' => $isfinal,
                                'attrubute' => $choiceData['stat'],
                            ]
                        );
                    }

                    // Créer le choix

                    Choice::create([
                        'mission_id' => $mission->id,
                        'option_text' => json_encode([
                            'en' => $choiceData['text']['en'],
                            'fr' => $choiceData['text']['fr'],
                        ]),
                        'consequence_type' => $choiceData['stat'],
                        'consequence_value' => $choiceData[$choiceData['stat']] ?? 1,
                        'item_id' => $item ? $item->id : null,
                        'next_mission_id' => null,
                    ]);
                }
            }
        }

        // Mettre à jour les next_mission_id pour les choix
        foreach ($jsonData['players'] as $playerData) {
            foreach ($playerData['story']['questions'] as $question) {
                foreach ($question['choices'] as $choiceData) {
                    if (isset($missions[$playerData['id']][$question['id'] + 1])) {
                        $choice = Choice::where('mission_id', $missions[$playerData['id']][$question['id']])
                            ->whereJsonContains('option_text', ['en' => $choiceData['text']['en']])
                            ->first();

                        if ($choice) {
                            $choice->update([
                                'next_mission_id' => $missions[$playerData['id']][$question['id'] + 1],
                            ]);
                        }
                    }
                }
            }
        }

        return response()->json(['message' => 'JSON data imported successfully with start missions, next steps, and final items.']);
    }
}
