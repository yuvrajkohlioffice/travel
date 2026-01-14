<div class="p-4 sm:p-6 md:p-8 lg:p-10 border-b border-blue-100">

    @php
        $scanner = [];
        $bank = [];
        if (isset($company) && $company) {
            $scannerRaw = $company->scanner_details;
            $scanner = is_string($scannerRaw) ? json_decode($scannerRaw, true) : ($scannerRaw ?? []);
            $bankRaw = $company->bank_details;
            $bank = is_string($bankRaw) ? json_decode($bankRaw, true) : ($bankRaw ?? []);
        }
        $totalAmount = $invoice->final_price ?? 0;
        $totalPaid = $invoice->payments->sum('paid_amount');
        $balanceDue = $totalAmount - $totalPaid;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

        {{-- LEFT COLUMN: PAYMENT DETAILS --}}
        <div class="page-break-inside-avoid w-full">
            <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-credit-card mr-3 text-blue-500 bg-blue-100 p-2 rounded-full text-sm md:text-base"></i>
                Payment Information
            </h3>

            <div class="w-full">
                {{-- PRIORITY 1: Scanner --}}
                @if (isset($scanner['image']) && !empty($scanner['image']))
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-6 border border-blue-100 shadow-sm">
                        <h4 class="font-bold text-gray-900 mb-4 text-center">Scan to Pay</h4>
                        <div class="flex flex-col items-center">
                            <div class="h-48 w-48 border-2 border-dashed border-blue-200 rounded-2xl flex items-center justify-center bg-white mb-4 overflow-hidden relative">
                                <img src="{{ asset($scanner['image']) }}" class="w-full h-full object-contain p-2" alt="UPI QR Code" />
                            </div>

                            @if (isset($scanner['upi_id']) && $scanner['upi_id'])
                                <div class="bg-blue-100 text-blue-800 px-4 py-1.5 rounded-full text-xs font-mono mb-2 break-all text-center">
                                    {{ $scanner['upi_id'] }}
                                </div>
                            @endif
                            <p class="text-xs text-gray-500 text-center">Use any UPI app (GPay, PhonePe, Paytm)</p>
                        </div>
                    </div>

                {{-- PRIORITY 2: Bank Details --}}
                @elseif (isset($bank['account_number']) && !empty($bank['account_number']))
                    <div class="bg-gradient-to-br from-green-50 to-white rounded-2xl p-6 border border-green-100 shadow-sm">
                        <h4 class="font-bold text-gray-900 mb-4 flex items-center justify-center">
                            <i class="fas fa-university mr-2 text-green-600"></i> Bank Transfer
                        </h4>

                        <div class="space-y-3">
                            <div class="flex justify-between items-start border-b border-gray-100 pb-2">
                                <span class="text-xs text-gray-500 uppercase font-semibold">Bank Name</span>
                                <span class="text-sm font-bold text-gray-800 text-right">{{ $bank['bank_name'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-start border-b border-gray-100 pb-2">
                                <span class="text-xs text-gray-500 uppercase font-semibold">Account Name</span>
                                <span class="text-sm font-medium text-gray-800 text-right">{{ $bank['account_name'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-start border-b border-gray-100 pb-2">
                                <span class="text-xs text-gray-500 uppercase font-semibold">Account No.</span>
                                <span class="text-sm font-mono font-bold text-blue-700">{{ $bank['account_number'] }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-xs text-gray-500 uppercase font-semibold">IFSC Code</span>
                                <span class="text-sm font-mono font-bold text-gray-800">{{ $bank['ifsc'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                @else
                    <div class="bg-gray-50 rounded-2xl p-10 border border-gray-200 text-center">
                        <p class="text-gray-400 text-sm">No Payment Details Available</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN: INVOICE SUMMARY --}}
        <div class="page-break-inside-avoid w-full">
            <div class="bg-gradient-to-br from-slate-900 to-blue-900 rounded-2xl p-6 md:p-8 text-white shadow-xl">
                <h3 class="text-xl font-bold mb-6 text-center border-b border-white/10 pb-4 uppercase tracking-wider">
                    Invoice Summary
                </h3>

                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center text-blue-100/80 text-sm">
                        <span>Package Subtotal</span>
                        <span class="font-medium text-white">₹ {{ number_format($invoice->subtotal_price ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-blue-100/80 text-sm">
                        <span>Discount</span>
                        <span class="font-medium text-green-400">-₹ {{ number_format($invoice->discount_amount ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-blue-100/80 text-sm">
                        <span>Taxes & Fees</span>
                        <span class="font-medium text-white">₹ {{ number_format($invoice->tax_amount ?? 0, 2) }}</span>
                    </div>

                    <div class="border-t border-white/10 my-2"></div>

                    <div class="flex justify-between items-center text-base md:text-lg font-bold">
                        <span>Total Amount</span>
                        <span>₹ {{ number_format($totalAmount, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-green-400 font-medium text-sm">
                        <span>Total Paid</span>
                        <span>(-) ₹ {{ number_format($totalPaid, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xl md:text-2xl font-black pt-4 mt-2 border-t border-dashed border-white/20">
                        <span class="text-blue-100">Balance Due</span>
                        <span class="{{ $balanceDue > 0 ? 'text-yellow-400' : 'text-green-400' }}">
                            ₹ {{ number_format($balanceDue, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BOTTOM SECTION: PAYMENT HISTORY --}}
    @if (isset($invoice->payments) && $invoice->payments->count() > 0)
        <div class="mt-12 page-break-inside-avoid">
            <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-history mr-3 text-blue-500 bg-blue-100 p-2 rounded-full text-sm md:text-base"></i>
                Payment History
            </h3>

            <div class="rounded-2xl border border-blue-100 overflow-hidden shadow-sm overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-50 text-blue-700 font-bold text-xs uppercase">
                        <tr>
                            <th class="px-4 py-4">Date</th>
                            <th class="px-4 py-4">Mode / ID</th>
                            <th class="px-4 py-4 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-50 bg-white">
                        @foreach ($invoice->payments as $payment)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-4 text-gray-700 font-medium whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($payment->created_at)->format('d M, Y') }}
                                </td>
                                <td class="px-4 py-4 text-gray-600">
                                    <span class="font-semibold block">{{ $payment->payment_method ?? 'Online' }}</span>
                                    @if ($payment->transaction_id)
                                        <span class="text-[10px] text-gray-400 font-mono break-all uppercase">
                                            #{{ $payment->transaction_id }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right font-bold text-green-600 whitespace-nowrap">
                                    ₹{{ number_format($payment->paid_amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>