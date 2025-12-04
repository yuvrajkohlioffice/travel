<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Package;
use Illuminate\Http\Request;
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
    $lead = null;
    $package = null;

    // Load lead if lead_id exists
    if ($request->lead_id) {
        $lead = Lead::find($request->lead_id);
        $package = $lead?->package;
    }
$packages = Package::all();
    // Prefill from query params if available
    $prefill = [
        'package_id'       => $request->query('package_id', $package?->id ?? null),
        'package_type'     => $request->query('package_type', 'standard_price'),
        'adult_count'      => $request->query('adult_count', 1),
        'child_count'      => $request->query('child_count', 0),
        'discount_amount'  => $request->query('discount_amount', 0),
        'tax_amount'       => $request->query('tax_amount', 0),
        'price_per_person' => $request->query('price_per_person', $package?->package_price ?? 0),
    ];

    return view('invoices.create', compact('lead', 'package', 'prefill','packages'));
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
            'invoice_no'        => $this->generateInvoiceNo(),
            'user_id'           => Auth::id(),

            'lead_id'           => $request->lead_id,
            'package_id'        => $request->package_id,

            'issued_date'       => $request->issued_date,
            'travel_start_date' => $request->travel_start_date,

            'primary_full_name' => $request->primary_full_name,
            'primary_email'     => $request->primary_email,
            'primary_phone'     => $request->primary_phone,
            'primary_address'   => $request->primary_address,

            'additional_travelers' => $request->additional_travelers
                ? json_decode($request->additional_travelers, true)
                : null,

            'total_travelers' => $request->total_travelers,
            'adult_count'     => $request->adult_count,
            'child_count'     => $request->child_count,

            'package_name'    => $request->package_name,
            'package_type'    => $request->package_type,
            'price_per_person'=> $request->price_per_person,

            'subtotal_price'  => $request->price_per_person * $request->total_travelers,
            'discount_amount' => $request->discount_amount ?? 0,
            'tax_amount'      => $request->tax_amount ?? 0,
            'final_price'     => (($request->price_per_person * $request->total_travelers)
                                    - ($request->discount_amount ?? 0))
                                    + ($request->tax_amount ?? 0),

            'additional_details' => $request->additional_details,
        ]);

        return redirect()->route('invoices.show', $invoice->id)
                         ->with('success', 'Invoice created successfully.');
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
}
