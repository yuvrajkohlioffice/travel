<x-app-layout>
    <div class="flex flex-col md:flex-row min-h-screen ">

        <!-- Sidebar -->
        <aside class="hidden md:flex md:flex-col w-64 bg-white dark:bg-gray-800 shadow-lg">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard</h1>
            </div>
            <nav class="mt-6 flex-1">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('cars.index') }}"
                           class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-car mr-3 w-5 text-center"></i>
                            Cars
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-auto">
            <div class="max-w-7xl mx-auto">

                <!-- Header / Actions -->
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-3 md:space-y-0">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Cars</h2>
                    <a href="{{ route('cars.create') }}"
   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition-colors">
    <i class="fas fa-plus mr-2"></i> Add Car
</a>

                </div>

                <!-- Notifications -->
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-500 text-white rounded shadow">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Cars DataTable -->
                <div class=" dark:bg-gray-800  rounded-lg overflow-hidden">
                    <div class="p-4 overflow-x-auto">
                        <x-data-table
                            id="cars-table"
                            :headers="['ID','Name', 'Type', 'Capacity', 'Price/KM', 'Price/Day', 'Actions']"
                            :excel="true"
                            :print="true"
                            title="Cars List"
                            resourceName="Cars"
                        >
                            @foreach($cars as $car)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="text-center">{{ $car->id }}</td>
                                    <td>{{ $car->name }}</td>
                                    <td>{{ $car->type }}</td>
                                    <td>{{ $car->capacity }}</td>
                                    <td>{{ $car->price_per_km }}</td>
                                    <td>{{ $car->price_per_day }}</td>
                                    <td class="text-center space-x-2">

                                        <!-- Edit Button -->
                                        <a href="{{ route('cars.edit', $car->id) }}"
                                           class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>

                                        <!-- Delete Button -->
                                        <form class="inline" action="{{ route('cars.destroy', $car->id) }}" method="POST" onsubmit="return confirm('Delete this car?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        </x-data-table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Add Car Modal -->
  
</x-app-layout>
