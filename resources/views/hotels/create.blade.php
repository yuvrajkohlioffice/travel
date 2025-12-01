<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-lg">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header with Back Button -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add Hotel</h2>
                    <a href="{{ route('hotels.index') }}"
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
                    <form action="{{ route('hotels.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Hotel Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Hotel Name</label>
                            <input type="text" name="name"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                                   focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                required>
                        </div>

                        <!-- Hotel Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Type</label>
                            <select name="type"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                   focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                required>
                                <option value="">Select Type</option>
                                <option value="3-star"
                                    {{ isset($hotel) && $hotel->type === '3-star' ? 'selected' : '' }}>3-star</option>
                                <option value="4-star"
                                    {{ isset($hotel) && $hotel->type === '4-star' ? 'selected' : '' }}>4-star</option>
                                <option value="5-star"
                                    {{ isset($hotel) && $hotel->type === '5-star' ? 'selected' : '' }}>5-star</option>
                                <option value="camp"
                                    {{ isset($hotel) && $hotel->type === 'camp' ? 'selected' : '' }}>Camp</option>
                                <option value="guest-house"
                                    {{ isset($hotel) && $hotel->type === 'guest-house' ? 'selected' : '' }}>Guest
                                    House</option>
                            </select>
                        </div>


                        <!-- Meal Included -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Meal
                                Included</label>
                            <select name="meal_included"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                                    focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                required>
                                <option value="">Select Option</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <!-- Meal Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Meal Type</label>
                            <input type="text" name="meal_type"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                                   focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Price</label>
                            <input type="number" step="0.01" name="price"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                                   focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                required>
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
