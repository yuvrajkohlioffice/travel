<div class="p-6 md:p-8 lg:p-10 border-b border-blue-100">

    <!-- A4 Fixed Width Layout -->
    <div class="flex flex-col lg:flex-row print:flex-row gap-8"
         style="width: 100%; max-width: 100%; display: flex; justify-content: space-between;">

        <!-- LEFT: 50% Payment Info -->
        <div class="page-break-inside-avoid"
             style="width: 50%; padding-right: 20px; box-sizing: border-box;">

            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-credit-card mr-3 text-blue-500 bg-blue-100 p-2 rounded-full"></i>
                Payment Information
            </h3>

            <div style="display: flex; gap: 20px;">
                <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-6 border border-blue-100 shadow-sm"
                     style="width: 100%;">

                    <h4 class="font-bold text-gray-900 mb-4 text-center">Scan to Pay</h4>

                    <div class="flex flex-col items-center">
                        <div class="h-48 w-48 border-2 border-dashed border-blue-300 rounded-2xl 
                                    flex items-center justify-center bg-white mb-4">
                            <img src="{{ asset('QRCode.png') }}" class="w-32 h-32" alt="QR Code" />
                        </div>
                        <p class="text-sm text-gray-600 text-center">
                            Use any mobile banking app to scan and pay
                        </p>
                    </div>

                </div>
            </div>

        </div>

        <!-- RIGHT: 50% Invoice Summary -->
        <div class="page-break-inside-avoid"
             style="width: 50%; padding-left: 20px; box-sizing: border-box;">

            <div class="bg-gradient-to-br from-blue-900 to-blue-800 rounded-2xl p-8 text-white shadow-2xl 
                        print:shadow-none print:p-4">

                <h3 class="text-2xl font-bold mb-8 text-center border-b border-blue-700 pb-4">
                    Invoice Summary
                </h3>

                <div class="space-y-4 mb-8">

                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Package Subtotal</span>
                        <span class="font-bold">₹ {{ number_format($invoice->subtotal_price ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Discount</span>
                        <span class="font-bold text-green-300">
                            -₹ {{ number_format($invoice->discount_amount ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Taxes & Fees</span>
                        <span class="font-bold">₹ {{ number_format($invoice->tax_amount ?? 0, 2) }}</span>
                    </div>

                    <div style="height: 1px; background: #1e40af; margin: 12px 0;"></div>

                    <div class="flex justify-between items-center pt-4"
                         style="border-top: 1px solid #1e3a8a;">
                        <span class="text-xl font-bold text-blue-100">Total Amount</span>
                        <span class="text-2xl font-bold">₹ {{ number_format($invoice->final_price ?? 0, 2) }}</span>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>
