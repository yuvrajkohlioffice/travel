<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-6xl w-full p-4 md:p-6 lg:p-8">
            <form method="POST" action="{{ route('invoices.store') }}" x-data="invoiceGenerator()" x-init="init()" class="space-y-6">
                @csrf

                <!-- Reactive Hidden Inputs -->
                <template x-for="(value, name) in hiddenFields" :key="name">
                    <input type="hidden" :name="name" :value="value">
                </template>

                <!-- Lead Info -->
                @if($lead)
                <div class="bg-white rounded-xl p-4 shadow mb-4">
                    <p class="font-semibold">Lead: {{ $lead->name }}</p>
                    <p class="text-gray-600">Email: {{ $lead->email ?? '---' }}</p>
                    <p class="text-gray-600">Phone: {{ $lead->phone_code . $lead->phone_number ?? '---' }}</p>
                    <p class="text-gray-600">Address: {{ $lead->address ?? '---' }}</p>
                </div>
                @endif

                <!-- Package Selection -->
                <div>
                    <label class="font-semibold">Select Package *</label>
                    <select x-model="selectedPackage" @change="loadPackage" class="w-full p-3 rounded-xl border bg-white">
                        <option value="">Select Package</option>
                        @foreach ($packages as $p)
                            <option value="{{ $p->id }}">{{ $p->package_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Travelers & Package Type -->
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="font-semibold">Adults</label>
                        <input type="number" min="0" x-model.number="adultCount" class="w-full p-3 rounded-xl border">
                    </div>
                    <div>
                        <label class="font-semibold">Children</label>
                        <input type="number" min="0" x-model.number="childCount" class="w-full p-3 rounded-xl border">
                    </div>
                    <div>
                        <label class="font-semibold">Package Type</label>
                        <select x-model="selectedRoomType" class="w-full p-3 rounded-xl border bg-white">
                            <option value="standard_price">Standard</option>
                            <option value="deluxe_price">Deluxe</option>
                            <option value="luxury_price">Luxury</option>
                            <option value="premium_price">Premium</option>
                        </select>
                    </div>
                </div>

                <!-- Additional Travelers -->
                <div>
                    <label class="font-semibold">Additional Travelers (JSON)</label>
                    <textarea x-model="additionalTravelers" placeholder='[{"name":"John Doe","age":30},{"name":"Jane Doe","age":28}]' class="w-full p-3 rounded-xl border h-24"></textarea>
                    <p class="text-sm text-gray-500">Enter additional travelers in JSON format (name, age).</p>
                </div>

                <!-- Discount -->
                <div>
                    <label class="font-semibold">Discount (%)</label>
                    <input type="number" min="0" x-model.number="discountPercent" class="w-full p-3 rounded-xl border">
                </div>

                <!-- Travel Date -->
                <div>
                    <label class="font-semibold">Travel Start Date</label>
                    <input type="date" x-model="travelStartDate" class="w-full p-3 rounded-xl border">
                </div>

                <!-- Additional Details -->
                <div>
                    <label class="font-semibold">Additional Details</label>
                    <textarea x-model="additionalDetails" placeholder="Any extra notes or information" class="w-full p-3 rounded-xl border h-24"></textarea>
                </div>

                <!-- Invoice Preview -->
                <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-blue-100">
                    <div class="p-6 md:p-8 lg:p-10 border-b border-blue-100 bg-gradient-to-r from-blue-50 to-cyan-50">
                        <div class="flex justify-between items-center">
                            <h2 class="text-3xl font-bold text-gray-900" x-text="packageData?.package_name || 'Select a package'"></h2>
                            <div class="text-right">
                                <p class="font-semibold text-gray-700">Travelers: <span x-text="totalTravelers"></span></p>
                                <p class="font-semibold text-gray-700">Type: <span x-text="roomTypeLabel"></span></p>
                                <p class="font-bold text-xl text-blue-700">Total: ₹ <span x-text="finalPrice.toFixed(2)"></span></p>
                                <p class="text-sm text-gray-500">Tax (5%): ₹ <span x-text="taxAmount.toFixed(2)"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Package Item Table -->
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
                                        <td class="px-6 py-5" x-text="packageData?.package_name || '---'"></td>
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
                selectedPackage: "{{ $prefill['package_id'] ?? '' }}",
                packageData: null,
                selectedRoomType: "{{ $prefill['package_type'] ?? 'standard_price' }}",
                adultCount: parseInt("{{ $prefill['adult_count'] ?? 1 }}"),
                childCount: parseInt("{{ $prefill['child_count'] ?? 0 }}"),
                discountPercent: parseFloat("{{ $prefill['discount_amount'] ?? 0 }}"),
                travelStartDate: "{{ $prefill['travel_start_date'] ?? now()->toDateString() }}",
                additionalTravelers: "[]",
                additionalDetails: "",

                hiddenFields: {},

                get basePrice() {
                    return parseFloat(this.packageData?.package_price || 0);
                },

                get totalTravelers() {
                    return this.adultCount + this.childCount + JSON.parse(this.additionalTravelers || "[]").length;
                },

                get roomTypeLabel() {
                    return this.selectedRoomType.replace('_price','').replace(/\b\w/g,l=>l.toUpperCase());
                },

                get subtotal() {
                    const adultTotal = this.basePrice * this.adultCount;
                    const childTotal = (this.basePrice / 2) * this.childCount;
                    return adultTotal + childTotal;
                },

                get taxAmount() {
                    return this.subtotal * 0.05;
                },

                get finalPrice() {
                    const discountValue = this.subtotal * this.discountPercent / 100;
                    return this.subtotal - discountValue + this.taxAmount;
                },

                loadPackage() {
                    if(!this.selectedPackage) return;
                    fetch(`/packages/${this.selectedPackage}/json`)
                        .then(res => res.ok ? res.json() : Promise.reject(res))
                        .then(data => this.packageData = data.package)
                        .catch(err => console.error("Failed to load package:", err));
                },

                updateHiddenFields() {
                    this.hiddenFields = {
                        package_id: this.selectedPackage,
                        adult_count: this.adultCount,
                        child_count: this.childCount,
                        package_type: this.selectedRoomType,
                        price_per_person: this.basePrice,
                        discount_amount: this.discountPercent,
                        tax_amount: this.taxAmount,
                        travel_start_date: this.travelStartDate,
                        additional_travelers: this.additionalTravelers,
                        additional_details: this.additionalDetails
                    };
                },

                init() {
                    if(this.selectedPackage) this.loadPackage();
                    this.$watch([
                        'selectedPackage','adultCount','childCount','selectedRoomType',
                        'discountPercent','travelStartDate','additionalTravelers','additionalDetails'
                    ], ()=>this.updateHiddenFields());
                    this.updateHiddenFields();
                }
            }
        }
    </script>
</x-app-layout>
