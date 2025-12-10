<?php

namespace App\Http\Controllers;

use App\Models\DifficultyType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class DifficultyTypeController extends Controller
{
   


public function index(Request $request)
{
    if ($request->ajax()) {
        $data = DifficultyType::select('id', 'name', 'level');
        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $editUrl = route('difficulty-types.edit', $row->id);
                $deleteUrl = route('difficulty-types.destroy', $row->id);
                return '
                    <a href="'.$editUrl.'" 
                        class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-500 text-white rounded-lg shadow-sm hover:bg-yellow-600 transition">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </a>
                    <form action="'.$deleteUrl.'" method="POST" class="inline" onsubmit="return confirm(\'Delete this difficulty type?\')">
                        '.csrf_field().method_field('DELETE').'
                        <button class="inline-flex items-center gap-1 px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-sm transition">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </form>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    return view('difficulty_types.index');
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
