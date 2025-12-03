<div x-data="invoiceModal()">

    <!-- INVOICE MODAL -->
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">

        <div class="bg-white rounded-xl p-6 w-full max-w-3xl shadow-lg">
            <h2 class="text-xl font-bold mb-4">Send Invoice</h2>

            <!-- Lead Info -->
            <p class="mb-2"><strong>Lead:</strong> <span x-text="leadName"></span></p>

            <!-- Package Dropdown -->
            <label class="font-semibold">Select Package</label>
            <select x-model="selectedPackage" @change="fetchPackageData" class="w-full border rounded p-2 mb-4">
                <option value="">Select Package</option>
                <template x-for="pkg in packages" :key="pkg.id">
                    <option :value="pkg.id" x-text="pkg.package_name"></option>
                </template>
            </select>

            <!-- PDF Preview -->
            <div class="border rounded p-4 bg-gray-50 h-72 overflow-auto" x-show="packageData">
                <h3 class="text-lg font-bold" x-text="packageData.package_name"></h3>

                <p><strong>Price:</strong> ₹<span x-text="packageData.package_price"></span></p>

                <!-- DAYS LIST -->
                <div class="mt-2">
                    <template x-for="(day, index) in packageData.days">
                        <p class="text-sm">
                            <strong>Day <span x-text="index + 1"></span>:</strong>
                            <span x-text="day.from"></span> → <span x-text="day.to"></span>
                        </p>
                    </template>
                </div>

                <!-- ADDONS -->
                <div class="mt-3">
                    <strong>Add-Ons:</strong>
                    <ul class="list-disc ml-5">
                        <template x-for="addon in packageData.addons">
                            <li>
                                <span x-text="addon.title"></span>:
                                ₹<span x-text="addon.price"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <button @click="sendInvoice" class="bg-blue-600 text-white px-4 py-2 rounded mt-4 w-full">
                Send Invoice
            </button>
        </div>

    </div>

</div>
