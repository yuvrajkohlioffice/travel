    <div class="bg-white rounded-2xl shadow-xl border overflow-hidden">
        <div class="p-6 border-b bg-blue-50">
            <div class="flex justify-between items-center">

                <h2 class="text-xl font-bold"
                    x-text="packageData?.package_name || 'Select Package'"></h2>

                <div class="text-right">
                    <p>Total Travelers: <span x-text="totalTravelers"></span></p>
                    <p>Subtotal: ₹ <span x-text="subtotal.toFixed(2)"></span></p>
                    <p>Tax (<span x-text="taxPercent"></span>%):
   ₹ <span x-text="taxAmount.toFixed(2)"></span></p>

                    <p class="font-bold text-blue-700 text-xl">
                        Final Price: ₹ <span x-text="finalPrice.toFixed(2)"></span>
                    </p>
                </div>

            </div>
        </div>
    </div>
