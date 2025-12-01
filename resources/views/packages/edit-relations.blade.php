<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-lg">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header with Back Button -->
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
                    <form action="{{ route('packages.update-relations', $package->id) }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Cars -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Select Cars</label>
                            <select name="cars[]" multiple
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                                    focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                @foreach($allCars as $car)
                                    <option value="{{ $car->id }}" {{ $package->cars->contains($car->id) ? 'selected' : '' }}>
                                        {{ $car->name }} - {{ $car->type }} (Capacity: {{ $car->capacity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Hotels -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Select Hotels</label>
                            <select name="hotels[]" multiple
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                                    focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                @foreach($allHotels as $hotel)
                                    <option value="{{ $hotel->id }}" {{ $package->hotels->contains($hotel->id) ? 'selected' : '' }}>
                                        {{ $hotel->name }} ({{ $hotel->type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Submit -->
                        <div>
                            <button type="submit"
                                    class="w-full px-4 py-3 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">
                                Save
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
w