<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Invoice;
use Illuminate\Support\Facades\Session;

class GuestInvoiceController extends Controller
{
    /**
     * 1. Show the Password/Login Screen
     */
    public function showLogin($lead_id)
    {
        // If already logged in, skip to form
        if (Session::has('client_access_' . $lead_id)) {
            return redirect()->route('guest.form', ['lead_id' => $lead_id]);
        }

        $lead = Lead::findOrFail($lead_id);
        
        // Return the login view (you need to create this)
        return view('guest_portal.login', compact('lead'));
    }

    /**
     * 2. Verify the Password (Phone Number)
     */
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|integer',
            'password' => 'required|string', // The user enters their Phone No here
        ]);

        $lead = Lead::findOrFail($request->lead_id);

        // CHECK: Is the password the Lead's Phone Number?
        // You can change '$lead->phone' to any specific password field you want
        if ($request->password == $lead->phone_number) {
            
            // Success: Store permission in session
            Session::put('client_access_' . $lead->id, true);

            return redirect()->route('guest.form', ['lead_id' => $lead->id]);
        }

        // Fail
        return back()->withErrors(['password' => 'Incorrect Mobile Number/Password.']);
    }

    /**
     * 3. Show the Form (Protected)
     */
    public function showForm($lead_id)
    {
        // Security Check
        if (!Session::has('client_access_' . $lead_id)) {
            return redirect()->route('guest.login', ['lead_id' => $lead_id])
                ->with('error', 'Please login first.');
        }

        // Bind Data
        $lead = Lead::with('package')->findOrFail($lead_id);
        
        // Check for existing invoice
        $invoice = Invoice::where('lead_id', $lead_id)->latest()->first();

        // Get Package Items
        $package = $lead->package;
        $packageItems = $package ? $package->packageItems : collect();

        // Return the form view
        return view('guest_portal.form', [
            'lead' => $lead,
            'invoice' => $invoice,
            'package' => $package,
            'packageItems' => $packageItems
        ]);
    }

    /**
     * 4. Update User Details
     */
     public function updateDetails(Request $request, $lead_id)
    {
        // 1. Security Check
        if (!Session::has('client_access_' . $lead_id)) {
            return redirect()->route('guest.login', ['lead_id' => $lead_id])
                ->with('error', 'Session expired. Please login again.');
        }

        // 2. Validate
        $request->validate([
            'primary_full_name' => 'required|string|max:255',
            'primary_email'     => 'required|email|max:255',
            'primary_phone'     => 'required|string|max:20',
            'primary_address'   => 'required|string|max:500',
            'additional_travelers' => 'nullable|json', // Expecting a JSON string here
            'notes'             => 'nullable|string',
        ]);

        // 3. Update Lead (Basic Info)
        $lead = Lead::findOrFail($lead_id);
        $lead->update([
            'name'    => $request->primary_full_name,
            'email'   => $request->primary_email,
            'phone_number' => $request->primary_phone,
            'address' => $request->primary_address,
            'notes'   => $request->notes
        ]);

        // 4. Update Invoice (Detailed Info & Travelers)
        $invoice = Invoice::where('lead_id', $lead_id)->latest()->first();

        if ($invoice) {
            
            // Decode JSON to calculate counts if needed
            $travelersArray = json_decode($request->additional_travelers, true) ?? [];
            
            // Optional: Recalculate counts based on input (or keep existing)
            // $adultCount = ... logic to count adults in array ...
            
            $invoice->update([
                'primary_full_name' => $request->primary_full_name,
                'primary_email'     => $request->primary_email,
                'primary_phone'     => $request->primary_phone,
                'primary_address'   => $request->primary_address,
                'additional_details'=> $request->notes,
                
                // Save the JSON directly to the column
                'additional_travelers' => $travelersArray, 
            ]);
        }

        return back()->with('success', 'Details updated successfully!');
    }
}