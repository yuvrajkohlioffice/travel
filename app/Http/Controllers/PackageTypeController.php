<?php

namespace App\Http\Controllers;

use App\Models\PackageType;
use Illuminate\Http\Request;

class PackageTypeController extends Controller
{
    public function index()
    {
        $packageTypes = PackageType::all();
        return view('package_types.index', compact('packageTypes'));
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
