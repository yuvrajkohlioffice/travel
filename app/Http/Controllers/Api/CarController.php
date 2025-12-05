<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;

class CarController extends Controller
{
    public function index()
    {
        // Fetch all cars
        $cars = Car::select('id', 'name', 'capacity', 'type', 'price')->get();

        return response()->json([
            'success' => true,
            'data' => $cars
        ]);
    }
}
