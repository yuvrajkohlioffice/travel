<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * Display a listing of the cars.
     */
    public function index()
    {
        $cars = Car::all();
        return view('cars.index', compact('cars'));
    }

    /**
     * Show the form for creating a new car.
     */
    public function create()
    {
        return view('cars.create');
    }

    /**
     * Store a newly created car in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'car_type' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'price_per_km' => 'required|numeric|min:0',
            'price_per_day' => 'required|numeric|min:0',
        ]);

        Car::create([
            'car_type' => $request->car_type,
            'capacity' => $request->capacity,
            'price_per_km' => $request->price_per_km,
            'price_per_day' => $request->price_per_day,
        ]);

        return redirect()->route('cars.index')->with('success', 'Car added successfully.');
    }

    /**
     * Display the specified car.
     */
    public function show(Car $car)
    {
        return view('cars.show', compact('car'));
    }

    /**
     * Show the form for editing the specified car.
     */
    public function edit(Car $car)
    {
        return view('cars.edit', compact('car'));
    }

    /**
     * Update the specified car in storage.
     */
    public function update(Request $request, Car $car)
    {
        $request->validate([
            'car_type' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'price_per_km' => 'required|numeric|min:0',
            'price_per_day' => 'required|numeric|min:0',
        ]);

        $car->update([
            'car_type' => $request->car_type,
            'capacity' => $request->capacity,
            'price_per_km' => $request->price_per_km,
            'price_per_day' => $request->price_per_day,
        ]);

        return redirect()->route('cars.index')->with('success', 'Car updated successfully.');
    }

    /**
     * Remove the specified car from storage.
     */
    public function destroy(Car $car)
    {
        $car->delete();
        return redirect()->route('cars.index')->with('success', 'Car deleted successfully.');
    }
}
