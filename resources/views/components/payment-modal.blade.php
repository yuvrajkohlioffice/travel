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
        <div
            class="max-h-[70vh] overflow-y-auto p-6 space-y-6 scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-100 dark:scrollbar-track-gray-700">

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
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-1" for="paidAmount">Paid
                        Amount</label>
                    <input id="paidAmount" type="number" x-model.number="paidAmount" min="0"
                        :max="remainingAmount" step="0.01"
                        class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-500 transition"
                        placeholder="Enter amount being paid">
                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">
                        Will remain: <span x-text="formatCurrency(remainingAmountReactive)"></span>
                    </p>
                    <p x-show="paidAmount > remainingAmount" class="text-red-500 mt-1 text-sm">Paid amount cannot exceed
                        remaining amount.</p>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="font-semibold">Payment Method</label>
                    <select x-model="paymentMethodId" required class="w-full p-3 rounded-xl border">
                        <option value="">Select Method</option>
                        <template x-for="method in paymentMethods" :key="method.id">
                            <option :value="method.id" x-text="method.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Transaction ID -->
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-1"
                        for="transactionId">Transaction ID (Optional)</label>
                    <input id="transactionId" type="text" x-model="transactionId"
                        class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-500 transition"
                        placeholder="Enter transaction ID if available">
                </div>
                <!-- Image Proof Upload -->
                <div x-show="selectedMethod?.image_proof_required" x-transition>
                    <label class="block font-semibold mb-1">
                        Payment Proof Image <span class="text-red-500">*</span>
                    </label>
                    <input type="file" accept="image/*" @change="handleImageUpload"
                        class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                    <p class="text-sm text-gray-500 mt-1">Upload screenshot / receipt</p>
                </div>
                <!-- Bank Details -->
                <div x-show="hasBankDetails" x-transition
                    class="rounded-xl border p-4 bg-gray-50 dark:bg-gray-700 space-y-1">

                    <p class="font-semibold text-gray-700 dark:text-gray-200">Bank Details</p>

                    <p x-show="selectedMethod.bank_name">
                        <strong>Bank:</strong> <span x-text="selectedMethod.bank_name"></span>
                    </p>

                    <p x-show="selectedMethod.account_name">
                        <strong>Account Name:</strong> <span x-text="selectedMethod.account_name"></span>
                    </p>

                    <p x-show="selectedMethod.account_number">
                        <strong>Account Number:</strong> <span x-text="selectedMethod.account_number"></span>
                    </p>

                    <p x-show="selectedMethod.ifsc_code">
                        <strong>IFSC:</strong> <span x-text="selectedMethod.ifsc_code"></span>
                    </p>
                </div>
                <!-- Tax Details -->
                <div x-show="selectedMethod?.is_tax_applicable" x-transition
                    class="rounded-xl border p-4 bg-yellow-50 dark:bg-gray-700 space-y-1">

                    <p class="font-semibold">Tax Information</p>

                    <p>
                        <strong x-text="selectedMethod.tax_name"></strong> :
                        <span x-text="selectedMethod.tax_percentage + '%'"></span>
                    </p>

                    <p x-show="selectedMethod.tax_number">
                        <strong>Tax Number:</strong>
                        <span x-text="selectedMethod.tax_number"></span>
                    </p>
                </div>
                <div x-show="selectedMethod?.description" x-transition
                    class="text-sm text-gray-600 dark:text-gray-300 italic">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    <span x-text="selectedMethod.description"></span>
                </div>

                <!-- Next Payment Date (only for partial payment) -->
                <div x-show="paidAmount < remainingAmount" x-transition>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-1" for="nextPaymentDate">
                        Next Payment Date <span class="text-gray-400 text-sm">(required for partial payments)</span>
                    </label>
                    <input id="nextPaymentDate" type="date" x-model="nextPaymentDate"
                        class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-500 transition">
                    <p x-show="partialPaymentWithoutNextDate" class="text-red-500 mt-1 text-sm">Next payment date is
                        required for partial payments.</p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200 mb-1" for="paymentNotes">Notes
                        (Optional)</label>
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
