<div class="p-6 md:p-8 lg:p-10 border-b border-blue-100">
    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-user-tag mr-3 text-blue-500 bg-blue-100 p-2 rounded-full"></i> Traveler Information
    </h3>

    <div class="grid grid-cols-2 md:grid-cols-2 gap-8">

        {{-- Primary Traveler --}}
        <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-6 shadow-sm border border-blue-100">
            <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user-circle mr-3 text-blue-500"></i> Primary Traveler
            </h4>
            <div class="space-y-3">
                <p class="text-gray-800"><span class="font-semibold">Full Name:</span> {{ $invoice->primary_full_name ?? 'N/A' }}</p>
                <p class="text-gray-800"><span class="font-semibold">Email:</span> {{ $invoice->primary_email ?? 'N/A' }}</p>
                <p class="text-gray-800"><span class="font-semibold">Phone:</span> {{ $invoice->primary_phone ?? 'N/A' }}</p>
                <p class="text-gray-800"><span class="font-semibold">Address:</span> {{ $invoice->primary_address ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Additional Travelers --}}
        <div class="bg-gradient-to-br from-cyan-50 to-white rounded-2xl p-6 shadow-sm border border-cyan-100">
            <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-users mr-3 text-cyan-500"></i> Additional Travelers
            </h4>
            <div class="space-y-2">
                @forelse($invoice->additional_travelers ?? [] as $traveler)
                    <p class="text-gray-800">
                        <span class="font-semibold">{{ $traveler['name'] ?? 'N/A' }}</span>
                        ({{ $traveler['relation'] ?? '' }}{{ isset($traveler['age']) ? ', Age ' . $traveler['age'] : '' }})
                    </p>
                @empty
                    <p class="text-gray-800">No additional travelers.</p>
                @endforelse
            </div>
            <div class="mt-4 pt-4 border-t border-cyan-100">
                <p class="text-gray-800">
                    <span class="font-semibold">Total Travelers:</span> {{ $invoice->total_travelers ?? 1 }} Persons
                </p>
            </div>
        </div>

    </div>
</div>
