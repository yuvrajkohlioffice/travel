<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageType;
use App\Models\PackageCategory;
use App\Models\DifficultyType;
use App\Models\Car;
use App\Models\MessageTemplate;
use App\Models\PackageItem;
use App\Models\Hotel;
use App\Mail\SharePackageMail;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function index()
    {
        $query = Package::with(['packageType', 'packageCategory', 'difficultyType']);

        if (Auth::user() && Auth::user()->role_id == 1) {
            // ✅
            $packages = $query->withTrashed()->get();
        } else {
            $packages = $query->get();
        }

        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        $types = PackageType::all();
        $companies = Company::all();
        $categories = PackageCategory::all();
        $difficulties = DifficultyType::all();

        return view('packages.create', compact('types', 'categories', 'difficulties','companies'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePackage($request);

        $files = $this->handlePackageFiles($request);

        Package::create(array_merge($validated, $files));

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
    try {
        // Validation
        $validated = $this->validatePackage($request);

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Package update validation failed', [
            'package_id' => $package->id,
            'errors' => $e->errors(),
            'input' => $request->all(),
        ]);

        return back()->withErrors($e->validator)->withInput();
    }

    try {
        // File handling
        $files = $this->handlePackageFiles($request, $package);

    } catch (\Exception $e) {
        \Log::error('Package file upload failed', [
            'package_id' => $package->id,
            'message' => $e->getMessage(),
            'input' => $request->all(),
        ]);

        return back()->with('error', 'File upload failed. Please try again.')->withInput();
    }

    try {
        // Database update
        $package->update(array_merge($validated, $files));

    } catch (\Exception $e) {
        \Log::error('Package update failed', [
            'package_id' => $package->id,
            'message' => $e->getMessage(),
        ]);

        return back()->with('error', 'Something went wrong while updating the package.');
    }

    return redirect()
        ->route('packages.index')
        ->with('success', 'Package updated successfully!');
}


    public function destroy(Package $package)
    {
        $this->deletePackageFiles($package);
        $package->delete();

        return redirect()->route('packages.index')->with('success', 'Package deleted successfully!');
    }

    public function show(Package $package)
    {
        return view('packages.show', compact('package'));
    }

    public function editRelations(Package $package)
    {
        $package->load(['packageItems.car']);
        $allCars = Car::all();
        $allHotels = Hotel::all();

        return view('packages.edit-relations', compact('package', 'allCars', 'allHotels'));
    }

    public function partialItemRow(Request $request)
    {
        $index = $request->query('index', 1);
        $itemId = $request->query('item_id', null);

        $item = $itemId ? PackageItem::find($itemId) : null;
        $allCars = Car::all();
        $allHotels = Hotel::all();

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

        $insertData = [];
        foreach ($data['items'] ?? [] as $item) {
            $insertData[] = array_merge($item, [
                'package_id' => $package->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!empty($insertData)) {
            DB::table('package_items')->insert($insertData);
        }

        if ($request->ajax()) {
            $package->load('packageItems.car');
            $html = view('packages.partials.package-items-table', compact('package'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'message' => 'Package items updated successfully!',
            ]);
        }

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
        $success = false;
        $message = 'Failed to delete package item.';

        try {
            $item->delete();
            $success = true;
            $message = 'Package item deleted successfully!';
        } catch (\Exception $e) {
            $message = 'Failed to delete package item: ' . $e->getMessage();
        }

        if (request()->ajax()) {
            return response()->json(['success' => $success, 'message' => $message]);
        }

        return back()->with($success ? 'success' : 'error', $message);
    }

    public function apiShow(Package $package)
{
    // Load relationships
    $package->load([
        'packageType',
        'packageCategory',
        'difficultyType',
        'packageItems.car',
        'messageTemplate' // Load the message template
    ]);

    $packageItems = $package->packageItems->map(
        fn($item) => [
            'id' => $item->id,
            'car' => $item->car,
            'person_count' => $item->person_count,
            'vehicle_name' => $item->vehicle_name,
            'room_count' => $item->room_count,
            'standard_price' => $item->standard_price,
            'deluxe_price' => $item->deluxe_price,
            'luxury_price' => $item->luxury_price,
            'premium_price' => $item->premium_price,
        ]
    );

    // Prepare message template if exists
    $messageTemplate = $package->messageTemplate
        ? [
            'whatsapp' => [
                'text' => $package->messageTemplate->whatsapp_text,
                'media' => $package->messageTemplate->whatsapp_media,
            ],
            'email' => [
                'subject' => $package->messageTemplate->email_subject,
                'body' => $package->messageTemplate->email_body,
                'media' => $package->messageTemplate->email_media,
            ]
        ]
        : null;

    return response()->json([
        'success' => true,
        'package' => [
            'id' => $package->id,
            'package_name' => $package->package_name,
            'package_days' => $package->package_days,
            'package_nights' => $package->package_nights,
            'package_price' => $package->package_price,
            'pickup_points' => $package->pickup_points,
            'package_banner_url' => $package->package_banner_url,
            'package_docs_url' => $package->package_docs_url,
            'other_images_url' => $package->other_images_url,
            'packageType' => $package->packageType,
            'packageCategory' => $package->packageCategory,
            'difficultyType' => $package->difficultyType,
            'packageItems' => $packageItems,
            'messageTemplate' => $messageTemplate, // include the template
        ],
    ]);
}


    public function sendPackageEmail(Request $request)
    {
        $request->validate([
            'lead_name' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'email' => 'required|email',
            'media_type' => 'nullable|string|in:template,banner,docs', // select which media to send
        ]);

        $package = Package::findOrFail($request->package_id);
        $template = MessageTemplate::where('package_id', $package->id)->first();

        $documents = [];

        switch ($request->media_type) {
            case 'template':
                if ($template?->email_media && Storage::disk('public')->exists($template->email_media)) {
                    $documents[] = Storage::disk('public')->path($template->email_media); // full path for Mail attachment
                }
                break;

            case 'banner':
                if ($package->package_banner && Storage::disk('public')->exists($package->package_banner)) {
                    $documents[] = Storage::disk('public')->path($package->package_banner);
                }
                break;

            case 'docs':
                if ($package->package_docs) {
                    $docs = is_array($package->package_docs) ? $package->package_docs : [$package->package_docs];
                    foreach ($docs as $doc) {
                        if (Storage::disk('public')->exists($doc)) {
                            $documents[] = Storage::disk('public')->path($doc);
                        }
                    }
                }
                break;
        }

        // Use template email subject/body if exists
        $subject = $template->email_subject ?? "Package Details: {$package->package_name}";
        $body = $template->email_body ?? "Hello, please find the details of the package: {$package->package_name}.";

        // Send email
        Mail::to($request->email)->send(new SharePackageMail($request->lead_name, $subject, $body, $documents));

        return response()->json([
            'success' => true,
            'message' => 'Package email sent successfully!',
            'media_type_used' => $request->media_type,
            'documents_sent' => $documents,
        ]);
    }


    public function filterPackageItems(Request $request)
    {
        $packageId = $request->package_id;
        $carId = $request->car_id;
        $totalPeople = ($request->adult_count ?? 0) + ($request->child_count ?? 0);

        if (!$packageId) {
            return response()->json([
                'success' => false,
                'message' => 'Package ID is required.',
                'data' => [],
            ]);
        }

        $query = PackageItem::with('car')->where('package_id', $packageId);

        if ($totalPeople > 0) {
            $query->where('person_count', $totalPeople);
        }
        if ($carId) {
            $query->where('car_id', $carId);
        }

        $items = $query->orderByDesc('id')->get();

        return response()->json([
            'success' => $items->isNotEmpty(),
            'data' => $items,
            'message' => $items->isNotEmpty() ? '' : 'No items found for the selected criteria.',
        ]);
    }

    /**
     * Handle file uploads and deletion for update
     */
    private function handlePackageFiles(Request $request, Package $package = null): array
    {
        $data = [];

        foreach (['package_banner', 'package_docs'] as $field) {
            if ($request->hasFile($field)) {
                // Delete old
                if ($package && $package->$field) {
                    Storage::disk('public')->delete($package->$field);
                }
                $data[$field] = $request->file($field)->store("packages/$field", 'public');
            }
        }

        if ($request->hasFile('other_images')) {
            if ($package && $package->other_images) {
                foreach ($package->other_images as $img) {
                    Storage::disk('public')->delete($img);
                }
            }
            $data['other_images'] = array_map(fn($img) => $img->store('packages/other_images', 'public'), $request->file('other_images'));
        }

        return $data;
    }

    private function deletePackageFiles(Package $package)
    {
        foreach (['package_banner', 'package_docs'] as $field) {
            if ($package->$field) {
                Storage::disk('public')->delete($package->$field);
            }
        }

        if ($package->other_images) {
            foreach ($package->other_images as $img) {
                Storage::disk('public')->delete($img);
            }
        }
    }

    private function validatePackage(Request $request): array
    {
        return $request->validate([
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
            'company_id' => 'nullable|exists:companies,id', // ✅ Added
        ]);
    }
}
