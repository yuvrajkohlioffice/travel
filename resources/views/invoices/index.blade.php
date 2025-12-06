<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-7xl">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-user text-blue-600"></i>
                        Users
                    </h2>
                    <a href="{{ route('invoices.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Invoice
                    </a>
                </div>

                <!-- Success message -->
                @if (session('success'))
                    <div class="m-6 p-4 bg-green-500 text-white rounded flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <!-- Users Table -->
                <div class="p-6 overflow-x-auto">
                    <x-data-table id="users-table" 
                        :headers="['invoice_no', 'Name', 'Email', 'Package','Action']" 
                        :excel="true" :print="true"
                        title="Users List" resourceName="Invoices">
                        @foreach ($invoices as $invoice)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="text-center">{{ $invoice->invoice_no }} </td>
                                <td class="flex items-center gap-2">
                                    <i class="fas fa-user text-gray-400"></i> {{ $invoice->user->name }}
                                </td>
                                <td>
                                    <i class="fas fa-envelope text-gray-400"></i> {{ $invoice->user->email }}
                                </td>
                                <td>
                                    <i class="fas fa-user-tag text-gray-400"></i> {{ $invoice->package->package_name ?? 'N/A' }}
                                </td>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice->id) }}">
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
</x-app-layout>