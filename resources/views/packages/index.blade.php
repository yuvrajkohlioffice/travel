<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 via-white to-gray-200 ml-64 flex justify-center items-start min-h-screen p-6">

        <div class="w-full max-w-7xl space-y-6">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-2xl shadow-xl">

                <h2 class="text-3xl font-bold flex items-center gap-3">
                    <i class="fa-solid fa-box-open"></i> Packages
                </h2>

                <a href="{{ route('packages.create') }}"
                    class="px-5 py-2 bg-white text-blue-600 font-semibold rounded-lg shadow hover:bg-gray-100 transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Add Package
                </a>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="p-4 bg-green-500 text-white rounded-lg shadow">
                    <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Table -->
            <div class="bg-white rounded-xl shadow overflow-x-auto p-4">

                <x-data-table id="packages-table"
                    :headers="['ID', 'Name', 'Type', 'Category', 'Difficulty', 'Days', 'Nights', 'Price', 'Action']"
                    :excel="true" :print="true" title="Packages List" resourceName="Packages">

                    @foreach ($packages as $package)
                         <tr class="border-b hover:bg-gray-50 transition-colors">

                            <td class="p-3 text-center font-medium">{{ $package->id }}</td>

                            <td class="p-3 text-center font-medium">{{ $package->package_name }}</td>

                            <td class="p-3 text-center font-medium">{{ $package->packageType->name ?? '' }}</td>
                            <td class="p-3 text-center font-medium">{{ $package->packageCategory->name ?? '' }}</td>
                            <td class="p-3 text-center font-medium">{{ $package->difficultyType->name ?? '' }}</td>
                            
                            <td class="p-3">{{ $package->package_days }}</td>
                            <td class="p-3">{{ $package->package_nights }}</td>

                            <td class="p-3 font-semibold text-green-600 dark:text-green-400">
                                ₹{{ number_format($package->package_price) }}
                            </td>

                            <!-- Actions -->
                            <td class="p-3 flex items-center gap-3">

                                <!-- Edit -->
                                <a href="{{ route('packages.edit', $package->id) }}"
                                    class="btn-edit px-3 py-1 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 shadow flex items-center gap-1">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>

                                <!-- View -->
                                <a href="{{ route('packages.show', $package->id) }}"
                                    class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow flex items-center gap-1">
                                    <i class="fa-solid fa-eye"></i> Show
                                </a>

                                <!-- Edit Relations -->
                                <a href="{{ route('packages.edit-relations', $package->id) }}"
                                    class="px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow flex items-center gap-1">
                                    <i class="fa-solid fa-link"></i> Relations
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('packages.destroy', $package->id) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Delete this package?')">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow flex items-center gap-1">
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
</x-app-layout>
