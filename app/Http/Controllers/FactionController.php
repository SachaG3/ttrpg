<?php

namespace App\Http\Controllers;

use App\Models\Faction;
use Illuminate\Http\Request;

class FactionController extends Controller
{
    public function index()
    {
        $factions = Faction::all();
        return view('factions.index', compact('factions'));
    }

    public function create()
    {
        return view('factions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'score' => 'integer',
        ]);

        Faction::create($request->all());
        return redirect()->route('factions.index')->with('success', 'Faction created successfully.');
    }

    public function show(Faction $faction)
    {
        return view('factions.show', compact('faction'));
    }

    public function edit(Faction $faction)
    {
        return view('factions.edit', compact('faction'));
    }

    public function update(Request $request, Faction $faction)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'score' => 'integer',
        ]);

        $faction->update($request->all());
        return redirect()->route('factions.index')->with('success', 'Faction updated successfully.');
    }

    public function destroy(Faction $faction)
    {
        $faction->delete();
        return redirect()->route('factions.index')->with('success', 'Faction deleted successfully.');
    }
}
