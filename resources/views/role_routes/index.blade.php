<x-app-layout>
    <div x-data="{ table: null }" x-init="table = $('#roles-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('role_routes.index') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'routes', name: 'routes', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[1, 'asc']],
            createdRow: function (row, data, dataIndex) {
                // Tailwind badge for route count
                $(row).find('.badge').each(function () {
                    const $el = $(this);
                    if ($el.hasClass('bg-primary')) {
                        $el.removeClass('bg-primary').addClass('bg-blue-500 text-white px-2 py-1 rounded-full text-xs');
                    }
                    if ($el.hasClass('bg-secondary')) {
                        $el.removeClass('bg-secondary').addClass('bg-gray-400 text-white px-2 py-1 rounded-full text-xs');
                    }
                });

                // Tailwind action button
                $(row).find('.btn-warning').removeClass('btn-warning').addClass('bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-sm inline-flex items-center gap-1');
            }
        } )" x-cloak class="min-h-screen">

        <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
            <div class="w-full">

                {{-- Header --}}
                <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg p-3 bg-gray-100/60 dark:bg-gray-700/50">
                            <i class="fa-solid fa-user-shield text-gray-700 dark:text-gray-200 text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                                Roles with Route Status
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                View roles and the number of routes assigned to each.
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('role_routes.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow transition">
                        <i class="fa-solid fa-plus"></i>
                        <span class="hidden sm:inline">Assign Routes</span>
                    </a>
                </header>

                {{-- Table Card --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden">
                    <div class="p-6">
                        <table id="roles-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                        Role Name
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                        Assigned Routes
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                {{-- DataTables will populate rows --}}
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
