<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-7xl">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-envelope-open-text text-blue-600"></i>
                        Message Templates
                    </h2>
                    <a href="{{ route('templates.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Template
                    </a>
                </div>

                <!-- Success -->
                @if (session('success'))
                    <div class="m-6 p-4 bg-green-500 text-white rounded flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <!-- Table -->
                <div class="p-6 overflow-x-auto">
                    <table class="table table-bordered w-full" id="templateTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Package</th>
                                <th>WhatsApp</th>
                                <th>Email</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>


    <script>
        $(function() {
            var table = $('#templateTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('templates.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'package_name',
                        name: 'package_name'
                    },
                    {
                        data: 'whatsapp_status',
                        name: 'whatsapp_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'email_status',
                        name: 'email_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Delete
            $(document).on('click', '.deleteBtn', function() {
                let id = $(this).data('id');
                if (confirm("Delete this template?")) {
                    $.ajax({
                        url: "/templates/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
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
