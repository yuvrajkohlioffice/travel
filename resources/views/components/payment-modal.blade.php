<!-- Payment Modal -->
<div x-data="paymentModal()" x-show="paymentOpen" x-transition.opacity
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">

    <div @click.outside="closePaymentModal()" x-transition
         class="bg-white dark:bg-gray-800 w-full max-w-3xl rounded-2xl shadow-xl p-6 relative">

        <!-- Close Button -->
        <button @click="closePaymentModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 transition"
                aria-label="Close Payment Modal">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>

        <!-- Header -->
        <div class="text-center border-b pb-3">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Add Payment</h2>
            <p class="text-blue-600 font-semibold text-lg mt-1">
                Invoice #: <span x-text="paymentInvoiceNumber"></span>
            </p>
        </div>

        <!-- Body -->
        <div class="max-h-[70vh] overflow-y-auto p-6 space-y-6 scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-100 dark:scrollbar-track-gray-700">

            <form @submit.prevent="submitPayment" class="space-y-6">

                <!-- Total & Remaining Amounts -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 dark:text-gray-200">
                    <div>
                        <p>Total Invoice Amount:</p>
                        <p class="font-semibold text-lg" x-text="formatCurrency(amount)"></p>
                    </div>
                    <div>
                        <p>Remaining Amount:</p>
                        <p class="font-semibold text-lg" x-text="formatCurrency(remainingAmount)"></p>
                    </div>
                </div>

                <!-- Paid Amount Input -->
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-1" for="paidAmount">Paid Amount</label>
                    <input id="paidAmount" type="number" x-model.number="paidAmount" min="0" :max="remainingAmount" step="0.01"
                           class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-500 transition"
                           placeholder="Enter amount being paid">
                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">
                        Will remain: <span x-text="formatCurrency(remainingAmount - paidAmount)"></span>
                    </p>
                    <p x-show="paidAmount > remainingAmount" class="text-red-500 mt-1 text-sm">Paid amount cannot exceed remaining amount.</p>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-1" for="paymentMethod">Payment Method</label>
                    <select id="paymentMethod" x-model="paymentMethod" required
                            class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-500 transition">
                        <option value="">Select Method</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="card">Card</option>
                        <option value="cash">Cash</option>
                        <option value="paypal">PayPal</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Transaction ID -->
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-1" for="transactionId">Transaction ID (Optional)</label>
                    <input id="transactionId" type="text" x-model="transactionId"
                           class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-500 transition"
                           placeholder="Enter transaction ID if available">
                </div>

                <!-- Next Payment Date (only for partial payment) -->
                <div x-show="paidAmount < remainingAmount" x-transition>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-1" for="nextPaymentDate">
                        Next Payment Date <span class="text-gray-400 text-sm">(required for partial payments)</span>
                    </label>
                    <input id="nextPaymentDate" type="date" x-model="nextPaymentDate"
                           class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-500 transition">
                    <p x-show="partialPaymentWithoutNextDate" class="text-red-500 mt-1 text-sm">Next payment date is required for partial payments.</p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-1" for="paymentNotes">Notes (Optional)</label>
                    <textarea id="paymentNotes" x-model="paymentNotes" rows="3"
                              class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-500 transition"
                              placeholder="Enter any notes about this payment"></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col md:flex-row justify-end gap-3 mt-4">
                    <button type="button" @click="closePaymentModal()"
                            class="px-6 py-3 rounded-xl border bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
                        Submit Payment
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Alpine.js Component Logic -->

