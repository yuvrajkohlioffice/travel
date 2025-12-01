<?php

namespace App\Http\Controllers;

use App\Models\PickupPoint;
use Illuminate\Http\Request;

class PickupPointController extends Controller
{
    // List all pickup points
    public function index()
    {
        $pickupPoints = PickupPoint::all();
        return view('pickup-points.index', compact('pickupPoints'));
    }

    // Show create form
    public function create()
    {
        return view('pickup-points.create');
    }

    // Store new pickup point
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        PickupPoint::create($request->only('name'));

        return redirect()->route('pickup-points.index')
            ->with('success', 'Pickup Point created successfully!');
    }

    // Edit form
    public function edit(PickupPoint $pickupPoint)
    {
        return view('pickup-points.edit', compact('pickupPoint'));
    }

    // Update pickup point
    public function update(Request $request, PickupPoint $pickupPoint)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $pickupPoint->update($request->only('name'));

        return redirect()->route('pickup-points.index')
            ->with('success', 'Pickup Point updated successfully!');
    }

    // Delete
    public function destroy(PickupPoint $pickupPoint)
    {
        $pickupPoint->delete();

        return redirect()->route('pickup-points.index')
            ->with('success', 'Pickup Point deleted successfully!');
    }
}
