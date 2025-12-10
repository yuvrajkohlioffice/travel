<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Package;
use App\Models\PackageItem;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
class InvoiceController extends Controller
{
   
public function index(Request $request)
{
    // AJAX request from DataTables
    if ($request->ajax()) {
        $query = Invoice::with(['lead', 'package'])->latest();

        return DataTables::of($query)
            ->addIndexColumn()

            ->editColumn('invoice_no', function ($row) {
                return '#' . $row->invoice_no;
            })

            ->editColumn('user', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user text-gray-400"></i>
                        <div>
                            <div class="font-semibold">'.($row->lead->name ?? 'N/A').'</div>
                            <div class="text-xs text-gray-500">'.($row->lead->email ?? '—').'</div>
                        </div>
                    </div>';
            })

            ->editColumn('package_name', function ($row) {
                return '<i class="fas fa-box text-gray-400"></i> ' .
                       ($row->package->package_name ?? $row->package_name ?? 'N/A');
            })

            ->addColumn('travelers', function ($row) {
                return "Adults: <strong>$row->adult_count</strong> • 
                        Children: <strong>$row->child_count</strong> <br> 
                        Total: <strong>$row->total_travelers</strong>";
            })

            ->addColumn('dates', function ($row) {
                return '
                    Issued: <strong>'.$row->formatted_issue_date.'</strong><br>
                    Travel: <strong>'.$row->formatted_travel_start_date.'</strong>
                ';
            })

            ->addColumn('amount', function ($row) {
                return '
                    Subtotal: ₹'.number_format($row->subtotal_price, 2).'<br>
                    Discount: -₹'.number_format($row->discount_amount, 2).'<br>
                    Tax: +₹'.number_format($row->tax_amount, 2).'<br>
                    <span class="font-bold text-green-600">
                        Final: ₹'.number_format($row->final_price, 2).'
                    </span>';
            })

            ->addColumn('action', function ($row) {
                return '<a href="'.route('invoices.show', $row->id).'"
                    class="text-blue-600 hover:underline">View</a>';
            })

            ->rawColumns(['user', 'package_name', 'travelers', 'dates', 'amount', 'action'])
            ->make(true);
    }

    return view('invoices.index');
}

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
        $packageItems = collect();

        // ---------------------------------------------
        // 1️⃣ If invoice_id is present → EDIT invoice
        // ---------------------------------------------
        if ($request->filled('invoice_id')) {
            $invoice = Invoice::with(['package', 'packageItem', 'lead'])->find($request->invoice_id);

            // If invoice exists, load its related data
            if ($invoice) {
                $lead = $invoice->lead;
                $package = $invoice->package;
                $packageItems = $package ? $package->packageItems : collect();
            }
        }

        // -------------------------------------------------
        // 2️⃣ If creating from a lead and NOT editing invoice
        // -------------------------------------------------
        if (!$invoice && $request->filled('lead_id')) {
            $lead = Lead::with('package')->find($request->lead_id);

            if ($lead) {
                $package = $lead->package;
                $packageItems = $package ? $package->packageItems : collect();
            }
        }

        // ---------------------------------------------
        // 3️⃣ Return view
        // ---------------------------------------------
        return view('invoices.create', [
            'invoice' => $invoice,
            'lead' => $lead,
            'packages' => Package::all(),
            'packageItems' => $packageItems,
        ]);
    }

    /**
     * Store Invoice
     */
    public function store(Request $request)
    {
        // 1️⃣ Validate incoming request
        $request->validate([
            'lead_id' => 'nullable|exists:leads,id',
            'package_id' => 'required|exists:packages,id',
            'package_items_id' => 'nullable|exists:package_items,id',
            'primary_full_name' => 'required|string|max:255',
            'primary_email' => 'nullable|email|max:255',
            'primary_phone' => 'nullable|string|max:20',
            'primary_address' => 'nullable|string|max:500',
            'issued_date' => 'required|date',
            'travel_start_date' => 'required|date',
            'adult_count' => 'required|integer|min:1',
            'child_count' => 'nullable|integer|min:0',
            'additional_travelers' => 'nullable|string',
            'total_travelers' => 'required|integer|min:1',
            'package_name' => 'nullable|string|max:255',
            'package_type' => 'nullable|string|max:100',
            'price_per_person' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'additional_details' => 'nullable|string',
        ]);

        // 2️⃣ Safely parse additional travelers JSON
        $additionalTravelers = null;
        if ($request->filled('additional_travelers')) {
            try {
                $additionalTravelers = json_decode($request->additional_travelers, true);
            } catch (\Exception $e) {
                $additionalTravelers = null; // fallback if JSON invalid
            }
        }

        // 3️⃣ Calculate derived fields
        $adultCount = $request->adult_count;
        $childCount = $request->child_count ?? 0;
        $pricePerPerson = $request->price_per_person;

        // Subtotal calculation: adult full price + child half price
        $subtotal = $adultCount * $pricePerPerson + $childCount * ($pricePerPerson / 2);

        $discount = $request->discount_amount ?? 0;
        $tax = $request->tax_amount ?? 0;

        $finalPrice = max(0, $subtotal - $discount + $tax);

        // 4️⃣ Create the invoice
        $invoice = Invoice::create([
            'invoice_no' => $this->generateInvoiceNo(),
            'user_id' => Auth::id(),

            'lead_id' => $request->lead_id,
            'package_id' => $request->package_id,
            'package_items_id' => $request->package_items_id,

            'issued_date' => $request->issued_date,
            'travel_start_date' => $request->travel_start_date,

            'primary_full_name' => $request->primary_full_name,
            'primary_email' => $request->primary_email,
            'primary_phone' => $request->primary_phone,
            'primary_address' => $request->primary_address,

            'additional_travelers' => $additionalTravelers,

            'adult_count' => $adultCount,
            'child_count' => $childCount,
            'total_travelers' => $adultCount + $childCount,

            'package_name' => $request->package_name,
            'package_type' => $request->package_type,
            'price_per_person' => $pricePerPerson,

            'subtotal_price' => $subtotal,
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'final_price' => $finalPrice,

            'additional_details' => $request->additional_details,
        ]);

        // 5️⃣ Redirect to the invoice page
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
