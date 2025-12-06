<div class="p-6 md:p-8 lg:p-10 border-b border-blue-100">
    <h3 class="text-2xl font-bold text-gray-900 mb-8 flex items-center">
        <i class="fas fa-suitcase-rolling mr-3 text-blue-500 bg-blue-100 p-2 rounded-full"></i> Travel Package Details
    </h3>

    <div class="overflow-x-auto rounded-2xl border border-blue-100 shadow-sm">
        <table class="min-w-full divide-y divide-blue-100">
            <thead class="bg-gradient-to-r from-blue-50 to-cyan-50">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Package Name</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Travelers</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Price/Person</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-blue-50">
                <tr class="hover:bg-blue-50 transition-colors duration-200">
                    <td class="px-6 py-5 whitespace-nowrap font-bold text-gray-900">
                        {{ $invoice->package_name ?? $invoice->package->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        {{ $invoice->adult_count ?? 0 }} Adults, {{ $invoice->child_count ?? 0 }} Children
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap font-bold text-gray-900">
                        ${{ number_format($invoice->price_per_person ?? 0, 2) }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap font-bold text-blue-700">
                        ${{ number_format($invoice->final_price ?? 0, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
