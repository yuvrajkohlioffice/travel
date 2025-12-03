<!-- SHARE LEAD MODAL -->
<div x-show="shareOpen" x-transition.opacity
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">

    <div @click.outside="closeShare" x-transition
        class="bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl space-y-5 relative border border-gray-200">

        <!-- Close Button -->
        <button @click="closeShare" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 transition">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>

        <!-- Header -->
        <div class="text-center border-b pb-3">
            <h2 class="text-2xl font-bold text-gray-800">
                Share Lead
            </h2>
            <p class="text-blue-600 font-semibold text-lg mt-1">
                <span x-text="shareLeadName"></span>
            </p>
        </div>

        <!-- Body -->
        <div class="space-y-4">

            <!-- PACKAGE DROPDOWN -->
            <div x-show="showDropdown" x-transition>
                <label class="block font-semibold text-gray-700 mb-1">
                    Select Package
                </label>

                <div class="relative">
                    <select x-model="selectedPackage"
                        class="w-full p-3 rounded-xl border bg-gray-50 focus:ring-2 focus:ring-blue-300 transition appearance-none pr-10">
                        <template x-for="pkg in allPackages" :key="pkg.id">
                            <option :value="pkg.id" x-text="pkg.package_name"></option>
                        </template>
                    </select>

                    <i class="fa-solid fa-chevron-down absolute right-3 top-3 text-gray-500"></i>
                </div>
            </div>

            <!-- SELECTED PACKAGE STATIC VIEW -->
            <div x-show="showSelectedPackage" x-transition
                class="p-4 bg-green-50 border border-green-300 rounded-xl text-green-800">

                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-box text-green-700"></i>
                    <span class="font-semibold">Selected Package:</span>
                </div>

                <p class="mt-1 ml-6 text-green-900" x-text="selectedPackageName"></p>
            </div>

            <!-- Share Buttons -->
            <div class="grid grid-cols-2 gap-4 pt-2">

                <!-- Email -->
                <button @click="sendEmail()"
                    class="flex items-center justify-center gap-2 px-4 py-3
                    bg-blue-600 text-white rounded-xl shadow-md
                    hover:bg-blue-700 hover:shadow-xl hover:-translate-y-0.5 transition-transform">
                    <i class="fa-solid fa-envelope"></i> Email
                </button>

                <!-- WhatsApp -->
                <button @click="sendWhatsApp()"
                    class="flex items-center justify-center gap-2 px-4 py-3
                    bg-green-600 text-white rounded-xl shadow-md
                    hover:bg-green-700 hover:shadow-xl hover:-translate-y-0.5 transition-transform">
                    <i class="fa-brands fa-whatsapp text-xl"></i> WhatsApp
                </button>

            </div>
        </div>

    </div>
</div>
