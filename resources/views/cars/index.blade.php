<x-app-layout>
    <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">

        <div class="w-full">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-car text-blue-600"></i> Cars
                </h2>

                <a href="{{ route('cars.create') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Car
                </a>
            </div>

            <!-- Success -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-500 text-white rounded-lg flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <!-- DataTable Container -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="p-4 overflow-x-auto">

                    <table id="cars-table" class="min-w-full">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Capacity</th>
                                <th>Price/KM</th>
                                <th>Price/Day</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>

        </div>

    </div>

    <!-- DataTables Script -->
    <script>
        $(function () {
            $('#cars-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('cars.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'car_type', name: 'car_type' },
                    { data: 'capacity', name: 'capacity' },
                    { data: 'price_per_km', name: 'price_per_km' },
                    { data: 'price_per_day', name: 'price_per_day' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false },
                ]
            });
        });
    </script>

</x-app-layout>
