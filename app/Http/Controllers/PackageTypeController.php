<?php

namespace App\Http\Controllers;

use App\Models\PackageType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class PackageTypeController extends Controller
{
  



public function index(Request $request)
{
    if ($request->ajax()) {
        $data = PackageType::select('id', 'name');
        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $editUrl = route('package-types.edit', $row->id);
                $deleteUrl = route('package-types.destroy', $row->id);
                return '
                    <a href="'.$editUrl.'" 
                        class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-500 text-white rounded-lg shadow-sm hover:bg-yellow-600 transition">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </a>
                    <form action="'.$deleteUrl.'" method="POST" class="inline" onsubmit="return confirm(\'Delete this type?\')">
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

    return view('package_types.index');
}


    public function create()
    {
        return view('package_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        PackageType::create($request->all());

        return redirect()->route('package-types.index')
                         ->with('success', 'Package Type created successfully.');
    }

    public function edit(PackageType $packageType)
    {
        return view('package_types.edit', compact('packageType'));
    }

    public function update(Request $request, PackageType $packageType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $packageType->update($request->all());

        return redirect()->route('package-types.index')
                         ->with('success', 'Package Type updated successfully.');
    }

    public function destroy(PackageType $packageType)
    {
        $packageType->delete();

        return redirect()->route('package-types.index')
                         ->with('success', 'Package Type deleted successfully.');
    }
}
