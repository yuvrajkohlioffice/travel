<x-app-layout>
    <div class="ml-64 p-6 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="w-full">

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <span class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                            <i class="fa-solid fa-file-invoice"></i>
                        </span>
                        Invoices List
                    </h2>

                    <a href="{{ route('invoices.create') }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg flex items-center gap-2 transition shadow-sm hover:shadow-md">
                        <i class="fas fa-plus"></i> Create Invoice
                    </a>
                </div>

                <div class="p-6">
                    <table id="invoice-table" class="w-full text-sm text-left text-gray-500 display nowrap" style="width:100%">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Inv #</th>
                                <th class="px-4 py-3">User</th>
                                <th class="px-4 py-3">Package</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Travelers</th>
                                <th class="px-4 py-3">Dates</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('#invoice-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false, // Prevents layout jumping
                ajax: "{{ route('invoices.index') }}",
                order: [[0, 'desc']], // Default sort by Invoice No
                
                columns: [
                    { data: 'invoice_no', name: 'invoice_no' },
                    { data: 'user', name: 'lead.name' }, 
                    { data: 'package_name', name: 'package.package_name' },
                    { data: 'package_type', name: 'package_type', orderable: false }, // Matches new Controller column
                    { data: 'travelers', name: 'travelers', orderable: false, searchable: false },
                    { data: 'dates', name: 'dates', orderable: false, searchable: false },
                    { data: 'amount', name: 'amount', orderable: false, searchable: false },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                ],
                
                // Optional: Styling adjustments for Tailwind
                drawCallback: function() {
                    $('.dataTables_paginate > .paginate_button').addClass('px-3 py-1 border rounded mx-1 hover:bg-gray-100');
                }
            });
        });
    </script>
   
</x-app-layout>