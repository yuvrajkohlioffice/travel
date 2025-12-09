<x-app-layout>
    <div class="ml-64 p-6 bg-gray-100 dark:bg-gray-900 min-h-screen flex justify-center items-start">
        <div class="w-full max-w-7xl">

            <!-- Card Wrapper -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice text-blue-600"></i> Invoices
                    </h2>

                    <a href="{{ route('invoices.create') }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Invoice
                    </a>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="m-6 p-4 bg-green-500 text-white rounded flex items-center gap-2 shadow">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <!-- FILTER PANEL -->
                <div
                    class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 p-4 flex flex-wrap gap-3 items-center">

                    <!-- Invoice ID -->
                    <input id="filter-invoice-id" type="text" placeholder="Invoice ID"
                        class="border px-4 py-2 rounded-lg text-sm w-40">

                    <!-- Name -->
                    <input id="filter-name" type="text" placeholder="Search Name"
                        class="border px-4 py-2 rounded-lg text-sm w-40">

                    <!-- Email -->
                    <input id="filter-email" type="text" placeholder="Search Email"
                        class="border px-4 py-2 rounded-lg text-sm w-40">

                    <!-- Package -->
                    <input id="filter-package" type="text" placeholder="Search Package"
                        class="border px-4 py-2 rounded-lg text-sm w-40">

                    <!-- DATE RANGE BUTTONS -->
                    <div class="flex gap-2">
                        <button class="date-range-btn px-4 py-2 rounded-lg border text-sm" data-value="today">
                            Today
                        </button>
                        <button class="date-range-btn px-4 py-2 rounded-lg border text-sm" data-value="week">
                            This Week
                        </button>
                        <button class="date-range-btn px-4 py-2 rounded-lg border text-sm" data-value="month">
                            This Month
                        </button>
                        <button class="date-range-btn px-4 py-2 rounded-lg border text-sm" data-value="year">
                            This Year
                        </button>
                    </div>

                    <!-- Status -->
                    <select id="filter-status" class="border px-4 py-2 rounded-lg text-sm w-40 bg-white">
                        <option value="">All Status</option>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- TABLE -->
                <div class="p-6 overflow-x-auto">
                    <x-data-table id="invoice-table" :headers="[
                        'Invoice No',
                        'User',
                        'Package',
                        'Package Type',
                        'Travelers',
                        'Dates',
                        'Amount',
                        'Action',
                    ]" :excel="true" :print="true"
                        title="Invoices List" resourceName="Invoices">

                        @foreach ($invoices as $invoice)
                            <tr class="hover:bg-gray-50">

                                <!-- Invoice Number -->
                                <td class="text-center font-semibold">
                                    #{{ $invoice->invoice_no }}
                                </td>

                                <!-- User Info -->
                                <td>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-user text-gray-400"></i>
                                        <div>
                                            <div class="font-semibold">{{ $invoice->lead->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $invoice->lead->email ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Package Name -->
                                <td>
                                    <i class="fas fa-box text-gray-400"></i>
                                    {{ $invoice->package->package_name ?? ($invoice->package_name ?? 'N/A') }}
                                </td>

                                <!-- Package Type -->
                                <td>
                                    {{ $invoice->package_type ?? '—' }}
                                </td>

                                <!-- Travelers -->
                                <td>
                                    <div class="text-sm">
                                        Adults: <strong>{{ $invoice->adult_count }}</strong> •
                                        Children: <strong>{{ $invoice->child_count }}</strong> <br>
                                        Total: <strong>{{ $invoice->total_travelers }}</strong>
                                    </div>
                                </td>

                                <!-- Dates -->
                                <td>
                                    <div class="text-sm">
                                        <div>Issued: <strong>{{ $invoice->formatted_issue_date }}</strong></div>
                                        <div>Travel: <strong>{{ $invoice->formatted_travel_start_date }}</strong></div>
                                    </div>
                                </td>

                                <!-- Prices -->
                                <td>
                                    <div class="text-sm leading-5">
                                        Subtotal: ₹{{ number_format($invoice->subtotal_price, 2) }} <br>
                                        Discount: -₹{{ number_format($invoice->discount_amount, 2) }} <br>
                                        Tax: +₹{{ number_format($invoice->tax_amount, 2) }} <br>
                                        <span class="font-bold text-green-600">
                                            Final: ₹{{ number_format($invoice->final_price, 2) }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="text-center">
                                    <a href="{{ route('invoices.show', $invoice->id) }}"
                                        class="text-blue-600 hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach


                    </x-data-table>
                </div>
            </div>
        </div>
    </div>

    <!-- DATA TABLE FILTER SCRIPT -->
    <script>
        $(document).ready(function() {

            let selectedRange = "";
            let table = $("#invoice-table").DataTable();

            function applyFilters() {
                table.column(0).search($("#filter-invoice-id").val());
                table.column(1).search($("#filter-name").val());
                table.column(2).search($("#filter-email").val());
                table.column(3).search($("#filter-package").val());
                table.draw();
            }

            $("#filter-invoice-id, #filter-name, #filter-email, #filter-package").on("keyup", applyFilters);

            // Date Range Buttons
            $(".date-range-btn").on("click", function() {
                $(".date-range-btn").removeClass("bg-blue-600 text-white");
                $(this).addClass("bg-blue-600 text-white");

                selectedRange = $(this).data("value");
                table.draw();
            });

            // Status Filter
            $("#filter-status").on("change", function() {
                table.column(4).search($(this).val());
                table.draw();
            });

        });
    </script>

</x-app-layout>
