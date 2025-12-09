@php
    // Determine if this is a single item edit or a bulk add
    $isEdit = isset($item);
    $index = $index ?? 0; // used for bulk insert
@endphp

<div class="border p-5 rounded-xl bg-gray-50 dark:bg-gray-800 shadow-sm" data-item-id="{{ $item->id ?? '' }}">

    <h3 class="font-semibold text-lg mb-4 text-gray-800 dark:text-gray-200">
        @if($isEdit)
            Edit Item
        @else
            Item #{{ $index + 1 }}
        @endif
    </h3>

    {{-- Select Car --}}
    <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Select Car
    </label>
    <select 
        name="{{ $isEdit ? 'car_id' : "items[$index][car_id]" }}" 
        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm text-gray-800 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        
        <option value="" disabled selected>Select a car</option>

        @foreach ($allCars as $car)
            <option value="{{ $car->id }}" 
                @if($isEdit)
                    @selected($item->car_id == $car->id)
                @else
                    @selected(isset($item) && $item->car_id == $car->id)
                @endif>
                {{ $car->car_type }} 
                | Capacity: {{ $car->capacity }} 
                | Per Day: ₹{{ number_format($car->price_per_day, 2) }} 
                | Per KM: ₹{{ number_format($car->price_per_km, 2) }}
            </option>
        @endforeach
    </select>
</div>


    {{-- Person, Vehicle, Room --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Person Count
            </label>
            <input type="number" name="{{ $isEdit ? 'person_count' : "items[$index][person_count]" }}"
                   value="{{ $item->person_count ?? '' }}"
                   class="p-2 border w-full rounded-lg dark:bg-gray-700 dark:text-white">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Vehicle Name
            </label>
            <input type="text" name="{{ $isEdit ? 'vehicle_name' : "items[$index][vehicle_name]" }}"
                   value="{{ $item->vehicle_name ?? '' }}"
                   class="p-2 border w-full rounded-lg dark:bg-gray-700 dark:text-white">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Room Count
            </label>
            <input type="number" name="{{ $isEdit ? 'room_count' : "items[$index][room_count]" }}"
                   value="{{ $item->room_count ?? '' }}"
                   class="p-2 border w-full rounded-lg dark:bg-gray-700 dark:text-white">
        </div>

    </div>

    {{-- Price Fields --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-4">

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Standard Price
            </label>
            <input type="number" step="0.01"
                   name="{{ $isEdit ? 'standard_price' : "items[$index][standard_price]" }}"
                   value="{{ $item->standard_price ?? '' }}"
                   class="p-2 border w-full rounded-lg dark:bg-gray-700 dark:text-white">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Deluxe Price
            </label>
            <input type="number" step="0.01"
                   name="{{ $isEdit ? 'deluxe_price' : "items[$index][deluxe_price]" }}"
                   value="{{ $item->deluxe_price ?? '' }}"
                   class="p-2 border w-full rounded-lg dark:bg-gray-700 dark:text-white">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Luxury Price
            </label>
            <input type="number" step="0.01"
                   name="{{ $isEdit ? 'luxury_price' : "items[$index][luxury_price]" }}"
                   value="{{ $item->luxury_price ?? '' }}"
                   class="p-2 border w-full rounded-lg dark:bg-gray-700 dark:text-white">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Premium Price
            </label>
            <input type="number" step="0.01"
                   name="{{ $isEdit ? 'premium_price' : "items[$index][premium_price]" }}"
                   value="{{ $item->premium_price ?? '' }}"
                   class="p-2 border w-full rounded-lg dark:bg-gray-700 dark:text-white">
        </div>

    </div>

</div>
