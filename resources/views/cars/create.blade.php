<x-app-layout>
    <div class="flex min-h-screen bg-gray-100 dark:bg-gray-900">

        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-gray-800 shadow-lg hidden md:block">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard</h1>
            </div>
            <nav class="mt-6">
                <ul>
                    <li>
                        <a href="{{ route('cars.index') }}"
                            class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"></path>
                            </svg>
                            Cars
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto p-6">
            <!-- Top Navigation -->
            <header class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Add Car</h2>
                <a href="{{ route('cars.index') }}"
                    class="flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back
                </a>
            </header>

            <!-- Form Card -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-4 p-4 text-red-700 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('cars.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Car Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Car Type</label>
                            <select name="car_type"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-800 shadow-sm
                                       focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                required>
                                <option value="" disabled selected>Select car type</option>
                                <option value="sedan">Sedan</option>
                                <option value="suv">SUV</option>
                                <option value="hatchback">Hatchback</option>
                                <option value="convertible">Convertible</option>
                                <option value="coupe">Coupe</option>
                                <option value="van">Van</option>
                            </select>
                        </div>

                        <!-- Capacity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Capacity</label>
                            <input type="number" name="capacity" min="1"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                                       focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                required>
                        </div>

                        <!-- Price per KM -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Price per KM</label>
                            <input type="number" step="0.01" name="price_per_km" min="0"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                                       focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                required>
                        </div>

                        <!-- Price per Day -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Price per Day</label>
                            <input type="number" step="0.01" name="price_per_day" min="0"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                                       focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                required>
                        </div>

                        <!-- Submit -->
                        <div>
                            <button type="submit"
                                class="w-full px-4 py-3 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">
                                Save Car
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
