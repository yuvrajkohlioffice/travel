<div x-show="shareOpen" x-transition.opacity
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">

    <div @click.outside="closeShare" x-transition
        class="bg-white dark:bg-gray-800 w-full max-w-7xl mt-16 rounded-2xl shadow-xl p-6 relative">

        <!-- Close Button -->
        <button @click="closeShare" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 transition">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>

        <!-- Header -->
        <div class="text-center border-b pb-3 mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Share Lead</h2>
            <p class="text-blue-600 font-semibold text-lg mt-1">
                <span x-text="shareLeadName"></span>
            </p>
        </div>

        <!-- Body Grid: 3 Columns -->
        <div class="grid grid-cols-12 gap-4 max-h-[75vh] overflow-y-auto p-2">

            <!-- Left: Media Preview -->
            <div class="col-span-4 border rounded-xl p-4 shadow-sm bg-white flex flex-col items-center justify-center">
                <template x-if="selectedPackagePdf">
                    <iframe :src="selectedPackagePdf" class="w-full h-64 rounded" frameborder="0"></iframe>
                </template>
                <template x-if="!selectedPackagePdf && whatsappMedia">
                    <img :src="whatsappMedia" alt="Media Preview" class="w-full h-64 object-cover rounded">
                </template>
                <template x-if="!selectedPackagePdf && !whatsappMedia">
                    <p class="text-gray-500 text-center mt-20">Select a document or media to preview</p>
                </template>
            </div>

            <!-- Center: Message Preview -->
            <div class="col-span-4 border rounded-xl p-4 shadow-sm bg-gray-50 space-y-4 overflow-y-auto">
                <template x-if="sendEmailChecked">
                    <div class="p-2 border rounded bg-white">
                        <p class="font-semibold text-gray-600">Email Subject:</p>
                        <p x-text="emailSubject" class="text-gray-800"></p>
                        <p class="font-semibold text-gray-600 mt-2">Email Body:</p>
                        <p x-html="emailBody" class="text-gray-800 whitespace-pre-line"></p>
                        <template x-if="emailMedia">
                            <img :src="emailMedia" alt="Email Media" class="mt-2 w-full rounded">
                        </template>
                    </div>
                </template>

                <template x-if="sendWhatsAppChecked">
                    <div class="p-2 border rounded bg-white">
                        <p class="font-semibold text-gray-600">WhatsApp Message:</p>
                        <p x-html="whatsappMessage" class="text-gray-800 whitespace-pre-line"></p>
                        <template x-if="whatsappMedia">
                            <img :src="whatsappMedia" alt="WhatsApp Media" class="mt-2 w-full rounded">
                        </template>
                    </div>
                </template>
            </div>

            <!-- Right: Controls -->
            <div class="col-span-4 border rounded-xl p-4 shadow-sm bg-gray-50 space-y-4">
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

                <!-- Media Type Selection -->
                <div class="mt-4">
                    <label class="block font-semibold text-gray-700 mb-1">Select Media Type</label>
                    <select x-model="selectedMediaType" class="w-full p-2 border rounded-xl">
                        <option value="banner">Template</option>
                        <option value="banner">Banner</option>
                        <option value="docs">Document</option>
                    </select>
                </div>

                <!-- Send Method Checkboxes -->
                <div class="space-y-2 mt-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" x-model="sendEmailChecked" class="rounded border-gray-300">
                        <span class="font-medium text-gray-700">Send Email</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" x-model="sendWhatsAppChecked" class="rounded border-gray-300">
                        <span class="font-medium text-gray-700">Send WhatsApp</span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col gap-3 mt-4">
                    <button @click="sendSelected()"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-indigo-600 text-white rounded-xl shadow-md hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-transform">
                        <i class="fa-solid fa-paper-plane"></i> Send Selected
                    </button>
                    <button @click="closeShare"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-gray-300 text-gray-800 rounded-xl shadow-md hover:bg-gray-400 hover:shadow-xl hover:-translate-y-0.5 transition-transform">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
