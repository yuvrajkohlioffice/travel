<x-guest-layout>
    @php
        $lead = $invoice->lead;
        $agent = $lead->createdBy ?? null;
        $company = $agent->company ?? null;
    @endphp

    <div class="max-w-6xl mx-auto p-3 sm:p-6 lg:p-8">

        <div class="no-print flex justify-end mb-4 sm:mb-6 sticky top-4 z-50 sm:relative sm:top-0">
            <button onclick="window.print()"
                class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 
                text-white font-semibold py-2 px-5 sm:py-3 sm:px-6 rounded-xl shadow-lg flex items-center 
                transition-all duration-300 transform active:scale-95 hover:scale-105 text-sm sm:text-base">
                <i class="fas fa-print mr-2 sm:mr-3"></i> Print Invoice
            </button>
        </div>

        <div id="invoice-container"
            class="invoice-wrapper bg-white rounded-2xl sm:rounded-3xl shadow-xl sm:shadow-2xl overflow-hidden border border-blue-100">

            {{-- Header - Ensure your component uses grid-cols-1 md:grid-cols-2 --}}
            <div class="p-4 sm:p-0">
                <x-invoice.header :invoice="$invoice" />
            </div>

            {{-- Traveler Info --}}
            <div class="px-4 sm:px-0">
                <x-invoice.traveler-info :invoice="$invoice" />
            </div>

            {{-- Package Details --}}
            <div class="px-4 sm:px-0">
                <x-invoice.package-details :invoice="$invoice" />
            </div>

            {{-- Removed hard mt-[150px] as it breaks mobile flow. Used responsive margin. --}}
              <div class="px-4 sm:px-0">
                <div
                class=" border-t border-blue-100 bg-gradient-to-r from-blue-50/50 to-cyan-50/50 px-4 md:p-8 lg:p-10 print:px-4 print:py-2 print:mt-2 print:border-t-0">
                <div class="max-w-4xl">
                    <h4 class="font-bold text-gray-900 text-lg md:text-xl mb-4 print:mb-2 flex items-center">
                        <span class="bg-yellow-100 p-2 rounded-lg mr-3 print:p-1">
                            <i class="fas fa-star text-yellow-600 text-sm md:text-base"></i>
                        </span>
                        Special Instructions
                    </h4>

                    <div
                        class="bg-white p-5 md:p-6 rounded-2xl border border-blue-100 shadow-sm print:shadow-none print:p-2 print:border-0">
                        <ul class="space-y-4 print:space-y-1">
                            @forelse($invoice->special_instructions ?? [] as $instruction)
                                <li class="flex items-start">
                                    <i
                                        class="fas fa-check-circle text-blue-400 mt-1 mr-3 flex-shrink-0 text-xs print:mr-1"></i>
                                    <span class="text-gray-700 text-sm md:text-base leading-relaxed">
                                        {{ $instruction }}
                                    </span>
                                </li>
                            @empty
                                <li class="flex items-start">
                                    <i class="fas fa-heart text-pink-400 mt-1 mr-3 flex-shrink-0 text-xs"></i>
                                    <span class="text-gray-600 italic text-sm md:text-base">
                                        Enjoy your trip! All arrangements have been made according to your booking.
                                    </span>
                                </li>
                            @endforelse

                            @if ($invoice->additional_details)
                                <li class="pt-4 border-t border-gray-50 flex items-start print:pt-1 print:mt-1">
                                    <i
                                        class="fas fa-info-circle text-cyan-500 mt-1 mr-3 flex-shrink-0 text-xs print:mr-1"></i>
                                    <span class="font-medium text-gray-800 text-sm md:text-base leading-relaxed">
                                        {{ $invoice->additional_details }}
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            </div>
            


            {{-- Summary --}}
            <div class="border-t border-blue-100 p-4 sm:p-8 bg-gray-50">
                <x-invoice.summary :invoice="$invoice" :company="$company" />
            </div>

            {{-- Footer --}}
            <div class="p-4 sm:p-0">
                <x-invoice.footer :invoice="$invoice" />
            </div>
        </div>
    </div>

    {{-- Enhanced Print & Layout CSS --}}
    <style>
        /* Force background colors to print */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @media print {
            @page {
                margin: 0.5cm;
            }

            body {
                background: white;
            }

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
                border: none;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }

            .page-break-inside-avoid {
                page-break-inside: avoid;
            }
        }

        /* Responsive Fixes for smaller screens */
        @media (max-width: 640px) {

            /* Fix potential table overflows in components */
            #invoice-container table {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Adjust typography for mobile */
            h1 {
                font-size: 1.5rem !important;
            }

            h2 {
                font-size: 1.25rem !important;
            }

            .text-sm {
                font-size: 0.75rem !important;
            }
        }
    </style>
</x-guest-layout>
