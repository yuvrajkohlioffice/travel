<div class="p-6 md:p-8 lg:p-10 border-b border-blue-100">

    @php
        // --- 1. SAFE DATA PREPARATION ---
        $scanner = [];
        $bank = [];

        // Check if company exists before accessing properties
        if (isset($company) && $company) {
            
            // Handle Scanner (Decode if string, otherwise use as array)
            $scannerRaw = $company->scanner_details;
            $scanner = is_string($scannerRaw) ? json_decode($scannerRaw, true) : ($scannerRaw ?? []);

            // Handle Bank (Decode if string, otherwise use as array)
            $bankRaw = $company->bank_details;
            $bank = is_string($bankRaw) ? json_decode($bankRaw, true) : ($bankRaw ?? []);
        }

        // --- 2. CALCULATE TOTALS ---
        $totalAmount = $invoice->final_price ?? 0;
        $totalPaid = $invoice->payments->sum('paid_amount');
        $balanceDue = $totalAmount - $totalPaid;
    @endphp

    <div class="flex flex-col lg:flex-row print:flex-row gap-8"
        style="width: 100%; max-width: 100%; display: flex; justify-content: space-between;">

        {{-- LEFT COLUMN: PAYMENT DETAILS --}}
        <div class="page-break-inside-avoid" style="width: 50%; padding-right: 20px; box-sizing: border-box;">

            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-credit-card mr-3 text-blue-500 bg-blue-100 p-2 rounded-full"></i>
                Payment Information
            </h3>

            <div style="display: flex; gap: 20px;">

                {{-- PRIORITY 1: Check if Scanner Image exists --}}
                @if (isset($scanner['image']) && !empty($scanner['image']))
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-6 border border-blue-100 shadow-sm"
                        style="width: 100%;">
                        <h4 class="font-bold text-gray-900 mb-4 text-center">Scan to Pay</h4>
                        <div class="flex flex-col items-center">
                            <div
                                class="h-48 w-48 border-2 border-dashed border-blue-300 rounded-2xl flex items-center justify-center bg-white mb-4 overflow-hidden relative">
                                <img src="{{ asset($scanner['image']) }}" class="w-full h-full object-contain"
                                    alt="UPI QR Code" />
                            </div>

                            @if (isset($scanner['upi_id']) && $scanner['upi_id'])
                                <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-mono mb-2">
                                    {{ $scanner['upi_id'] }}
                                </div>
                            @endif
                            <p class="text-sm text-gray-600 text-center">Use any mobile banking app to scan and pay</p>
                        </div>
                    </div>

                {{-- PRIORITY 2: Fallback to Bank Details if Scanner is missing --}}
                @elseif (isset($bank['account_number']) && !empty($bank['account_number']))
                    <div class="bg-gradient-to-br from-green-50 to-white rounded-2xl p-6 border border-green-100 shadow-sm"
                        style="width: 100%;">
                        <h4 class="font-bold text-gray-900 mb-4 text-center">Bank Transfer Details</h4>

                        <div class="space-y-3">
                            {{-- Bank Name --}}
                            @if (!empty($bank['bank_name']))
                                <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                    <span class="text-xs text-gray-500 uppercase font-semibold">Bank Name</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $bank['bank_name'] }}</span>
                                </div>
                            @endif

                            {{-- Account Name --}}
                            @if (!empty($bank['account_name']))
                                <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                    <span class="text-xs text-gray-500 uppercase font-semibold">Account Name</span>
                                    <span class="text-sm font-medium text-gray-800 text-right">{{ $bank['account_name'] }}</span>
                                </div>
                            @endif

                            {{-- Account Number --}}
                            <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                <span class="text-xs text-gray-500 uppercase font-semibold">Account No.</span>
                                <span class="text-sm font-mono font-bold text-blue-700">{{ $bank['account_number'] }}</span>
                            </div>

                            {{-- IFSC Code --}}
                            @if (!empty($bank['ifsc']))
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500 uppercase font-semibold">IFSC Code</span>
                                    <span class="text-sm font-mono font-bold text-gray-800">{{ $bank['ifsc'] }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-400">Please share receipt after transfer</p>
                        </div>
                    </div>

                {{-- PRIORITY 3: Nothing found --}}
                @else
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 text-center w-full">
                        <p class="text-gray-400 text-sm">No Payment Details Available</p>
                    </div>
                @endif

            </div>
        </div>

        {{-- RIGHT COLUMN: INVOICE SUMMARY --}}
        <div class="page-break-inside-avoid" style="width: 50%; padding-left: 20px; box-sizing: border-box;">

            <div class="bg-gradient-to-br from-blue-900 to-blue-800 rounded-2xl mt-[65px] p-8 text-white shadow-2xl print:shadow-none print:p-6 print:bg-blue-900">

                <h3 class="text-2xl font-bold mb-6 text-center border-b border-blue-700 pb-4">
                    Invoice Summary
                </h3>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center text-blue-200 text-sm">
                        <span>Package Subtotal</span>
                        <span class="font-medium text-white">₹ {{ number_format($invoice->subtotal_price ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-blue-200 text-sm">
                        <span>Discount</span>
                        <span class="font-medium text-green-300">
                            -₹ {{ number_format($invoice->discount_amount ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-blue-200 text-sm">
                        <span>Taxes & Fees</span>
                        <span class="font-medium text-white">₹ {{ number_format($invoice->tax_amount ?? 0, 2) }}</span>
                    </div>

                    <div class="border-t border-blue-700 my-2"></div>

                    <div class="flex justify-between items-center text-lg font-bold">
                        <span>Total Amount</span>
                        <span>₹ {{ number_format($totalAmount, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-green-300 font-medium">
                        <span>Total Paid</span>
                        <span>(-) ₹ {{ number_format($totalPaid, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xl font-bold pt-2 mt-2 border-t border-dashed border-blue-600">
                        <span class="{{ $balanceDue > 0 ? 'text-red-200' : 'text-green-200' }}">Balance Due</span>
                        <span class="{{ $balanceDue > 0 ? 'text-red-200' : 'text-green-200' }}">
                            ₹ {{ number_format($balanceDue, 2) }}
                        </span>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- BOTTOM ROW: PAYMENT HISTORY --}}
<div class="flex flex-col lg:flex-row print:flex-row gap-8 mt-5"
    style="width: 100%; max-width: 100%; display: flex; justify-content: space-between;">

    <div class="page-break-inside-avoid" style="width: 100%; padding-right: 20px; box-sizing: border-box;">

        @if (isset($invoice->payments) && $invoice->payments->count() > 0)
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-history mr-3 text-blue-500 bg-blue-100 p-2 rounded-full"></i>
                Payment History
            </h3>

            <div class="mt-4 rounded-xl border border-blue-100 overflow-hidden shadow-sm">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-50 text-blue-700 font-bold text-xs uppercase border-b border-blue-100">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Mode</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-blue-50 bg-white">
                        @foreach ($invoice->payments as $payment)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-700 font-medium">
                                    {{ \Carbon\Carbon::parse($payment->created_at)->format('d M, Y') }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    <span class="font-semibold">{{ $payment->payment_method ?? 'Online' }}</span>
                                    @if ($payment->transaction_id)
                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5"
                                            title="{{ $payment->transaction_id }}">
                                            #{{ Str::limit($payment->transaction_id, 12) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-green-600">
                                    ₹{{ number_format($payment->paid_amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>