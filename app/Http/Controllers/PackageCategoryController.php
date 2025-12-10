<?php

namespace App\Http\Controllers;

use App\Models\PackageCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class PackageCategoryController extends Controller
{
    

public function index(Request $request)
{
    if ($request->ajax()) {
        $data = PackageCategory::select('id', 'name');

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $editUrl = route('package-categories.edit', $row->id);
                $deleteUrl = route('package-categories.destroy', $row->id);

                return '
                    <a href="'.$editUrl.'" 
                        class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-500 text-white rounded-lg shadow-sm hover:bg-yellow-600">
                        <i class="fa-solid fa-pen-to-square"></i>Edit
                    </a>

                    <form action="'.$deleteUrl.'" method="POST" class="inline" 
                        onsubmit="return confirm(\'Delete this category?\')">
                        '.csrf_field().method_field('DELETE').'
                        <button class="inline-flex items-center gap-1 px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fa-solid fa-trash"></i>Delete
                        </button>
                    </form>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    return view('package_categories.index');
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
