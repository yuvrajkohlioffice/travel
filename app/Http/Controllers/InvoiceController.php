<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Package;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Generate invoice number e.g. TRAV-2023-0876
     */
    private function generateInvoiceNo()
    {
        $latest = Invoice::latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;

        return 'TRAV-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Show Create Invoice Form
     */
    public function create(Request $request)
    {
        $invoice = null;
        $lead = null;
        $package = null;

        // Load existing invoice if invoice_id exists
        if ($request->invoice_id) {
            $invoice = Invoice::with('package', 'lead')->find($request->invoice_id);
            if ($invoice) {
                $lead = $invoice->lead;
                $package = $invoice->package;
            }
        }

        // Load lead if lead_id exists and no invoice
        if (!$invoice && $request->lead_id) {
            $lead = Lead::find($request->lead_id);
            $package = $lead?->package;
        }

        $packages = Package::all();

        // Prefill values
        $prefill = [
            'package_id' => $invoice->package_id ?? $request->query('package_id', $package?->id ?? null),
            'package_type' => $invoice->package_type ?? $request->query('package_type', 'standard_price'),
            'adult_count' => $invoice->adult_count ?? $request->query('adult_count', 1),
            'child_count' => $invoice->child_count ?? $request->query('child_count', 0),
            'discount_amount' => $invoice->discount_amount ?? $request->query('discount_amount', 0),
            'tax_amount' => $invoice->tax_amount ?? $request->query('tax_amount', 0),
            'price_per_person' => $invoice->price_per_person ?? $request->query('price_per_person', $package?->package_price ?? 0),
            'travel_start_date' => $invoice->travel_start_date ?? $request->query('travel_start_date', now()->toDateString()),
        ];

        return view('invoices.create', compact('lead', 'package', 'prefill', 'packages', 'invoice'));
    }

    /**
     * Store Invoice
     */
    public function store(Request $request)
    {
        $request->validate([
            'primary_full_name' => 'required|string|max:255',
            'issued_date' => 'required|date',
            'travel_start_date' => 'required|date',
            'total_travelers' => 'required|integer|min:1',
            'price_per_person' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create([
            'invoice_no' => $this->generateInvoiceNo(),
            'user_id' => Auth::id(),

            'lead_id' => $request->lead_id,
            'package_id' => $request->package_id,

            'issued_date' => $request->issued_date,
            'travel_start_date' => $request->travel_start_date,

            'primary_full_name' => $request->primary_full_name,
            'primary_email' => $request->primary_email,
            'primary_phone' => $request->primary_phone,
            'primary_address' => $request->primary_address,

            'additional_travelers' => $request->additional_travelers ? json_decode($request->additional_travelers, true) : null,

            'total_travelers' => $request->total_travelers,
            'adult_count' => $request->adult_count,
            'child_count' => $request->child_count,

            'package_name' => $request->package_name,
            'package_type' => $request->package_type,
            'price_per_person' => $request->price_per_person,

            'subtotal_price' => $request->price_per_person * $request->total_travelers,
            'discount_amount' => $request->discount_amount ?? 0,
            'tax_amount' => $request->tax_amount ?? 0,
            'final_price' => $request->price_per_person * $request->total_travelers - ($request->discount_amount ?? 0) + ($request->tax_amount ?? 0),

            'additional_details' => $request->additional_details,
        ]);

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Invoice created successfully.');
    }

    /**
     * Show Single Invoice
     */
    public function show($id)
    {
        $invoice = Invoice::with(['lead', 'package', 'user'])->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Display All Invoices
     */
    public function index()
    {
        $invoices = Invoice::latest()->paginate(20);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Delete Invoice
     */
    public function destroy($id)
    {
        Invoice::findOrFail($id)->delete();

        return back()->with('success', 'Invoice deleted successfully.');
    }

    public function createQuickInvoice(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|integer|exists:leads,id',
            'package_id' => 'required|integer|exists:packages,id',
            'package_items_id' => 'nullable|integer|exists:package_items,id',
            'package_type' => 'required|string',
            'adult_count' => 'required|integer|min:0',
            'child_count' => 'required|integer|min:0',
            'discount_amount' => 'required|numeric|min:0',
            'price_per_person' => 'required|numeric|min:0',
            'travel_start_date' => 'nullable|date',
        ]);

        $userId = Auth::id() ?? 1; // fallback to 1 if not logged in

        // Fetch lead to get name
        $lead = Lead::find($request->lead_id);

        // Current date in India
        $issuedDate = Carbon::now('Asia/Kolkata')->toDateString();

        $totalTravelers = $request->adult_count + $request->child_count;
        $subtotalPrice = $request->price_per_person * $totalTravelers;
        $finalPrice = $subtotalPrice - $request->discount_amount;

        $invoice = Invoice::create([
            'invoice_no' => $this->generateInvoiceNo(),
            'user_id' => $userId,
            'lead_id' => $request->lead_id,
            'primary_full_name' => $lead->name ?? 'Unknown',
            'package_id' => $request->package_id,
            'package_items_id' => $request->package_items_id ?? null, // ✅ save selected item
            'package_type' => $request->package_type,
            'adult_count' => $request->adult_count,
            'child_count' => $request->child_count,
            'discount_amount' => $request->discount_amount,
            'price_per_person' => $request->price_per_person,
            'travel_start_date' => $request->travel_start_date,
            'issued_date' => $issuedDate,
            'total_travelers' => $totalTravelers,
            'subtotal_price' => $subtotalPrice,
            'final_price' => $finalPrice,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully',
            'data' => $invoice,
        ]);
    }
}
