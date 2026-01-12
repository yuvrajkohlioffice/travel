<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Invoice;
use App\Models\Package;
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
        // Security Check
        if (!Session::has('client_access_' . $lead_id)) {
            return redirect()->route('guest.login', ['lead_id' => $lead_id]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            // Add other fields you want them to fill (e.g., passport)
        ]);

        $lead = Lead::findOrFail($lead_id);

        // Update Lead Info
        $lead->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            // Add other fields here
        ]);

        return back()->with('success', 'Details updated successfully!');
    }
}