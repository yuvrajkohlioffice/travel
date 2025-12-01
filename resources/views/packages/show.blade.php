<x-app-layout>
    <div class="ml-64 p-6 flex justify-center">
        <div class="w-full max-w-5xl bg-white dark:bg-gray-800 rounded shadow-lg p-6">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $package->package_name }}</h2>
                <a href="{{ route('packages.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    ← Back
                </a>
            </div>

            <!-- Banner Image -->
            @if($package->package_banner)
                <div class="mb-6">
                    <img src="{{ asset('storage/'.$package->package_banner) }}" alt="Banner" class="w-full h-64 object-cover rounded shadow">
                </div>
            @endif

            <!-- Package Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Type:</span> {{ $package->packageType->name ?? '-' }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Category:</span> {{ $package->packageCategory->name ?? '-' }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Difficulty:</span> {{ $package->difficultyType->name ?? '-' }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Days / Nights:</span> {{ $package->package_days }} / {{ $package->package_nights }}</p>
                </div>
                <div>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Price:</span> ${{ number_format($package->package_price, 2) }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Pickup Points:</span> {{ $package->pickup_points ?? '-' }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Altitude:</span> {{ $package->altitude ?? '-' }}</p>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Age Range:</span> {{ $package->min_age ?? '-' }} - {{ $package->max_age ?? '-' }}</p>
                </div>
            </div>

            <!-- Best Time to Visit -->
            @if($package->best_time_to_visit)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Best Time to Visit</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $package->best_time_to_visit }}</p>
                </div>
            @endif

            <!-- Package Docs -->
            @if($package->package_docs)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Documents</h3>
                    <a href="{{ asset('storage/'.$package->package_docs) }}" target="_blank" class="text-blue-600 underline">View Document</a>
                </div>
            @endif

            <!-- Other Images -->
            @if($package->other_images && count($package->other_images) > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Gallery</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($package->other_images as $image)
                            <img src="{{ asset('storage/'.$image) }}" class="h-32 w-32 object-cover rounded shadow" alt="Image">
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Content -->
            @if($package->content)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Content</h3>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! $package->content !!}
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
