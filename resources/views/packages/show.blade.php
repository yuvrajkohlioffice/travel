<x-app-layout>
    <div class="ml-64 p-6 flex justify-center">
        <div class="w-full max-w-5xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 space-y-6">

            <!-- Header -->
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $package->package_name }}</h2>
                <a href="{{ route('packages.index') }}" 
                   class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                   ← Back
                </a>
            </div>

            <!-- Banner Image -->
            @if($package->package_banner)
                <div class="rounded overflow-hidden shadow mb-6">
                    <img src="{{ asset('storage/'.$package->package_banner) }}" 
                         alt="Banner" class="w-full h-64 object-cover">
                </div>
            @endif

            <!-- Package Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-2">
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Type:</span> {{ $package->packageType->name ?? '-' }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Category:</span> {{ $package->packageCategory->name ?? '-' }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Difficulty:</span> {{ $package->difficultyType->name ?? '-' }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Days / Nights:</span> {{ $package->package_days }} / {{ $package->package_nights }}</p>
                </div>
                <div class="space-y-2">
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Price:</span> ${{ number_format($package->package_price, 2) }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Pickup Points:</span> {{ $package->pickup_points ?? '-' }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Altitude:</span> {{ $package->altitude ?? '-' }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Age Range:</span> {{ $package->min_age ?? '-' }} - {{ $package->max_age ?? '-' }}</p>
                </div>
            </div>

            <!-- Best Time to Visit -->
            @if($package->best_time_to_visit)
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded shadow">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Best Time to Visit</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $package->best_time_to_visit }}</p>
                </div>
            @endif

            <!-- Documents -->
            @if($package->package_docs)
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded shadow">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Documents</h3>
                    <a href="{{ asset('storage/'.$package->package_docs) }}" target="_blank" class="text-blue-600 underline">
                        View Document
                    </a>
                </div>
            @endif

            <!-- Gallery -->
            @if($package->other_images && count($package->other_images) > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Gallery</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($package->other_images as $image)
                            <img src="{{ asset('storage/'.$image) }}" 
                                 class="h-32 w-full object-cover rounded shadow" alt="Image">
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Content -->
            @if($package->content)
                <div class="prose dark:prose-invert max-w-none mb-6">
                    {!! $package->content !!}
                </div>
            @endif

            <!-- Total Price -->
            <div class="text-right text-xl font-bold text-gray-800 dark:text-white mb-4">
                Total Price: $<span id="total-price">{{ number_format($package->package_price, 2) }}</span>
            </div>

            <!-- Package Items -->
            @if($package->packageItems->count() > 0)
                <div class="space-y-4">
                    @foreach($package->packageItems as $index => $item)
                        <div class="grid grid-cols-12 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded shadow items-center">
                            <!-- Car -->
                            <div class="col-span-12 md:col-span-3 space-y-1">
                                <p class="font-semibold text-gray-700 dark:text-gray-200">Car</p>
                                <p class="text-gray-700 dark:text-gray-200">{{ $item->car->name ?? '-' }} - {{ $item->car->type ?? '-' }}</p>
                            </div>

                            <!-- Hotel -->
                            <div class="col-span-12 md:col-span-3 space-y-1">
                                <p class="font-semibold text-gray-700 dark:text-gray-200">Hotel</p>
                                <p class="text-gray-700 dark:text-gray-200">{{ $item->hotel->name ?? '-' }} - {{ $item->hotel->type ?? '-' }}</p>
                            </div>

                            <!-- Custom Price -->
                            <div class="col-span-12 md:col-span-2 space-y-1">
                                <label for="price_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Add-On Price
                                </label>
                                <span id="price_{{ $index }}" class="text-gray-700 dark:text-gray-200 font-semibold">
                                    ${{ number_format($item->custom_price, 2) }}
                                </span>
                            </div>

                            <!-- Checkbox -->
                            <div class="col-span-12 md:col-span-4 flex items-center space-x-2">
    <input type="checkbox" id="already_{{ $index }}" 
           class="w-4 h-4 addon-checkbox" 
           data-price="{{ $item->custom_price }}" 
           {{ $item->already_price ? 'checked' : '' }}>
    <label for="already_{{ $index }}" class="text-gray-700 dark:text-gray-200 text-sm">
        Include Add-On
    </label>
</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-700 dark:text-gray-200">No add-on items available.</p>
            @endif

        </div>
    </div>

    <script>
      const basePrice = parseFloat(@json($package->package_price));
const totalPriceEl = document.getElementById('total-price');
const checkboxes = document.querySelectorAll('.addon-checkbox');

function calculateTotal() {
    let total = basePrice;
    checkboxes.forEach(cb => {
        if (cb.checked) {
            total += parseFloat(cb.dataset.price);
        }
    });
    totalPriceEl.textContent = total.toFixed(2);
}

// Only allow one checkbox to be selected at a time
checkboxes.forEach(cb => {
    cb.addEventListener('change', () => {
        if (cb.checked) {
            checkboxes.forEach(otherCb => {
                if (otherCb !== cb) otherCb.checked = false;
            });
        }
        calculateTotal();
    });
});

// Initial total calculation
calculateTotal();

    </script>
</x-app-layout>
