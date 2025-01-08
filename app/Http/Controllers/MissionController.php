<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    public function index()
    {
        $missions = Mission::all();
        return view('missions.index', compact('missions'));
    }

    public function create()
    {
        return view('missions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,completed,failed',
            'assigned_type' => 'required|integer|in:1,2,3', // 1 = Player, 2 = Group, 3 = Faction
            'assigned_id' => 'required|integer',
        ]);

        Mission::create($request->all());
        return redirect()->route('missions.index')->with('success', 'Mission created successfully.');
    }

    public function show(Mission $mission)
    {
        return view('missions.show', compact('mission'));
    }

    public function edit(Mission $mission)
    {
        return view('missions.edit', compact('mission'));
    }

    public function update(Request $request, Mission $mission)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,completed,failed',
            'assigned_type' => 'required|integer|in:1,2,3',
            'assigned_id' => 'required|integer',
        ]);

        $mission->update($request->all());
        return redirect()->route('missions.index')->with('success', 'Mission updated successfully.');
    }

    public function destroy(Mission $mission)
    {
        $mission->delete();
        return redirect()->route('missions.index')->with('success', 'Mission deleted successfully.');
    }
}
