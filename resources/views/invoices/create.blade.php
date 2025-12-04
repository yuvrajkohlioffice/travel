<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">

        <div class="max-w-6xl w-full p-4 md:p-6 lg:p-8">
            <form method="POST" action="{{ route('invoices.store') }}" x-data="invoiceGenerator()" class="space-y-6">
                @csrf

                <!-- Hidden prefill inputs -->
                <input type="hidden" name="package_id" x-model="selectedPackage">
                <input type="hidden" name="adult_count" x-model="adultCount">
                <input type="hidden" name="child_count" x-model="childCount">
                <input type="hidden" name="price_per_person" x-model="basePrice">
                <input type="hidden" name="package_type" x-model="selectedRoomType">
                <input type="hidden" name="discount_amount" x-model="discountPercent">
                <input type="hidden" name="travel_start_date" x-model="travelStartDate">

                <!-- PACKAGE SELECTION -->
                <div>
                    <label class="font-semibold">Select Package *</label>
                    <select x-model="selectedPackage" @change="loadPackage" class="w-full p-3 rounded-xl border bg-white">
                        <option value="">Select Package</option>
                        @foreach ($packages as $p)
                            <option value="{{ $p->id }}">{{ $p->package_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- TRAVELERS & PACKAGE TYPE -->
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="font-semibold">Adults</label>
                        <input type="number" x-model="adultCount" @input="calculatePrice" class="w-full p-3 rounded-xl border">
                    </div>
                    <div>
                        <label class="font-semibold">Children</label>
                        <input type="number" x-model="childCount" @input="calculatePrice" class="w-full p-3 rounded-xl border">
                    </div>
                    <div>
                        <label class="font-semibold">Package Type</label>
                        <select x-model="selectedRoomType" @change="calculatePrice" class="w-full p-3 rounded-xl border bg-white">
                            <option value="standard_price">Standard</option>
                            <option value="deluxe_price">Deluxe</option>
                            <option value="luxury_price">Luxury</option>
                            <option value="premium_price">Premium</option>
                        </select>
                    </div>
                </div>

                <!-- DISCOUNT & TAX -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold">Discount (%)</label>
                        <input type="number" x-model="discountPercent" @input="calculatePrice" class="w-full p-3 rounded-xl border">
                    </div>
                    <div>
                        <label class="font-semibold">Tax Amount</label>
                        <input type="number" x-model="taxAmount" @input="calculatePrice" class="w-full p-3 rounded-xl border">
                    </div>
                </div>

                <!-- INVOICE PREVIEW -->
                <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-blue-100">
                    <div class="p-6 md:p-8 lg:p-10 border-b border-blue-100 bg-gradient-to-r from-blue-50 to-cyan-50">
                        <div class="flex justify-between items-center">
                            <h2 class="text-3xl font-bold text-gray-900" x-text="packageData ? packageData.package_name : 'Select a package'"></h2>
                            <div class="text-right">
                                <p class="font-semibold text-gray-700">Travelers: <span x-text="adultCount + childCount"></span></p>
                                <p class="font-semibold text-gray-700">Type: <span x-text="selectedRoomTypeLabel"></span></p>
                                <p class="font-bold text-xl text-blue-700">Total: ₹ <span x-text="finalPrice.toFixed(2)"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- PACKAGE ITEM TABLE -->
                    <div class="p-6 md:p-8 lg:p-10 border-b border-blue-100">
                        <div class="overflow-x-auto rounded-2xl border border-blue-100 shadow-sm">
                            <table class="min-w-full divide-y divide-blue-100">
                                <thead class="bg-gradient-to-r from-blue-50 to-cyan-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Package Name</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Price/Person</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-blue-50">
                                    <tr class="hover:bg-blue-50 transition-colors duration-200">
                                        <td class="px-6 py-5" x-text="packageData ? packageData.package_name : '---'"></td>
                                        <td class="px-6 py-5" x-text="basePrice.toFixed(2)"></td>
                                        <td class="px-6 py-5 font-bold text-blue-700" x-text="finalPrice.toFixed(2)"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700">
                    <i class="fa-solid fa-file-invoice"></i> Generate Invoice
                </button>
            </form>
        </div>
    </div>

    <script>
        function invoiceGenerator() {
            return {
                selectedPackage: "{{ request()->package_id }}",
                packageData: null,
                selectedRoomType: "{{ request()->package_type ?? 'standard_price' }}",
                selectedRoomTypeLabel: "{{ ucfirst(str_replace('_price','',request()->package_type ?? 'standard')) }}",
                basePrice: parseFloat("{{ request()->price_per_person ?? 0 }}"),
                adultCount: parseInt("{{ request()->adult_count ?? 1 }}"),
                childCount: parseInt("{{ request()->child_count ?? 0 }}"),
                discountPercent: parseFloat("{{ request()->discount_amount ?? 0 }}"),
                taxAmount: 0,
                finalPrice: 0,
                travelStartDate: "{{ request()->travel_start_date ?? '' }}",

                loadPackage() {
                    if(!this.selectedPackage) return;
                    fetch(`/api/package/${this.selectedPackage}`)
                        .then(res => res.json())
                        .then(data => {
                            this.packageData = data;
                            this.calculatePrice();
                        });
                },

                calculatePrice() {
                    if(!this.packageData) return;
                    this.basePrice = parseFloat(this.packageData[this.selectedRoomType]) || 0;
                    let adultTotal = this.basePrice * this.adultCount;
                    let childTotal = (this.basePrice / 2) * this.childCount;
                    let subtotal = adultTotal + childTotal;
                    let discountValue = (subtotal * this.discountPercent) / 100;
                    this.finalPrice = subtotal - discountValue + parseFloat(this.taxAmount || 0);

                    // Label
                    this.selectedRoomTypeLabel = this.selectedRoomType.replace('_price','').replace(/\b\w/g, l => l.toUpperCase());
                },

                init() {
                    if(this.selectedPackage) this.loadPackage();
                    else this.calculatePrice();
                }
            }
        }
    </script>
</x-app-layout>
