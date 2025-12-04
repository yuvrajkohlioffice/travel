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
                class="col-span-8 border rounded-xl p-4 shadow-sm bg-gray-50 max-h-[500px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100">

                <p x-show="!packageData" class="text-gray-500 text-center mt-20">
                    Select a package to preview invoice details
                </p>

                <template x-if="packageData">
                    <div class="space-y-4">

                        <!-- PACKAGE INFO -->
                        <div>
                            <h3 class="text-xl font-bold text-indigo-700" x-text="packageData.package_name"></h3>
                            <p class="mt-2 text-lg">
                                <span><strong>Base Price:</strong> ₹<span x-text="packagePrice"></span></span>
                                <template x-if="itemPrice > 0">
                                    <span class="block text-sm text-gray-600">+ Item Price: ₹<span
                                            x-text="itemPrice"></span></span>
                                </template>
                            </p>
                        </div>

                        <!-- PICKUP & DETAILS -->
                        <div class="space-y-2">
                            <p><strong>Pickup:</strong> <span x-text="packageData.pickup_points"></span></p>
                            <p>
                                <strong>Type:</strong> <span x-text="packageData.packageType?.name"></span> |
                                <strong>Category:</strong> <span x-text="packageData.packageCategory?.name"></span> |
                                <strong>Difficulty:</strong> <span x-text="packageData.difficultyType?.name"></span>
                            </p>
                        </div>

                        <!-- IMAGES -->
                        <div>
                            <strong>Images:</strong>
                            <div class="flex gap-2 flex-wrap mt-2">
                                <template x-for="img in packageData.other_images_url">
                                    <img :src="img"
                                        class="w-16 h-16 rounded-lg object-cover border shadow-sm" />
                                </template>
                            </div>
                        </div>

                        <!-- PACKAGE ITEMS -->
                        <div>
                            <strong class="text-lg">Package Items:</strong>
                            <template x-for="item in packageData.packageItems" :key="item.id">
                                <label class="flex items-start gap-3 mt-3 border-b pb-3 cursor-pointer">
                                    <input type="radio" :value="item.id" x-model="selectedInvoiceItems"
                                        @change="updateInvoicePrice()"
                                        class="mt-1 h-5 w-5 text-blue-600 border-gray-300 rounded">
                                    <div class="flex-1">
                                        <p class="font-semibold text-blue-700">Item #<span x-text="item.id"></span></p>
                                        <p class="text-sm mt-1"><strong>Hotel:</strong> <span
                                                x-text="item.hotel.name"></span> (Type: <span
                                                x-text="item.hotel.type"></span>)</p>
                                        <p class="text-sm mt-1"><strong>Car:</strong> <span
                                                x-text="item.car.name"></span> (Type: <span
                                                x-text="item.car.type"></span>, Capacity: <span
                                                x-text="item.car.capacity"></span>)</p>
                                        <p class="mt-1"><strong>Price:</strong> ₹<span
                                                x-text="(item.already_price || !item.custom_price || item.custom_price == '0.00') ? '0.00 (Already Added)' : item.custom_price"></span>
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

                <!-- FINAL PRICE -->
                <div class="pt-2">
                    <strong class="text-indigo-700">Final Price:</strong>
                    <span class="text-2xl font-bold">₹<span x-text="discountedPrice"></span></span>
                    <template x-if="peopleCount > 1">
                        <p class="text-sm text-gray-600">
                            (For <span x-text="peopleCount"></span> people) <br>
                            (Single Person: ₹<span x-text="(Number(discountedPrice) / peopleCount).toFixed(2)"></span>)

                        </p>
                    </template>
                </div>

                <!-- SEND BUTTON -->
                <button @click="sendInvoice"
                    class="w-full bg-blue-600 text-white py-3 rounded-xl shadow-md hover:bg-blue-700 hover:-translate-y-0.5 transition transform">
                    <i class="fa-solid fa-file-invoice"></i> Send Invoice
                </button>

            </div>

        </div>
    </div>
</div>
