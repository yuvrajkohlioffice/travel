<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">

        <div class="w-full max-w-7xl">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-box-open text-blue-600"></i>
                        Packages
                    </h2>

                    <a href="{{ route('packages.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Add Package
                    </a>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="m-6 p-4 bg-green-500 text-white rounded-lg shadow">
                        <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                    </div>
                @endif

                <!-- Data Table -->
                <div class="p-6 overflow-x-auto">

                    <x-data-table id="packages-table" :headers="['ID', 'Name', 'Type', 'Category', 'Difficulty', 'Days', 'Nights', 'Price', 'Action']" :excel="true" :print="true"
                        title="Packages List" resourceName="Packages">
                        @foreach ($packages as $package)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                <td class="text-center font-semibold">{{ $package->id }}</td>
                                <td class="font-medium">{{ $package->package_name }}</td>
                                <td>{{ $package->packageType->name ?? '' }}</td>
                                <td>{{ $package->packageCategory->name ?? '' }}</td>
                                <td>{{ $package->difficultyType->name ?? '' }}</td>
                                <td>{{ $package->package_days }}</td>
                                <td>{{ $package->package_nights }}</td>
                                <td class="font-semibold text-green-600 dark:text-green-400">
                                    ₹{{ number_format($package->package_price) }}
                                </td>

                                <td class="text-center space-x-2">

                                    <!-- Edit -->
                                    <a href="{{ route('packages.edit', $package->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-500 text-white 
                                              rounded-lg hover:bg-yellow-600 shadow-sm">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>

                                    <!-- Show -->
                                    <a href="{{ route('packages.show', $package->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500 text-white 
                                              rounded-lg hover:bg-blue-600 shadow-sm">
                                        <i class="fa-solid fa-eye"></i> Show
                                    </a>
                                    <a href="{{ route('packages.edit-relations', $package->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-500 text-white 
              rounded-lg hover:bg-indigo-600 shadow-sm">
                                        <i class="fa-solid fa-link"></i> Edit Relations
                                    </a>
                                    <!-- Delete -->
                                    <form action="{{ route('packages.destroy', $package->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Delete this package?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-red-600 text-white 
                                                   rounded-lg hover:bg-red-700 shadow-sm">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </x-data-table>

                </div>

            </div>
        </div>

    </div>
</x-app-layout>
