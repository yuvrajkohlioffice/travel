<?php

namespace App\Http\Controllers;

use App\Models\PackageCategory;
use Illuminate\Http\Request;

class PackageCategoryController extends Controller
{
    public function index()
    {
        $categories = PackageCategory::all();
        return view('package_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('package_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        PackageCategory::create($request->all());

        return redirect()->route('package-categories.index')
                         ->with('success', 'Package Category created successfully.');
    }

    public function edit(PackageCategory $packageCategory)
    {
        return view('package_categories.edit', compact('packageCategory'));
    }

    public function update(Request $request, PackageCategory $packageCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $packageCategory->update($request->all());

        return redirect()->route('package-categories.index')
                         ->with('success', 'Package Category updated successfully.');
    }

    public function destroy(PackageCategory $packageCategory)
    {
        $packageCategory->delete();

        return redirect()->route('package-categories.index')
                         ->with('success', 'Package Category deleted successfully.');
    }
}
