<div x-show="shareOpen" x-transition.opacity
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div x-transition class="bg-white w-full max-w-md rounded-2xl p-6 shadow-xl space-y-4">
                <!-- Header -->
                <div class="flex justify-between items-center border-b pb-3">
                    <h2 class="text-xl font-bold text-gray-800">
                        Share Lead – <span x-text="shareLeadName"></span>
                    </h2>
                    <button @click="closeShare" class="text-gray-500 hover:text-black text-xl">
                        &times;
                    </button>
                </div>

                <div class="space-y-4">

                    <!-- Package Dropdown -->
                    <div x-show="showDropdown" x-transition>
                        <label class="block font-semibold text-gray-700 mb-1">
                            Select Package
                        </label>

                        <select x-model="selectedPackage"
                            class="w-full p-3 rounded-lg border bg-gray-50 focus:ring focus:ring-blue-300 transition">
                            <template x-for="pkg in allPackages" :key="pkg.id">
                                <option :value="pkg.id" x-text="pkg.package_name"></option>
                            </template>
                        </select>
                    </div>
                    <!-- Show Selected Package (instead of dropdown) -->
                    <div x-show="showSelectedPackage" x-transition
                        class="p-3 bg-green-50 border border-green-300 rounded-lg text-green-800 font-medium">
                        Selected Package:
                        <span class="font-semibold" x-text="selectedPackageName"></span>
                    </div>

                    <!-- Share Buttons -->
                    <div class="grid grid-cols-2 gap-4 pt-2">

                        <!-- Email -->
                        <button @click="sendEmail()"
                            class="flex items-center justify-center gap-2 px-4 py-3
                    bg-blue-600 text-white rounded-xl shadow
                    hover:bg-blue-700 hover:shadow-xl transition">
                            <i class="fa-solid fa-envelope"></i> Email
                        </button>

                        <!-- WhatsApp -->
                        <button @click="sendWhatsApp()"
                            class="flex items-center justify-center gap-2 px-4 py-3
                    bg-green-600 text-white rounded-xl shadow
                    hover:bg-green-700 hover:shadow-xl transition">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp
                        </button>

                    </div>

                </div>
            </div>
        </div>  