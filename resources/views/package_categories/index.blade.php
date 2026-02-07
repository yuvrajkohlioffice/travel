<x-app-layout>
    <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full">

            {{-- Header / Title + Actions --}}
            <header
                class="mb-3 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700
                p-5 md:p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">

                <div class="flex items-center gap-4">
                    <div class="rounded-lg p-3 bg-gray-100/60">
                        <i class="fa-solid fa-layer-group text-gray-700 text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                            Package Categories
                        </h1>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Manage all package categories — create, edit, and delete.
                        </p>
                    </div>
                </div>

                <a href="{{ route('package-categories.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-800 font-medium rounded-lg 
                    shadow-sm border border-gray-200 hover:shadow-md hover:bg-gray-50 transition">
                    <i class="fa-solid fa-plus"></i>
                    <span class="hidden sm:inline">Add Category</span>
                </a>
            </header>

            {{-- Success Message --}}
            @if (session('success'))
                <div
                    class="flex items-start gap-3 bg-green-50 border border-green-100 rounded-lg p-4 text-sm md:text-base shadow-sm mb-4">
                    <div class="text-green-600 mt-0.5">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="text-gray-800">{{ session('success') }}</div>
                    <button onclick="this.parentElement.remove()"
                        class="ml-auto text-gray-400 hover:text-gray-600 focus:outline-none"
                        aria-label="Dismiss">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            {{-- Table Card --}}
            <section class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="p-4 md:p-6">
                    <div class="overflow-x-auto">
                           <table id="categories-table"  class="min-w-full border border-gray-300 rounded-lg">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="p-4 text-sm font-semibold text-gray-600">ID</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Category Name</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Company / Type</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
            </table>
                    </div>
                </div>
            </section>

        </div>
    </div>

    {{-- DataTable Script --}}
   <script>
        $(document).ready(function() {
            $('#categories-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('package-categories.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                // Tailwind styling for DataTable
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search categories...",
                }
            });
        });
    </script>
</x-app-layout>
