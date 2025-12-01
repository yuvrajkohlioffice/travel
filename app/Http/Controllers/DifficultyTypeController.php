<?php

namespace App\Http\Controllers;

use App\Models\DifficultyType;
use Illuminate\Http\Request;

class DifficultyTypeController extends Controller
{
    public function index()
    {
        $difficultyTypes = DifficultyType::all();
        return view('difficulty_types.index', compact('difficultyTypes'));
    }

    public function create()
    {
        return view('difficulty_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
        ]);

        DifficultyType::create($request->all());

        return redirect()->route('difficulty-types.index')
                         ->with('success', 'Difficulty Type created successfully.');
    }

    public function edit(DifficultyType $difficultyType)
    {
        return view('difficulty_types.edit', compact('difficultyType'));
    }

    public function update(Request $request, DifficultyType $difficultyType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
        ]);

        $difficultyType->update($request->all());

        return redirect()->route('difficulty-types.index')
                         ->with('success', 'Difficulty Type updated successfully.');
    }

    public function destroy(DifficultyType $difficultyType)
    {
        $difficultyType->delete();

        return redirect()->route('difficulty-types.index')
                         ->with('success', 'Difficulty Type deleted successfully.');
    }
}
