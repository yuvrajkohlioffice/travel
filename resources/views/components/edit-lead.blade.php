@php
    $packages = \App\Models\Package::all();
@endphp

<!-- Edit Lead Modal -->
<div x-show="editOpen" x-transition.opacity
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">

    <div @click.outside="closeEditModal"
         class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-3xl shadow-2xl p-6 relative">

        <!-- Close Button -->
        <button @click="closeEditModal"
            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>

        <!-- Header -->
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
            Edit Lead
        </h2>

        <!-- Form -->
        <form @submit.prevent="submitEdit" class="space-y-4">

            <div class="grid grid-cols-2 gap-4">

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                    <input type="text" x-model="editForm.name"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                        required>
                </div>

                <!-- Company Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Company Name</label>
                    <input type="text" x-model="editForm.company_name"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                    <input type="email" x-model="editForm.email"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Country -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Country</label>
                    <input type="text" x-model="editForm.country"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- District -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">District</label>
                    <input type="text" x-model="editForm.district"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Phone Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Phone Code</label>
                    <input type="text" x-model="editForm.phone_code"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Phone Number</label>
                    <input type="text" x-model="editForm.phone_number"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">City</label>
                    <input type="text" x-model="editForm.city"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Client Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Client Category</label>
                    <input type="text" x-model="editForm.client_category"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
                

                <!-- Lead Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Lead Status</label>
                    <select x-model="editForm.lead_status"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Select Status</option>
                        <option value="Hot">Hot</option>
                        <option value="Warm">Warm</option>
                        <option value="Cold">Cold</option>
                    </select>
                </div>
                <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Lead Status</label>
    <select x-model="editForm.status"
        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        <option value="">Select Status</option>
        <option value="Pending">Pending</option>
        <option value="Approved">Approved</option>
        <option value="Quotation Sent">Quotation Sent</option>
        <option value="Follow-up Taken">Follow-up Taken</option>
        <option value="Hot">Hot</option>
        <option value="Warm">Warm</option>
        <option value="Cold">Cold</option>
        <option value="Lost">Lost</option>
        <option value="Converted">Converted</option>
        <option value="On Hold">On Hold</option>
        <option value="Rejected">Rejected</option>
    </select>
</div>


                <!-- Lead Source -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Lead Source</label>
                    <input type="text" x-model="editForm.lead_source"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Website -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Website</label>
                    <input type="text" x-model="editForm.website"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Count of People Going</label>
                    <input type="number" x-model="editForm.people_count"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Package -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Select Package (Optional)</label>
                    <select x-model="editForm.package_id"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">-- No Package --</option>
                        @foreach ($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->package_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Inquiry Text -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Inquiry Text</label>
                    <textarea x-model="editForm.inquiry_text" rows="4"
                        class="mt-1 block w-full rounded-lg border px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
                </div>

            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full mt-2 px-4 py-3 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">
                Update Lead
            </button>

        </form>

    </div>
</div>
