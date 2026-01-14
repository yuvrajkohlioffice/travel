<div class="p-4 sm:p-6 md:p-8 lg:p-10 border-b border-blue-100">
    <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-user-tag mr-3 text-blue-500 bg-blue-100 p-2 rounded-full text-sm md:text-base"></i> 
        Traveler Information
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8">

        {{-- Primary Traveler --}}
        <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-5 md:p-6 shadow-sm border border-blue-100">
            <h4 class="text-lg md:text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user-circle mr-3 text-blue-500"></i> Primary Traveler
            </h4>
            <div class="space-y-3 text-sm md:text-base">
                <p class="text-gray-800 flex flex-col sm:flex-row">
                    <span class="font-semibold sm:w-32">Full Name:</span> 
                    <span class="break-words">{{ $invoice->primary_full_name ?? 'N/A' }}</span>
                </p>
                <p class="text-gray-800 flex flex-col sm:flex-row">
                    <span class="font-semibold sm:w-32">Email:</span> 
                    <span class="break-words">{{ $invoice->primary_email ?? 'N/A' }}</span>
                </p>
                <p class="text-gray-800 flex flex-col sm:flex-row">
                    <span class="font-semibold sm:w-32">Phone:</span> 
                    <span>{{ $invoice->primary_phone ?? 'N/A' }}</span>
                </p>
                <p class="text-gray-800 flex flex-col sm:flex-row">
                    <span class="font-semibold sm:w-32">Address:</span> 
                    <span class="break-words">{{ $invoice->primary_address ?? 'N/A' }}</span>
                </p>
            </div>
        </div>

        {{-- Additional Travelers --}}
        <div class="bg-gradient-to-br from-cyan-50 to-white rounded-2xl p-5 md:p-6 shadow-sm border border-cyan-100">
            <h4 class="text-lg md:text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-users mr-3 text-cyan-500"></i> Additional Travelers
            </h4>
            <div class="space-y-2 text-sm md:text-base">
                @forelse($invoice->additional_travelers ?? [] as $traveler)
                    <div class="p-2 bg-white/50 rounded-lg border border-cyan-50">
                        <p class="text-gray-800">
                            <span class="font-semibold">{{ $traveler['name'] ?? 'N/A' }}</span>
                            <span class="text-gray-500 block sm:inline text-xs sm:text-sm">
                                ({{ $traveler['relation'] ?? '' }}{{ isset($traveler['age']) ? ', Age ' . $traveler['age'] : '' }})
                            </span>
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 italic">No additional travelers.</p>
                @endforelse
            </div>
            
            <div class="mt-4 pt-4 border-t border-cyan-100 flex justify-between items-center">
                <span class="text-gray-600 font-medium">Total Group Size:</span>
                <span class="bg-cyan-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                    {{ $invoice->total_travelers ?? 1 }} {{ Str::plural('Person', $invoice->total_travelers ?? 1) }}
                </span>
            </div>
        </div>

    </div>
</div>