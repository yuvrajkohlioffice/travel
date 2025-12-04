<div class="border p-4 rounded-xl bg-gray-50">
    <h3 class="font-semibold mb-3">Item #{{ $index + 1 }}</h3>

    {{-- Car --}}
    <label class="block text-sm font-medium">Select Car</label>
    <select name="items[{{ $index }}][car_id]"
            class="w-full p-2 border rounded-lg">
        @foreach ($allCars as $car)
            <option value="{{ $car->id }}"
                @selected(isset($item) && $item->car_id == $car->id)>
                {{ $car->name }}
            </option>
        @endforeach
    </select>

    <div class="grid grid-cols-3 gap-4 mt-4">

        <div>
            <label class="block text-sm font-medium">Person Count</label>
            <input type="number" name="items[{{ $index }}][person_count]"
                   value="{{ $item->person_count ?? '' }}"
                   class="p-2 border w-full rounded-lg">
        </div>

        <div>
            <label class="block text-sm font-medium">Vehicle Name</label>
            <input type="text" name="items[{{ $index }}][vehicle_name]"
                   value="{{ $item->vehicle_name ?? '' }}"
                   class="p-2 border w-full rounded-lg">
        </div>

        <div>
            <label class="block text-sm font-medium">Room Count</label>
            <input type="number" name="items[{{ $index }}][room_count]"
                   value="{{ $item->room_count ?? '' }}"
                   class="p-2 border w-full rounded-lg">
        </div>

    </div>

    <!-- Price fields -->
    <div class="grid grid-cols-4 gap-4 mt-4">

        <div>
            <label class="block text-sm font-medium">Standard Price</label>
            <input type="number" step="0.01"
                   name="items[{{ $index }}][standard_price]"
                   value="{{ $item->standard_price ?? '' }}"
                   class="p-2 border w-full rounded-lg">
        </div>

        <div>
            <label class="block text-sm font-medium">Deluxe Price</label>
            <input type="number" step="0.01"
                   name="items[{{ $index }}][deluxe_price]"
                   value="{{ $item->deluxe_price ?? '' }}"
                   class="p-2 border w-full rounded-lg">
        </div>

        <div>
            <label class="block text-sm font-medium">Luxury Price</label>
            <input type="number" step="0.01"
                   name="items[{{ $index }}][luxury_price]"
                   value="{{ $item->luxury_price ?? '' }}"
                   class="p-2 border w-full rounded-lg">
        </div>

        <div>
            <label class="block text-sm font-medium">Premium Price</label>
            <input type="number" step="0.01"
                   name="items[{{ $index }}][premium_price]"
                   value="{{ $item->premium_price ?? '' }}"
                   class="p-2 border w-full rounded-lg">
        </div>

    </div>
</div>
