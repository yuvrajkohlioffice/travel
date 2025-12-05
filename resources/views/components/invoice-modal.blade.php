<!-- INVOICE MODAL -->
<div x-show="invoiceOpen" x-transition.opacity
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">

    <div @click.outside="closeInvoice"
        class="bg-white dark:bg-gray-900 rounded-2xl p-6 w-full  relative transition-transform transform scale-95 x-show:invoiceOpen:scale-100">

        <!-- CLOSE BUTTON -->
        <button @click="closeInvoice" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>

        <!-- HEADER -->
        <div class="text-center border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Generate Invoice</h2>
            <p class="text-blue-600 font-semibold text-lg mt-1" x-text="leadName"></p>
        </div>

        <!-- GRID LAYOUT -->
        <div class="grid grid-cols-12 gap-6">

            <!-- LEFT PANEL: Inputs & Actions -->
            <div
                class="col-span-4 border rounded-xl p-6 shadow-sm bg-gray-50 dark:bg-gray-800 max-h-[650px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 space-y-4">

                <template x-if="!packageData">
                    <p class="text-gray-500 dark:text-gray-300 text-center mt-20">Select a package to see details here
                    </p>
                </template>

                <template x-if="packageData">
                    <div class="space-y-6">

                        <!-- PACKAGE INFO -->
                        <div class="border p-4 rounded-lg bg-gray-100 dark:bg-gray-700 space-y-2">
                            <h3 class="text-xl font-semibold text-blue-700" x-text="packageData.package_name"></h3>
                            <p>Days/Nights: <span
                                    x-text="packageData.package_days + ' / ' + packageData.package_nights"></span></p>
                            <p>Price: ₹<span x-text="packageData.package_price"></span></p>
                            <p>Package Type: <span x-text="packageData.packageType.name"></span></p>
                            <p>Pickup Point: <span x-text="packageData.pickup_points"></span></p>
                            <p>Category: <span x-text="packageData.packageCategory.name"></span></p>
                            <p>Difficulty: <span x-text="packageData.difficultyType.name"></span></p>
                            <!-- PDF Viewer -->
                            <a :href="packageData.package_docs_url" target="_blank"
                                class="text-blue-600 font-semibold hover:underline flex items-center gap-2">
                                <i class="fa-solid fa-up-right-from-square"></i> Open PDF in new tab
                            </a>

                            <img :src="packageData.package_banner_url" alt="Banner" class="w-full rounded-lg mt-2">
                        </div>

                        <!-- PACKAGE ITEMS -->


                    </div>
                </template>

            </div>
            <div
                class="col-span-4 border rounded-xl p-6 shadow-sm bg-gray-50 dark:bg-gray-800 max-h-[650px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 space-y-4">

                <template x-if="!packageData">
                    <p class="text-gray-500 dark:text-gray-300 text-center mt-20">Select a package to see details here
                    </p>
                </template>

                <template x-if="packageData">
                    <div class="space-y-6">

                        <!-- PEOPLE & CAR SELECTION -->
                        <div class="grid grid-cols-2 gap-4">

                            <!-- Adults -->
                            <div class="flex flex-col gap-2">
                                <label class="font-semibold">Adults</label>
                                <input type="number" x-model="peopleCount" min="1" @input="fetchFilteredItems()"
                                    class="w-full p-3 rounded-xl border bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-400">
                            </div>

                            <!-- Children -->
                            <div class="flex flex-col gap-2">
                                <label class="font-semibold">Children</label>
                                <input type="number" x-model="childCount" min="0" @input="fetchFilteredItems()"
                                    class="w-full p-3 rounded-xl border bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-400">
                            </div>

                            <!-- Car Dropdown -->
                            <div class="flex flex-col gap-2 col-span-2">
                                <label class="font-semibold">Select Car</label>
                                <select x-model="selectedCar" @change="fetchFilteredItems()"
                                    class="w-full p-3 rounded-xl border bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-400">
                                    <option value="">All Cars</option>
                                    <template x-for="car in cars" :key="car.id">
                                        <option :value="car.id"
                                            x-text="car.name + ' (' + car.capacity + ' seats)'"></option>
                                    </template>
                                </select>
                            </div>

                        </div>


                        <!-- PACKAGE ITEMS -->
                        <div>
                            <h3 class="text-xl font-semibold mb-3">Select Car & Item</h3>

                            <template x-if="packageData.packageItems.length === 0">
                                <p class="text-gray-500 dark:text-gray-300">No items found for this package.</p>
                            </template>

                            <template x-for="item in packageData.packageItems" :key="item.id">
                                <label
                                    class="flex items-start gap-4 p-4 border rounded-lg hover:border-blue-500 cursor-pointer transition">
                                    <input type="radio" :value="item.id" x-model="selectedInvoiceItems"
                                        @change="updateInvoicePrice(item)"
                                        class="mt-1 h-5 w-5 text-blue-600 border-gray-300 rounded">

                                    <div class="flex-1 space-y-2">
                                        <div class="flex justify-between items-center">
                                            <p class="font-semibold text-blue-700">Item #<span x-text="item.id"></span>
                                            </p>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-300"
                                                x-text="'₹' + (item[selectedRoomType] || 0)"></span>
                                        </div>

                                        <!-- CAR DETAILS -->
                                        <div class="bg-blue-50 dark:bg-blue-900 p-3 rounded-lg border">
                                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-1">Car
                                                Details</p>
                                            <p class="text-sm">Name: <span x-text="item.car.name"></span></p>
                                            <p class="text-sm">Type: <span x-text="item.car.type"></span></p>
                                            <p class="text-sm">Capacity: <span x-text="item.car.capacity"></span></p>
                                            <p class="text-sm">Price/Day: ₹<span
                                                    x-text="item.car.price?.per_day"></span></p>
                                        </div>

                                        <!-- PACKAGE DETAILS -->
                                        <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg border">
                                            <p class="text-sm font-semibold mb-1">Package Details</p>
                                            <p class="text-sm">Person Capacity: <span x-text="item.person_count"></span>
                                            </p>
                                            <p class="text-sm">Room Prices: Standard ₹<span
                                                    x-text="item.standard_price"></span> | Deluxe ₹<span
                                                    x-text="item.deluxe_price"></span> | Luxury ₹<span
                                                    x-text="item.luxury_price"></span> | Premium ₹<span
                                                    x-text="item.premium_price"></span></p>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>

            </div>

            <!-- RIGHT PANEL: Package & Item Details -->


            <div class="col-span-4 border rounded-xl p-6 shadow-sm bg-gray-50 dark:bg-gray-800 space-y-4">

                <!-- PACKAGE SELECT -->
                <div>
                    <label class="block font-semibold mb-1">Select Package</label>
                    <select x-model="selectedPackageInvoice" @change="fetchPackageDetails(selectedPackageInvoice)"
                        class="w-full p-3 rounded-xl border bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-400">
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
                        class="w-full p-3 rounded-xl border bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- DISCOUNT -->
                <div>
                    <label class="block font-semibold mb-1">Discount</label>
                    <select x-model="selectedDiscount" @change="calculateDiscountedPrice()"
                        class="w-full p-3 rounded-xl border bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-400">
                        <option value="0">No Discount</option>
                        <option value="5">5% Off</option>
                        <option value="10">10% Off</option>
                        <option value="15">15% Off</option>
                        <option value="20">20% Off</option>
                    </select>
                </div>

                <!-- PACKAGE TYPE -->
                <div>
                    <label class="block font-semibold mb-1">Package Type</label>
                    <select x-model="selectedRoomType" @change="updateInvoicePrice()"
                        class="w-full p-3 rounded-xl border bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-400">
                        <option value="standard_price">Standard</option>
                        <option value="deluxe_price">Deluxe</option>
                        <option value="luxury_price">Luxury</option>
                        <option value="premium_price">Premium</option>
                    </select>
                </div>

                <!-- FINAL PRICE -->
                <div class="pt-2">
                    <strong class="text-indigo-700 text-lg">Final Price:</strong>
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">₹<span
                            x-text="discountedPrice"></span></div>
                    <template x-if="peopleCount > 0">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Adults: <span x-text="peopleCount"></span>
                            —
                            ₹<span x-text="(finalPricePerAdult).toFixed(2)"></span> each</p>
                    </template>
                    <template x-if="childCount > 0">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Children: <span
                                x-text="childCount"></span>
                            — ₹<span x-text="(finalPricePerAdult/2).toFixed(2)"></span> each</p>
                    </template>
                </div>

                <!-- GENERATE BUTTON -->
                <button type="button" @click="createQuickInvoice()"
                    class="w-full bg-blue-600 text-white py-3 rounded-xl shadow-md hover:bg-blue-700 hover:-translate-y-0.5 transition transform flex items-center justify-center gap-2">
                    <i class="fa-solid fa-file-invoice"></i> Generate Invoice
                </button>
            </div>

        </div>
    </div>
</div>
