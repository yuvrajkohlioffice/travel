<x-app-layout>
    @php
        // 1. Extract Data
        $lead = $invoice->lead;
        $agent = $lead->createdBy ?? null;
        $company = $agent->company ?? null;
        $companyName = $company->company_name ?? 'Our Team';

        // 2. Generate Links
        $invoiceLink = route('guest.show', $invoice->id);
        
        // Assuming your route is: Route::get('/form/{lead_id}', ...)->name('guest.form');
        // We use the lead ID associated with this invoice
        $formLink = route('guest.form', $lead->id); 

        // 3. Prepare Phone & Name
        $leadPhone = preg_replace('/[^0-9]/', '', ($lead->phone_code ?? '') . ($lead->phone_number ?? ''));
        $leadName = $lead->name ?? 'Valued Customer';
    @endphp

    <div class="max-w-6xl mx-auto p-4 md:p-6 lg:p-8">

        <div class="no-print flex flex-col sm:flex-row justify-end gap-3 mb-6">
            <button onclick="copyToClipboard('{{ $invoiceLink }}')"
                class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 
                text-white font-semibold py-3 px-6 rounded-xl shadow-lg flex items-center justify-center
                transition-all duration-300 transform hover:scale-105 active:scale-95">
                <i class="fas fa-link mr-3"></i> 
                <span id="copy-btn-text">Copy Invoice Link</span>
            </button>

            <button onclick="sendWhatsappLink(this, '{{ $leadPhone }}', '{{ $leadName }}', '{{ $invoiceLink }}', '{{ $formLink }}', '{{ $companyName }}')"
                class="bg-gradient-to-r from-teal-500 to-green-500 hover:from-teal-600 hover:to-green-600 
                text-white font-semibold py-3 px-6 rounded-xl shadow-lg flex items-center justify-center
                transition-all duration-300 transform hover:scale-105 active:scale-95 group">
                
                <i class="fab fa-whatsapp mr-3 text-xl icon-normal"></i>
                <svg class="animate-spin h-5 w-5 mr-3 text-white icon-loading hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>

                <span class="btn-text">Send on WhatsApp</span>
            </button>

            <button onclick="window.print()" 
                class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 
                text-white font-semibold py-3 px-6 rounded-xl shadow-lg flex items-center justify-center
                transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-print mr-3"></i> Print Invoice
            </button>
               
        </div>

        <div id="invoice-container" class="invoice-wrapper bg-white rounded-3xl shadow-2xl overflow-hidden border border-blue-100">
            {{-- Header --}}
            <x-invoice.header :invoice="$invoice" />

            {{-- Traveler Info --}}
            <x-invoice.traveler-info :invoice="$invoice" />

            {{-- Package Details --}}
            <x-invoice.package-details :invoice="$invoice" />

            <div class="page-break-inside-avoid border-t border-blue-100 bg-gradient-to-r from-blue-50 to-cyan-50 p-6 mt-8">
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
                <x-invoice.summary :invoice="$invoice" :company="$company" />
            </div>

            {{-- Footer --}}
            <x-invoice.footer :invoice="$invoice" />
        </div>
    </div>

    {{-- Print CSS --}}
    <style>
        @media print {
            body * { visibility: hidden; }
            #invoice-container, #invoice-container * { visibility: visible; }
            #invoice-container { position: absolute; left: 0; top: 0; width: 100%; margin: 0; }
            .no-print { display: none !important; }
        }
    </style>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // --- 1. Copy Link Logic ---
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const btnText = document.getElementById('copy-btn-text');
                const originalText = btnText.innerText;
                btnText.innerText = 'Copied!';
                setTimeout(() => { btnText.innerText = originalText; }, 2000);
            });
        }

        // --- 2. Send WhatsApp API Logic ---
        function sendWhatsappLink(button, phone, name, invoiceLink, formLink, companyName) {
            
            // 1. Validation
            if (!phone || phone.length < 10) {
                Swal.fire('Error', 'No valid phone number found for this lead.', 'error');
                return;
            }

            // 2. Confirmation
            Swal.fire({
                title: 'Send details on WhatsApp?',
                text: `Sending Invoice & Form links to ${name}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                confirmButtonText: 'Yes, Send Now'
            }).then((result) => {
                if (result.isConfirmed) {
                    performWhatsAppRequest(button, phone, name, invoiceLink, formLink, companyName);
                }
            });
        }

        function performWhatsAppRequest(button, phone, name, invoiceLink, formLink, companyName) {
            const iconNormal = button.querySelector('.icon-normal');
            const iconLoading = button.querySelector('.icon-loading');
            const btnText = button.querySelector('.btn-text');
            const originalText = btnText.innerText;

            // UI Loading State
            button.disabled = true;
            button.classList.add('opacity-75', 'cursor-not-allowed');
            iconNormal.classList.add('hidden');
            iconLoading.classList.remove('hidden');
            btnText.innerText = 'Sending...';

            // --- 3. PROFESSIONAL MESSAGE FORMAT ---
            const message = `Hello *${name}*, 👋

Thank you for choosing us for your upcoming trip! 🌍✈️

Please find your important trip details below:

📄 *Invoice & Itinerary:*
${invoiceLink}

📝 *Traveler Details Form:*
Please fill out this form to confirm your booking details:
${formLink}

💡 *Note:* You can easily print these details using the "Print" button on the invoice page.

Best regards,
*${companyName}*`;

            // API Call
            fetch("{{ route('whatsapp.send-text') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    recipient: phone, // The API requires a main link
                    text: message      // The full professional text overwrites the default
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' || data.status === true) {
                    Swal.fire('Sent!', 'Message sent successfully on WhatsApp.', 'success');
                } else {
                    Swal.fire('Failed', data.message || 'Could not send message.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'System error occurred.', 'error');
            })
            .finally(() => {
                // Reset UI
                button.disabled = false;
                button.classList.remove('opacity-75', 'cursor-not-allowed');
                iconNormal.classList.remove('hidden');
                iconLoading.classList.add('hidden');
                btnText.innerText = originalText;
            });
        }
    </script>
</x-app-layout>