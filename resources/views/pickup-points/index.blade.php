<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-7xl">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Pickup Points</h2>

                    <a href="{{ route('pickup-points.create') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Add Pickup Point
                    </a>
                </div>

                <!-- Success message -->
                @if(session('success'))
                    <div class="m-6 p-4 bg-green-500 text-white rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Data Table Component -->
                <div class="p-6 overflow-x-auto">
                    <x-data-table 
                        id="pickup-points-table"
                        :headers="['ID', 'Name', 'Action']"
                        :excel="true"
                        :print="true"
                        title="Pickup Points List"
                        resourceName="Pickup Points"
                    >
                        @foreach ($pickupPoints as $point)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="text-center">{{ $point->id }}</td>
                                <td>{{ $point->name }}</td>

                                <td class="text-center space-x-2">

                                    <!-- Edit -->
                                    <a href="{{ route('pickup-points.edit', $point->id) }}"
                                       class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                        Edit
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('pickup-points.destroy', $point->id) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Delete this pickup point?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                            Delete
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
