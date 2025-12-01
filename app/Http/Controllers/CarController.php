<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::all();
        return view('cars.index', compact('cars'));
    }

    public function create()
    {
        return view('cars.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'price_per_km' => 'required|numeric',
            'price_per_day' => 'required|numeric',
        ]);

        // Let Laravel handle JSON automatically
        Car::create([
            'name' => $request->name,
            'type' => $request->type,
            'capacity' => $request->capacity,
            'price' => [
                'per_km' => $request->price_per_km,
                'per_day' => $request->price_per_day,
            ],
        ]);

        return redirect()->route('cars.index')->with('success', 'Car added successfully.');
    }

    public function show(Car $car)
    {
        return view('cars.show', compact('car'));
    }

    public function edit(Car $car)
    {
        return view('cars.edit', compact('car'));
    }

    public function update(Request $request, Car $car)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'price_per_km' => 'required|numeric',
            'price_per_day' => 'required|numeric',
        ]);

        $car->update([
            'name' => $request->name,
            'type' => $request->type,
            'capacity' => $request->capacity,
            'price' => [
                'per_km' => $request->price_per_km,
                'per_day' => $request->price_per_day,
            ],
        ]);

        return redirect()->route('cars.index')->with('success', 'Car updated successfully.');
    }

    public function destroy(Car $car)
    {
        $car->delete();
        return redirect()->route('cars.index')->with('success', 'Car deleted successfully.');
    }
}
