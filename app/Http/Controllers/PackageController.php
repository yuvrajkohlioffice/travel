<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageType;
use App\Models\PackageCategory;
use App\Models\DifficultyType;
use App\Models\Car;
use App\Models\PackageItem;
use App\Mail\SharePackageMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with(['packageType', 'packageCategory', 'difficultyType'])->get();
        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        $types = PackageType::all();
        $categories = PackageCategory::all();
        $difficulties = DifficultyType::all();

        return view('packages.create', compact('types', 'categories', 'difficulties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'package_type_id' => 'required|exists:package_types,id',
            'package_category_id' => 'required|exists:package_category,id',
            'difficulty_type_id' => 'required|exists:difficulty_types,id',
            'package_days' => 'required|integer|min:1',
            'package_nights' => 'required|integer|min:0',
            'package_price' => 'required|numeric|min:0',
            'pickup_points' => 'nullable|string',
            'package_docs' => 'nullable|file|mimes:pdf,doc,docx',
            'package_banner' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'other_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'altitude' => 'nullable|string',
            'min_age' => 'nullable|integer|min:0',
            'max_age' => 'nullable|integer|min:0',
            'best_time_to_visit' => 'nullable|string',
            'content' => 'nullable|string',
        ]);

        $data = $request->all();

        // Handle package banner
        if ($request->hasFile('package_banner')) {
            $data['package_banner'] = $request->file('package_banner')->store('packages/banners', 'public');
        }

        // Handle package docs
        if ($request->hasFile('package_docs')) {
            $data['package_docs'] = $request->file('package_docs')->store('packages/docs', 'public');
        }

        // Handle other images
        if ($request->hasFile('other_images')) {
            $otherImages = [];
            foreach ($request->file('other_images') as $image) {
                $otherImages[] = $image->store('packages/other_images', 'public');
            }
            $data['other_images'] = $otherImages;
        }

        Package::create($data);

        return redirect()->route('packages.index')->with('success', 'Package created successfully!');
    }

    public function edit(Package $package)
    {
        $types = PackageType::all();
        $categories = PackageCategory::all();
        $difficulties = DifficultyType::all();

        return view('packages.edit', compact('package', 'types', 'categories', 'difficulties'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'package_type_id' => 'required|exists:package_types,id',
            'package_category_id' => 'required|exists:package_category,id',
            'difficulty_type_id' => 'required|exists:difficulty_types,id',
            'package_days' => 'required|integer|min:1',
            'package_nights' => 'required|integer|min:0',
            'package_price' => 'required|numeric|min:0',
            'pickup_points' => 'nullable|string',
            'package_docs' => 'nullable|file|mimes:pdf,doc,docx',
            'package_banner' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'other_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'altitude' => 'nullable|string',
            'min_age' => 'nullable|integer|min:0',
            'max_age' => 'nullable|integer|min:0',
            'best_time_to_visit' => 'nullable|string',
            'content' => 'nullable|string',
        ]);

        $data = $request->all();

        // Update package banner
        if ($request->hasFile('package_banner')) {
            // Delete old banner
            if ($package->package_banner) {
                Storage::disk('public')->delete($package->package_banner);
            }
            $data['package_banner'] = $request->file('package_banner')->store('packages/banners', 'public');
        }

        // Update package docs
        if ($request->hasFile('package_docs')) {
            if ($package->package_docs) {
                Storage::disk('public')->delete($package->package_docs);
            }
            $data['package_docs'] = $request->file('package_docs')->store('packages/docs', 'public');
        }

        // Update other images
        if ($request->hasFile('other_images')) {
            // Delete old images
            if ($package->other_images) {
                foreach ($package->other_images as $img) {
                    Storage::disk('public')->delete($img);
                }
            }

            $otherImages = [];
            foreach ($request->file('other_images') as $image) {
                $otherImages[] = $image->store('packages/other_images', 'public');
            }
            $data['other_images'] = $otherImages;
        }

        $package->update($data);

        return redirect()->route('packages.index')->with('success', 'Package updated successfully!');
    }

    public function destroy(Package $package)
    {
        // Delete associated files
        if ($package->package_banner) {
            Storage::disk('public')->delete($package->package_banner);
        }
        if ($package->package_docs) {
            Storage::disk('public')->delete($package->package_docs);
        }
        if ($package->other_images) {
            foreach ($package->other_images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Package deleted successfully!');
    }

    public function show(Package $package)
    {
        return view('packages.show', compact('package'));
    }

    public function editRelations(Package $package)
    {
        // Load actual package items with related car and hotel
        $package->load(['packageItems.car']);

        $allCars = Car::all();
        $allHotels = Hotel::all();

        return view('packages.edit-relations', compact('package', 'allCars', 'allHotels'));
    }

    /**
     * Update package's cars and hotels with custom_price and already_price
     */
    public function partialItemRow(Request $request)
    {
        $index = $request->query('index', null);
        $itemId = $request->query('item_id', null);

        $allCars = Car::all();
        $allHotels = Hotel::all();

        $item = null;
        if ($itemId) {
            $item = \App\Models\PackageItem::find($itemId);
            $index = $itemId; // just to keep unique input names
        } elseif (!$index) {
            $index = 1;
        }

        return view('packages.partials.package-item-row', compact('index', 'item', 'allCars', 'allHotels'));
    }

    public function updateRelations(Request $request, Package $package)
    {
        $data = $request->validate([
            'items' => 'nullable|array',
            'items.*.car_id' => 'required|exists:cars,id',
            'items.*.person_count' => 'required|integer|min:1',
            'items.*.vehicle_name' => 'nullable|string|max:255',
            'items.*.room_count' => 'required|integer|min:1',
            'items.*.standard_price' => 'nullable|numeric|min:0',
            'items.*.deluxe_price' => 'nullable|numeric|min:0',
            'items.*.luxury_price' => 'nullable|numeric|min:0',
            'items.*.premium_price' => 'nullable|numeric|min:0',
        ]);

        // Insert new records
        $insertData = [];
        foreach ($data['items'] ?? [] as $item) {
            $insertData[] = [
                'package_id' => $package->id,
                'car_id' => $item['car_id'],
                'person_count' => $item['person_count'],
                'vehicle_name' => $item['vehicle_name'],
                'room_count' => $item['room_count'],
                'standard_price' => $item['standard_price'] ?? null,
                'deluxe_price' => $item['deluxe_price'] ?? null,
                'luxury_price' => $item['luxury_price'] ?? null,
                'premium_price' => $item['premium_price'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            DB::table('package_items')->insert($insertData);
        }

        // AJAX response
        if ($request->ajax()) {
            $package->load('packageItems.car');
            $html = view('packages.partials.package-items-table', compact('package'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'message' => 'Package items updated successfully!',
            ]);
        }

        // Regular redirect with flash message
        return redirect()->route('packages.edit-relations', $package->id)->with('success', 'Package items updated successfully!');
    }
    public function updatePackageItem(Request $request, PackageItem $item)
    {
        $data = $request->validate([
            'car_id' => 'nullable|exists:cars,id',
            'person_count' => 'nullable|integer|min:1',
            'vehicle_name' => 'nullable|string|max:255',
            'room_count' => 'nullable|integer|min:1',
            'standard_price' => 'nullable|numeric|min:0',
            'deluxe_price' => 'nullable|numeric|min:0',
            'luxury_price' => 'nullable|numeric|min:0',
            'premium_price' => 'nullable|numeric|min:0',
        ]);

        $item->update($data);

        if ($request->ajax()) {
            // Reload package items to update table dynamically
            $package = $item->package()->with('packageItems.car')->first();
            $html = view('packages.partials.package-items-table', compact('package'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'message' => 'Package item updated successfully!',
            ]);
        }

        return redirect()->back()->with('success', 'Package item updated successfully!');
    }

    public function deleteRelation(PackageItem $item)
    {
        try {
            $item->delete();

            // If AJAX request, return JSON
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Package item deleted successfully!',
                ]);
            }

            // Otherwise, redirect back
            return back()->with('success', 'Package item deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete package item: ' . $e->getMessage(),
                ]);
            }

            return back()->with('error', 'Failed to delete package item.');
        }
    }

    public function apiShow(Package $package)
    {
        // Eager load relationships
        $package->load(['packageType', 'packageCategory', 'difficultyType', 'packageItems.car']);

        // Transform packageItems with car details
        $packageItems = $package->packageItems->map(function ($item) {
            return [
                'id' => $item->id,
                'car' => $item->car,
                'person_count' => $item->person_count,
                'vehicle_name' => $item->vehicle_name,
                'room_count' => $item->room_count,
                'standard_price' => $item->standard_price,
                'deluxe_price' => $item->deluxe_price,
                'luxury_price' => $item->luxury_price,
                'premium_price' => $item->premium_price,
            ];
        });

        // Build custom response
        $response = [
            'id' => $package->id,
            'package_name' => $package->package_name,
            'package_days' => $package->package_days,
            'package_nights' => $package->package_nights,
            'package_price' => $package->package_price,
            'pickup_points' => $package->pickup_points,
            'package_banner_url' => $package->package_banner ? asset('storage/' . $package->package_banner) : null,
            'package_docs_url' => $package->package_docs ? asset('storage/' . $package->package_docs) : null,
            'other_images_url' => collect($package->other_images ?? [])
                ->map(fn($img) => asset('storage/' . $img))
                ->toArray(),
            'packageType' => $package->packageType,
            'packageCategory' => $package->packageCategory,
            'difficultyType' => $package->difficultyType,
            'packageItems' => $packageItems,
        ];

        return response()->json([
            'success' => true,
            'package' => $response,
        ]);
    }

    public function sendPackageEmail(Request $request)
    {
        $request->validate([
            'lead_name' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'email' => 'required|email',
            'documents' => 'nullable|array',
        ]);

        $package = Package::findOrFail($request->package_id);

        // Use selected documents or all package docs
        $docs = $request->documents ?? $package->package_docs;

        // Resolve full paths and filter out invalid files
        $validDocs = [];
        foreach ($docs as $doc) {
            // If it's already a full URL from storage, convert to storage path
            if (str_starts_with($doc, asset('storage'))) {
                $docPath = str_replace(asset('storage'), storage_path('app/public'), $doc);
            } else {
                // Otherwise, assume relative to public folder
                $docPath = public_path($doc);
            }

            if (file_exists($docPath) && is_readable($docPath)) {
                $validDocs[] = $docPath;
            }
        }

        Mail::to($request->email)->send(new SharePackageMail($request->lead_name, $package->package_name, $validDocs));

        return response()->json([
            'success' => true,
            'message' => 'Package email sent successfully!',
        ]);
    }
public function filterPackageItems(Request $request)
{
    $packageId = $request->package_id;
    $carId = $request->car_id;

    $totalPeople = ($request->adult_count ?? 0) + ($request->child_count ?? 0);

    // ------------------------------------------
    // Validate package
    // ------------------------------------------
    if (!$packageId) {
        return response()->json([
            'success' => false,
            'message' => 'Package ID is required.',
            'data' => [],
        ]);
    }

    // ------------------------------------------
    // Build base query
    // ------------------------------------------
    $query = PackageItem::with('car')->where('package_id', $packageId);

    // ------------------------------------------
    // Filter by people count if provided
    // ------------------------------------------
    if ($totalPeople > 0) {
        $query->where('person_count', $totalPeople);
    }

    // ------------------------------------------
    // Filter by car only if car_id is provided
    // ------------------------------------------
    if ($carId) {
        $query->where('car_id', $carId);
    }

    $items = $query->orderBy('id', 'desc')->get();

    if ($items->count() > 0) {
        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    // ------------------------------------------
    // Nothing found
    // ------------------------------------------
    return response()->json([
        'success' => false,
        'message' => 'No items found for the selected criteria.',
        'data' => [],
    ]);
}


}
