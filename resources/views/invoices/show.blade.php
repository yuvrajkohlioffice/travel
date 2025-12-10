<x-app-layout>
    <div class="max-w-6xl mx-auto p-4 md:p-6 lg:p-8">

        <!-- Print Button -->
        <div class="no-print flex justify-end mb-6">
            <button onclick="window.print()" 
                class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 
                text-white font-semibold py-3 px-6 rounded-xl shadow-lg flex items-center 
                transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-print mr-3"></i> Print Invoice
            </button>
        </div>

        <!-- Invoice Container -->
        <div id="invoice-container"
            class=" invoice-wrapper bg-white rounded-3xl shadow-2xl overflow-hidden border border-blue-100">

            {{-- Header --}}
            <x-invoice.header :invoice="$invoice" />

            {{-- Traveler Info --}}
            <x-invoice.traveler-info :invoice="$invoice" />

            {{-- Package Details --}}
            <x-invoice.package-details :invoice="$invoice" />

            <!-- Special Instructions -->
            <div class="page-break-inside-avoid border-t border-blue-100 bg-gradient-to-r from-blue-50 to-cyan-50 p-6 mt-[150px]">
                <h4 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-3"></i>
                    Special Instructions for Your Trip
                </h4>

                <div class="bg-white p-5 rounded-xl border border-blue-200 shadow-sm">
                    <ul class="list-disc pl-5 text-gray-700 space-y-2">
                        @forelse($invoice->special_instructions ?? [] as $instruction)
                            <li>{{ $instruction }}</li>
                        @empty
                            <li>Enjoy your trip! All arrangements have been made according to your booking.</li>
                        @endforelse

                        @if($invoice->additional_details)
                            <li class="font-medium text-gray-800">{{ $invoice->additional_details }}</li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Summary --}}
            <div class="border-t border-blue-100 p-6 bg-gray-50">
                <x-invoice.summary :invoice="$invoice" />
            </div>

            {{-- Footer --}}
            <x-invoice.footer :invoice="$invoice" />
        </div>

    </div>

    {{-- Print CSS --}}
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #invoice-container,
            #invoice-container * {
                visibility: visible;
            }
            #invoice-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
