<x-app-layout>
    <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">

        <div class="w-full ">

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-building text-green-600"></i>
                        Companies
                    </h2>

                    <a href="{{ route('companies.create') }}"
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Company
                    </a>
                </div>

                <!-- Success -->
                @if (session('success'))
                    <div class="m-6 p-4 bg-green-500 text-white rounded flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <!-- DataTable -->
                <div class="p-6 overflow-x-auto">
                    <table id="companies-table" class="min-w-full">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Company Name</th>
                                <th>Owner</th>
                                <th>Team</th>
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
            $('#companies-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('companies.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'owner', name: 'owner', orderable: false },
                    { data: 'team', name: 'team', orderable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false },
                ]
            });
        });
    </script>

</x-app-layout>
