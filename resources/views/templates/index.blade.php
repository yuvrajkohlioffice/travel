<x-app-layout>
    <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full ">

            {{-- Header --}}
            <header
                class="mb-3 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700
                p-5 md:p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">

                <div class="flex items-center gap-4">
                    <div class="rounded-lg p-3 bg-gray-100/60">
                        <i class="fas fa-envelope-open-text text-blue-600 text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                            Message Templates
                        </h1>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Manage all message templates — WhatsApp and Email — create, edit, or delete.
                        </p>
                    </div>
                </div>

                <a href="{{ route('templates.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-800 font-medium rounded-lg 
                    shadow-sm border border-gray-200 hover:shadow-md hover:bg-gray-50 transition">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Add Template</span>
                </a>
            </header>

            {{-- Success --}}
            @if(session('success'))
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
                        <table id="templateTable" class="min-w-full border border-gray-300 rounded-lg">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-center">ID</th>
                                    <th class="px-4 py-2 text-left">Package</th>
                                    <th class="px-4 py-2 text-center">WhatsApp</th>
                                    <th class="px-4 py-2 text-center">Email</th>
                                    <th class="px-4 py-2 text-center">Created</th>
                                    <th class="px-4 py-2 text-center">Action</th>
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
        $(function() {
            var table = $('#templateTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('templates.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'package_name', name: 'package_name' },
                    { data: 'whatsapp_status', name: 'whatsapp_status', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'email_status', name: 'email_status', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'created_at', name: 'created_at', className: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                order: [[0, 'desc']],
            });

            // Delete via AJAX
            $(document).on('click', '.deleteBtn', function() {
                let id = $(this).data('id');
                if (confirm("Delete this template?")) {
                    $.ajax({
                        url: "/templates/" + id,
                        type: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(res) {
                            alert(res.message);
                            table.ajax.reload();
                        }
                    });
                }
            });
        });
    </script>
</x-app-layout>
