<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-4xl">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                        Edit Cars & Hotels for {{ $package->package_name }}
                    </h2>
                    <a href="{{ route('packages.index') }}"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back
                    </a>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form action="{{ route('packages.update-relations', $package->id) }}" method="POST"
                        class="space-y-5">
                        @csrf

                        <div id="package-items" class="space-y-3">
                            @php $items = $package->packageItems ?? []; @endphp

                            @if (count($items) > 0)
                                @foreach ($items as $index => $item)
                                    <div
                                        class="package-item grid grid-cols-12 gap-2 items-center bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                        <!-- Car -->
                                        <div class="col-span-3">
                                            <label for="car_{{ $index }}"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-200">Car</label>
                                            <select id="car_{{ $index }}"
                                                name="items[{{ $index }}][car_id]"
                                                class="w-full border rounded px-2 py-1">
                                                <option value="">Select Car</option>
                                                @foreach ($allCars as $car)
                                                    <option value="{{ $car->id }}"
                                                        {{ $item->car_id == $car->id ? 'selected' : '' }}>
                                                        {{ $car->name }} - {{ $car->type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Hotel -->
                                        <div class="col-span-3">
                                            <label for="hotel_{{ $index }}"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-200">Hotel</label>
                                            <select id="hotel_{{ $index }}"
                                                name="items[{{ $index }}][hotel_id]"
                                                class="w-full border rounded px-2 py-1">
                                                <option value="">Select Hotel</option>
                                                @foreach ($allHotels as $hotel)
                                                    <option value="{{ $hotel->id }}"
                                                        {{ $item->hotel_id == $hotel->id ? 'selected' : '' }}>
                                                        {{ $hotel->name }} - {{ $hotel->type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Custom Price -->
                                        <div class="col-span-2">
                                            <label for="price_{{ $index }}"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-200">Per
                                                Person</label>
                                            <input id="price_{{ $index }}" type="number" step="0.01"
                                                name="items[{{ $index }}][custom_price]"
                                                value="{{ $item->custom_price ?? '' }}" placeholder="Price"
                                                class="w-full border rounded px-2 py-1">
                                        </div>

                                        <!-- Already Price -->
                                        <div class="col-span-2 flex items-center space-x-2">
                                            <input id="already_{{ $index }}" type="checkbox"
                                                name="items[{{ $index }}][already_price]" value="1"
                                                {{ $item->already_price ? 'checked' : '' }} class="w-4 h-4">
                                            <label for="already_{{ $index }}"
                                                class="text-gray-700 dark:text-gray-200 text-sm">Already Price</label>
                                        </div>

                                        <!-- Remove button -->
                                        <div class="col-span-2 flex justify-end">
                                            <button type="button" onclick="removeItem(this)"
                                                class="text-red-500 font-bold">✕</button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div
                                    class="package-item grid grid-cols-12 gap-2 items-center bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                    <!-- Car -->
                                    <div class="col-span-3">
                                        <label for="car_0"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Car</label>
                                        <select id="car_0" name="items[0][car_id]"
                                            class="w-full border rounded px-2 py-1">
                                            <option value="">Select Car</option>
                                            @foreach ($allCars as $car)
                                                <option value="{{ $car->id }}">{{ $car->name }} -
                                                    {{ $car->type }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Hotel -->
                                    <div class="col-span-3">
                                        <label for="hotel_0"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Hotel</label>
                                        <select id="hotel_0" name="items[0][hotel_id]"
                                            class="w-full border rounded px-2 py-1">
                                            <option value="">Select Hotel</option>
                                            @foreach ($allHotels as $hotel)
                                                <option value="{{ $hotel->id }}">{{ $hotel->name }} -
                                                    {{ $hotel->type }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Custom Price -->
                                    <div class="col-span-2">
                                        <label for="price_0"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Per
                                            Person</label>
                                        <input id="price_0" type="number" step="0.01"
                                            name="items[0][custom_price]" placeholder="Per Person Price"
                                            class="w-full border rounded px-2 py-1">
                                    </div>

                                    <!-- Already Price -->
                                    <div class="col-span-2 flex items-center space-x-2">
                                        <input id="already_0" type="checkbox" name="items[0][already_price]"
                                            value="1" class="w-4 h-4">
                                        <label for="already_0" class="text-gray-700 dark:text-gray-200 text-sm">Already
                                            Price</label>
                                    </div>

                                    <!-- Remove button -->
                                    <div class="col-span-2 flex justify-end">
                                        <button type="button" onclick="removeItem(this)"
                                            class="text-red-500 font-bold">✕</button>
                                    </div>
                                </div>
                            @endif

                        </div>

                        <button type="button" onclick="addItem()"
                            class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-green-700 transition">
                            Add Combination
                        </button>

                        <div class="mt-6">
                            <button type="submit"
                                class="w-full px-4 py-3 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">
                                Save
                            </button>
                        </div>
                    </form>
                    <!-- Load tables -->
<div class="mt-6 flex gap-4">
    <button type="button"
            class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700"
            @click="loadCars()">
        View All Cars
    </button>

    <button type="button"
            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
            @click="loadHotels()">
        View All Hotels
    </button>
</div>
<div x-data="detailsTable()" x-cloak>
    <!-- Car Table Modal -->
    <div x-show="carModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-3xl shadow-xl overflow-auto max-h-[90vh]">

            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">All Cars</h2>

            <table class="w-full text-left text-sm">
                <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="p-2">Name</th>
                        <th class="p-2">Type</th>
                        <th class="p-2">Capacity</th>
                        <th class="p-2">Price/km</th>
                        <th class="p-2">Price/day</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="car in cars" :key="car.id">
                        <tr class="border-b dark:border-gray-700">
                            <td class="p-2" x-text="car.name"></td>
                            <td class="p-2" x-text="car.type"></td>
                            <td class="p-2" x-text="car.capacity"></td>
                            <td class="p-2">₹<span x-text="car.price.per_km"></span></td>
                            <td class="p-2">₹<span x-text="car.price.per_day"></span></td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <button class="mt-4 px-4 py-2 bg-red-500 text-white rounded" 
                    @click="carModal = false">Close</button>
        </div>
    </div>

    <!-- Hotel Table Modal -->
    <div x-show="hotelModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-3xl shadow-xl overflow-auto max-h-[90vh]">

            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">All Hotels</h2>

            <table class="w-full text-left text-sm">
                <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="p-2">Name</th>
                        <th class="p-2">Type</th>
                        <th class="p-2">Meal Included</th>
                        <th class="p-2">Meal Type</th>
                        <th class="p-2">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="hotel in hotels" :key="hotel.id">
                        <tr class="border-b dark:border-gray-700">
                            <td class="p-2" x-text="hotel.name"></td>
                            <td class="p-2" x-text="hotel.type"></td>
                            <td class="p-2" x-text="hotel.meal_included ? 'Yes' : 'No'"></td>
                            <td class="p-2" x-text="hotel.meal_type"></td>
                            <td class="p-2">₹<span x-text="hotel.price"></span></td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <button class="mt-4 px-4 py-2 bg-red-500 text-white rounded"
                    @click="hotelModal = false">Close</button>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

                </div>
            </div>
        </div>
    </div>

    <script>
        let count = {{ count($items) }};

        function addItem() {
            let container = document.getElementById('package-items');
            let newItem = document.querySelector('.package-item').cloneNode(true);

            newItem.querySelectorAll('select, input').forEach(el => {
                let name = el.getAttribute('name').replace(/\d+/, count);
                el.setAttribute('name', name);

                if (el.type !== 'checkbox') el.value = '';
                else el.checked = false;
            });

            container.appendChild(newItem);
            count++;
        }

        function removeItem(el) {
            let row = el.closest('.package-item');
            row.remove();
        }
    </script>
    <script>
function detailsTable() {
    return {
        carModal: false,
        hotelModal: false,
        cars: [],
        hotels: [],

        async loadCars() {
            let res = await fetch('/api/cars');
            this.cars = await res.json();
            this.carModal = true;
        },

        async loadHotels() {
            let res = await fetch('/api/hotels');
            this.hotels = await res.json();
            this.hotelModal = true;
        }
    }
}
</script>

</x-app-layout>
