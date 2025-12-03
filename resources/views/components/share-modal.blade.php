<div x-data="shareLeadModal()" x-show="shareOpen" x-transition.opacity
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">

    <div @click.outside="closeShare" x-transition
         class="bg-white dark:bg-gray-800 w-full max-w-6xl mt-16 rounded-2xl shadow-xl p-6 relative">

        <!-- Close Button -->
        <button @click="closeShare" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 transition">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>

        <!-- Header -->
        <div class="text-center border-b pb-3">
            <h2 class="text-2xl font-bold text-gray-800">Share Lead</h2>
            <p class="text-blue-600 font-semibold text-lg mt-1">
                <span x-text="shareLeadName"></span>
            </p>
        </div>

        <!-- Body: Grid -->
        <div class="grid grid-cols-12 gap-6 mt-4">

            <!-- Right Column: PDF Preview -->
            <div class="col-span-8 overflow-hidden border rounded-xl p-4 shadow-sm bg-white">
                <template x-if="selectedPackagePdf">
                    <iframe :src="selectedPackagePdf" class="w-full h-full min-h-[400px]" frameborder="0"></iframe>
                </template>
                <p x-show="!selectedPackagePdf" class="text-gray-500 text-center mt-20">Select a document to preview</p>
            </div>

            <!-- Left Column: Dropdown + Send Buttons -->
            <div class="col-span-4 border rounded-xl p-6 space-y-4 shadow-sm bg-gray-50">

                <!-- PACKAGE DROPDOWN -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Select Package</label>
                    <div class="relative">
                        <select x-model="selectedPackage" @change="fetchPackageDocs(selectedPackage)"
                                class="w-full p-3 rounded-xl border bg-gray-50 focus:ring-2 focus:ring-blue-300 transition appearance-none pr-10">
                            <template x-for="pkg in allPackages" :key="pkg.id">
                                <option :value="pkg.id" x-text="pkg.package_name"></option>
                            </template>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-3 text-gray-500"></i>
                    </div>
                </div>

               
                <!-- Send Buttons -->
                <div class="flex flex-col gap-3 mt-4">
                    <button @click="sendEmail()"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white rounded-xl shadow-md hover:bg-blue-700 hover:shadow-xl hover:-translate-y-0.5 transition-transform">
                        <i class="fa-solid fa-envelope"></i> Send Email
                    </button>
                    <button @click="sendWhatsApp()"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white rounded-xl shadow-md hover:bg-green-700 hover:shadow-xl hover:-translate-y-0.5 transition-transform">
                        <i class="fa-brands fa-whatsapp text-xl"></i> Send WhatsApp
                    </button>
                    <button @click="sendBoth()"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-indigo-600 text-white rounded-xl shadow-md hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-transform">
                        <i class="fa-solid fa-paper-plane"></i> Send Both
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

