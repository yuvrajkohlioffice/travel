<div x-show="invoiceOpen" x-transition.opacity
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">

    <div @click.outside="closeInvoice"
        class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-5xl shadow-xl relative transition-transform transform scale-95 x-show:invoiceOpen:scale-100">

        <!-- CLOSE BUTTON -->
        <button @click="closeInvoice" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 transition">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>

        <!-- HEADER -->
        <div class="text-center border-b pb-3">
            <h2 class="text-2xl font-bold text-gray-800">Send Invoice</h2>
            <p class="text-blue-600 font-semibold text-lg mt-1" x-text="leadName"></p>
        </div>

        <!-- GRID -->
        <div class="grid grid-cols-12 gap-6 mt-4">

            <!-- RIGHT PANEL: Invoice Preview -->
            <div
                class="col-span-8 border rounded-xl p-4 shadow-sm bg-gray-50 max-h-[600px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100">
                <p x-show="!packageData" class="text-gray-500 text-center mt-20">
                    Select a package to preview invoice details
                </p>

                <template x-if="packageData">
                    <div class="space-y-4">
                        <!-- PACKAGE INFO -->



                        <div class="grid grid-cols-2 gap-4">

                            <!-- Adults -->
                            <div class="flex flex-col gap-2">
                                <label class="font-semibold">Adults</label>
                                <input type="number" x-model="peopleCount"
                                    class="w-full p-3 rounded-xl border bg-white focus:ring-2 focus:ring-blue-300">
                            </div>

                            <!-- Children -->
                            <div class="flex flex-col gap-2">
                                <label class="font-semibold">Children</label>
                                <input type="number" x-model="childCount"
                                    class="w-full p-3 rounded-xl border bg-white focus:ring-2 focus:ring-blue-300">
                            </div>

                            <!-- Cars Dropdown -->
                            <div class="flex flex-col gap-2 col-span-2">
                                <label class="font-semibold">Select Car</label>
                                <select x-model="selectedCar"
                                    class="w-full p-3 rounded-xl border bg-white focus:ring-2 focus:ring-blue-300">
                                    <option value="">All Cars</option>

                                    <!-- Dynamically load available cars -->
                                    <template x-for="car in cars" :key="car.id">
                                        <option :value="car.id"
                                            x-text="car.name + ' (' + car.capacity + ' seats)'"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Button full width beneath -->
                            <div class="col-span-2">
                                <button type="button" @click="fetchFilteredItems()"
                                    class="w-full bg-green-600 text-white py-3 rounded-xl shadow hover:bg-green-700 transition">
                                    🔍 Search Items
                                </button>
                            </div>

                        </div>




                        <div>
                            <strong class="text-lg">Select Car & Item:</strong>

                            <template x-if="packageData.packageItems.length === 0">
                                <p class="text-gray-500 mt-2">No package found for the selected people count, and car
                                    create it or Give Custom.</p>
                            </template>

                            <template x-for="item in packageData.packageItems" :key="item.id">
                                <label class="flex items-start gap-3 mt-3 border-b pb-3 cursor-pointer">

                                    <!-- SELECT ITEM -->
                                    <input type="radio" :value="item.id" x-model="selectedInvoiceItems"
                                        @change="updateInvoicePrice(item)"
                                        class="mt-1 h-5 w-5 text-blue-600 border-gray-300 rounded">

                                    <div class="flex-1">
                                        <p class="font-semibold text-blue-700">
                                            Item #<span x-text="item.id"></span>
                                        </p>

                                        <!-- ⭐ Car Details -->
                                        <div class="mt-1 bg-blue-50 p-2 rounded-lg border">
                                            <p class="text-sm font-semibold text-blue-900">
                                                Car Details
                                            </p>

                                            <p class="text-sm">
                                                <strong>Name:</strong> <span x-text="item.car.name"></span>
                                            </p>
                                            <p class="text-sm">
                                                <strong>Type:</strong> <span x-text="item.car.type"></span>
                                            </p>
                                            <p class="text-sm">
                                                <strong>Capacity:</strong> <span x-text="item.car.capacity"></span>
                                            </p>

                                            <p class="text-sm">
                                                <strong>Price Per Day:</strong> ₹<span
                                                    x-text="item.car.price?.per_day"></span>
                                            </p>
                                        </div>

                                        <!-- ⭐ Person Capacity -->
                                        <p class="text-sm mt-2">
                                            <strong>Person Capacity:</strong>
                                            <span x-text="item.person_count"></span>
                                        </p>

                                        <!-- ⭐ Room Prices -->
                                        <p class="mt-2 text-sm">
                                            <strong>Room Prices:</strong><br>
                                            Standard: ₹<span x-text="item.standard_price"></span> |
                                            Deluxe: ₹<span x-text="item.deluxe_price"></span> |
                                            Luxury: ₹<span x-text="item.luxury_price"></span> |
                                            Premium: ₹<span x-text="item.premium_price"></span>
                                        </p>
                                    </div>
                                </label>
                            </template>
                        </div>

                    </div>
                </template>
            </div>

            <!-- LEFT PANEL: Inputs & Actions -->
            <div class="col-span-4 border rounded-xl p-6 shadow-sm bg-gray-50 space-y-4">
                <!-- PACKAGE SELECT -->
                <div>
                    <label class="block font-semibold mb-1">Select Package</label>
                    <select x-model="selectedPackageInvoice" @change="fetchPackageDataAPI"
                        class="w-full p-3 rounded-xl border bg-white focus:ring-2 focus:ring-blue-300">
                        <option value="">Select Package</option>
                        <template x-for="pkg in packages" :key="pkg.id">
                            <option :value="pkg.id" x-text="pkg.package_name"></option>
                        </template>
                    </select>
                </div>

                <!-- TRAVEL DATE -->
                <div>
                    <label class="block font-semibold mb-1">Start Travel Date</label>
                    <input type="date" x-model="travelStartDate"
                        class="w-full p-3 rounded-xl border bg-white focus:ring-2 focus:ring-blue-300">
                </div>

                <!-- DISCOUNT -->
                <div>
                    <label class="block font-semibold mb-1">Select Discount</label>
                    <select x-model="selectedDiscount" @change="calculateDiscountedPrice()"
                        class="w-full p-3 rounded-xl border bg-white focus:ring-2 focus:ring-blue-300">
                        <option value="0">No Discount</option>
                        <option value="5">5% Off</option>
                        <option value="10">10% Off</option>
                        <option value="15">15% Off</option>
                        <option value="20">20% Off</option>
                    </select>
                </div>

                <!-- PACKAGE TYPE -->
                <div>
                    <label class="block font-semibold mb-1">Select Package Type</label>
                    <select x-model="selectedRoomType" @change="updateInvoicePrice()"
                        class="w-full p-3 rounded-xl border bg-white focus:ring-2 focus:ring-blue-300">
                        <option value="standard_price">Standard</option>
                        <option value="deluxe_price">Deluxe</option>
                        <option value="luxury_price">Luxury</option>
                        <option value="premium_price">Premium</option>
                    </select>
                </div>

                <!-- FINAL PRICE -->
                <div class="pt-2">
                    <strong class="text-indigo-700">Final Price:</strong>
                    <span class="text-2xl font-bold">₹<span x-text="discountedPrice"></span></span>
                    <template x-if="peopleCount > 0">
                        <p class="text-sm text-gray-600">Adults: <span x-text="peopleCount"></span> — ₹<span
                                x-text="(finalPricePerAdult).toFixed(2)"></span> each</p>
                    </template>
                    <template x-if="childCount > 0">
                        <p class="text-sm text-gray-600">Children: <span x-text="childCount"></span> — ₹<span
                                x-text="(finalPricePerAdult/2).toFixed(2)"></span> each</p>
                    </template>
                </div>

                <!-- SEND BUTTON -->
                <button type="button"
                    @click="
                            let params = new URLSearchParams({
                                package_id: selectedPackageInvoice,
                                package_type: selectedRoomType,
                                adult_count: peopleCount,
                                child_count: childCount,
                                discount_amount: selectedDiscount,
                                
                                price_per_person: discountedPrice,
                                travel_start_date: travelStartDate
                            }).toString();
                            window.location.href = '{{ route('invoices.create') }}?' + params;
                        "
                    class="w-full bg-blue-600 text-white py-3 rounded-xl shadow-md hover:bg-blue-700 hover:-translate-y-0.5 transition transform">
                    <i class="fa-solid fa-file-invoice"></i> Generate Invoice
                </button>
            </div>
        </div>
    </div>


</div>
