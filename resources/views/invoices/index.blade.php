<x-app-layout>
    <div class="ml-64 p-6 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="w-full">

            <!-- Card -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        <i class="fa-solid fa-file-invoice text-blue-600"></i> Invoices
                    </h2>

                    <a href="{{ route('invoices.create') }}"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Invoice
                    </a>
                </div>

                <!-- Table -->
                <div class="p-6">
                    <table id="invoice-table" class="min-w-full display nowrap">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>User</th>
                                <th>Package</th>
                                <th>Package Type</th>
                                <th>Travelers</th>
                                <th>Dates</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>

        </div>
    </div>

<script>
$(function () {

    $('#invoice-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('invoices.index') }}",

        columns: [
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'user', name: 'lead.name' },
            { data: 'package_name', name: 'package.package_name' },
            { data: 'package_type', name: 'package_type' },
            { data: 'travelers', orderable: false, searchable: false },
            { data: 'dates', orderable: false, searchable: false },
            { data: 'amount', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false },
        ]
    });

});
</script>

</x-app-layout>
