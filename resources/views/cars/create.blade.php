<x-app-layout>
    <div class="flex h-screen ">

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
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"></path>
                            </svg>
                            Cars
                        </a>
                    </li>
                    <!-- Add more sidebar links -->
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

            <!-- Card Container -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Form -->
                <div class="p-6">
                    <form action="{{ route('cars.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Car Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Car Name</label>
                            <input type="text" name="name" 
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                                          focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                   required>
                        </div>

                        <!-- Car Type -->
                        <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Car Type</label>
    <select name="type" 
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
        <!-- Add more options as needed -->
    </select>
</div>


                        <!-- Capacity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Capacity</label>
                            <input type="number" name="capacity" 
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                                          focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                   required>
                        </div>

                        <!-- Price per KM -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Price per KM</label>
                            <input type="number" step="0.01" name="price_per_km" 
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm
                                          focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                   required>
                        </div>

                        <!-- Price per Day -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Price per Day</label>
                            <input type="number" step="0.01" name="price_per_day" 
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

            <!-- Filters / Pagination Placeholder -->
            
        </div>
    </div>
</x-app-layout>
