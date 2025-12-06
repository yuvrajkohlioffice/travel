<div class="p-6 md:p-8 lg:p-10 border-b border-blue-100 page-break-inside-avoid">
    <div class="flex flex-col lg:flex-row justify-center">
        <div class="lg:w-2/3 mb-10 lg:mb-0">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-credit-card mr-3 text-blue-500 bg-blue-100 p-2 rounded-full"></i>
                Payment Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- QR Code -->
                <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-6 border border-blue-100 shadow-sm">
                    <h4 class="font-bold text-gray-900 mb-4 text-center">Scan to Pay</h4>
                    <div class="flex flex-col items-center">
                        <div
                            class="h-48 w-48 border-2 border-dashed border-blue-300 rounded-2xl flex items-center justify-center bg-white mb-4">
                            <div class="text-center">
                                
                                <img src="{{ asset('QRCode.png') }}" alt="QR Code" class="w-32 h-32" />
                                <p class="text-sm text-gray-600">Payment QR Code</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 text-center">Use any mobile banking app to scan
                            and pay</p>
                    </div>
                </div>

                <!-- Amount in Words -->
                
            </div>

            <!-- Pay Now Button -->
           
        </div>
        <div class="lg:w-1/4 w-full max-w-md mx-auto">
            <div
                class="bg-gradient-to-br from-blue-900 to-blue-800 rounded-2xl p-8 text-white shadow-2xl print:shadow-none print:p-4 page-break-inside-avoid">
                <h3 class="text-2xl font-bold mb-8 text-center border-b border-blue-700 pb-4 print:mb-4 print:text-xl">
                    Invoice Summary
                </h3>

                <div class="space-y-4 mb-8 print:mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Package Subtotal</span>
                        <span class="font-bold">₹ {{ number_format($invoice->subtotal_price ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Discount</span>
                        <span class="font-bold text-green-300">-₹ {{ number_format($invoice->discount_amount ?? 0, 2)
                            }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Taxes & Fees</span>
                        <span class="font-bold">₹ {{ number_format($invoice->tax_amount ?? 0, 2) }}</span>
                    </div>
                    <div class="h-px w-full bg-blue-700 my-4 print:my-2"></div>
                    <div class="flex justify-between items-center pt-4 border-t border-blue-700">
                        <span class="text-xl font-bold text-blue-100">Total Amount</span>
                        <span class="text-3xl font-bold">₹ {{ number_format($invoice->final_price ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>