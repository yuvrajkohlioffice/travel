<div class="p-6 md:p-8 lg:p-10 border-b border-blue-100">
    <h3 class="text-2xl font-bold text-gray-900 mb-8 flex items-center">
        <i class="fas fa-suitcase-rolling mr-3 text-blue-500 bg-blue-100 p-2 rounded-full"></i> 
        Travel Package Details
    </h3>

    {{-- Changed overflow-x-hidden to overflow-hidden so it respects border-radius but doesn't clip content weirdly --}}
    <div class="rounded-2xl border border-blue-100 shadow-sm overflow-hidden">
        
        {{-- ADDED: 'table-fixed' makes the columns respect specific widths --}}
        <table class="w-full table-fixed divide-y divide-blue-100">
            <thead class="bg-gradient-to-r from-blue-50 to-cyan-50">
                <tr>
                    {{-- COLUMN 1: Package Name (Given 40% width) --}}
                    <th class="w-[40%] px-4 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">
                        Package Name
                    </th>
                    {{-- COLUMN 2: Travelers (Given 25% width) --}}
                    <th class="w-[25%] px-4 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">
                        Travelers
                    </th>
                    {{-- COLUMN 3: Price (Given 15% width) --}}
                    <th class="w-[15%] px-4 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">
                        Price/Person
                    </th>
                    {{-- COLUMN 4: Total (Given 20% width) --}}
                    <th class="w-[20%] px-4 py-4 text-right text-sm font-bold text-gray-900 uppercase tracking-wider">
                        Total
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-blue-50">
                <tr class="hover:bg-blue-50 transition-colors duration-200">
                    
                    {{-- FIX: Removed whitespace-nowrap, Added whitespace-normal break-words --}}
                    <td class="px-4 py-5 font-bold text-gray-900 whitespace-normal break-words align-top">
                        {{ $invoice->package_name ?? $invoice->package->package_name ?? 'N/A' }}
                    </td>

                    {{-- FIX: Allow wrapping for travelers if needed --}}
                    <td class="px-4 py-5 text-gray-700 whitespace-normal align-top">
                        {{ $invoice->adult_count ?? 0 }} Adults, {{ $invoice->child_count ?? 0 }} Children
                    </td>

                    <td class="px-4 py-5 font-bold text-gray-900 whitespace-nowrap align-top">
                        ₹{{ number_format($invoice->price_per_person ?? 0, 2) }}
                    </td>

                    {{-- Aligned Total to Right for better invoice standard --}}
                    <td class="px-4 py-5 font-bold text-blue-700 whitespace-nowrap text-right align-top">
                        ₹{{ number_format($invoice->final_price ?? 0, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>