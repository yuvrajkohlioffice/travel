<x-app-layout>
<div x-data="leadForm()" class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">

    <div class="w-full max-w-3xl">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add Lead</h2>

                <!-- Share Button -->
                <button 
                    type="button"
                    @click="canShare ? handleShare(null, lead.name, lead.package_id, lead.email) : alert('Please fill Name, Phone & Email first')"
                    class="px-4 py-2 text-white bg-green-600 rounded-lg shadow hover:bg-green-700 transition-colors">
                    Share Package
                </button>

                <a href="{{ route('leads.index') }}"
                   class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back
                </a>
            </div>

            <!-- Form Section -->
            <div class="p-6">
                <form method="POST" action="{{ route('leads.store') }}">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                            <input type="text" name="name" x-model="lead.name"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                value="{{ old('name', $lead->name ?? '') }}" required>
                        </div>

                        <!-- Company Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Company Name</label>
                            <input type="text" name="company_name" x-model="lead.company_name"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                value="{{ old('company_name', $lead->company_name ?? '') }}">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                            <input type="email" name="email" x-model="lead.email"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                value="{{ old('email', $lead->email ?? '') }}">
                        </div>

                        <!-- Country -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Country</label>
                            <input type="text" name="country" x-model="lead.country"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                value="{{ old('country', $lead->country ?? '') }}">
                        </div>

                        <!-- District -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">District</label>
                            <input type="text" name="district" x-model="lead.district"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                value="{{ old('district', $lead->district ?? '') }}">
                        </div>

                        <!-- Phone Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Phone Code</label>
                            <input type="text" name="phone_code" x-model="lead.phone_code"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                value="{{ old('phone_code', $lead->phone_code ?? '') }}">
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Phone Number</label>
                            <input type="text" name="phone_number" x-model="lead.phone_number"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                value="{{ old('phone_number', $lead->phone_number ?? '') }}">
                        </div>

                        <!-- City -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">City</label>
                            <input type="text" name="city" x-model="lead.city"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                value="{{ old('city', $lead->city ?? '') }}">
                        </div>

                        <!-- Client Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Client Category</label>
                            <input type="text" name="client_category" x-model="lead.client_category"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                value="{{ old('client_category', $lead->client_category ?? '') }}">
                        </div>

                        <!-- Lead Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Lead Status</label>
                            <select name="lead_status" x-model="lead.lead_status"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Select Status</option>
                                <option value="Hot" {{ old('lead_status', $lead->lead_status ?? '') == 'Hot' ? 'selected' : '' }}>Hot</option>
                                <option value="Warm" {{ old('lead_status', $lead->lead_status ?? '') == 'Warm' ? 'selected' : '' }}>Warm</option>
                                <option value="Cold" {{ old('lead_status', $lead->lead_status ?? '') == 'Cold' ? 'selected' : '' }}>Cold</option>
                            </select>
                        </div>

                        <!-- Lead Source -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Lead Source</label>
                            <input type="text" name="lead_source" x-model="lead.lead_source"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                value="{{ old('lead_source', $lead->lead_source ?? '') }}">
                        </div>

                        <!-- Website -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Website</label>
                            <input type="text" name="website" x-model="lead.website"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                value="{{ old('website', $lead->website ?? '') }}">
                        </div>

                        <!-- Package -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Select Package (Optional)</label>
                            <select name="package_id" x-model="lead.package_id"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="">-- No Package --</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}"
                                        {{ old('package_id', $lead->package_id ?? '') == $package->id ? 'selected' : '' }}>
                                        {{ $package->package_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <!-- Inquiry Text -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Inquiry Text</label>
                        <textarea name="inquiry_text" x-model="lead.inquiry_text" rows="4"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('inquiry_text', $lead->inquiry_text ?? '') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <button
                        class="w-full mt-4 px-4 py-3 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">
                        Save Lead
                    </button>
                </form>
            </div>

            <!-- Share Modal -->
            <x-share-modal />
        </div>
    </div>
</div>

<script>
function leadForm() {
    return {
        // Lead form data
        lead: {
            name: "",
            phone_code: "",
            phone_number: "",
            email: "",
            package_id: "",
            inquiry_text: ""
        },

        // Packages from PHP
        allPackages: @json($packages ?? []),

        // Share Modal State
        shareOpen: false,
        shareLeadId: null,
        shareLeadName: "",
        leadEmail: "",
        selectedPackage: "",
        selectedPackageDocs: [],
        selectedPackagePdf: null,
        selectedDocs: [],
        selectedPackageName: "",
        sending: false,

        // Show Share button only when required fields are filled
        get canShare() {
            return (
                this.lead.name?.trim() &&
                this.lead.phone_code?.trim() &&
                this.lead.phone_number?.trim() &&
                this.lead.email?.trim()
            );
        },

        // OPEN SHARE POPUP
        handleShare(id, name, packageId = null, email = '') {
            this.shareLeadId = id;
            this.shareLeadName = name;
            this.leadEmail = email || this.lead.email;

            // auto select first package
            this.selectedPackage = packageId || (this.allPackages[0]?.id ?? "");

            this.fetchPackageDocs(this.selectedPackage);

            this.shareOpen = true;
        },

        // FETCH DOCS
        fetchPackageDocs(packageId) {
            fetch(`/packages/${packageId}/json`)
                .then(res => res.json())
                .then(data => {
                    let docs = data.package.package_docs_url;
                    if (typeof docs === "string") docs = [docs];
                    if (!Array.isArray(docs)) docs = [];

                    this.selectedPackageDocs = docs;
                    this.selectedPackagePdf = docs[0] || null;
                    this.selectedDocs = [...docs];
                    this.selectedPackageName = data.package.package_name;
                });
        },

        // CLOSE MODAL
        closeShare() {
            this.shareOpen = false;
            this.selectedPackageDocs = [];
            this.selectedDocs = [];
            this.selectedPackagePdf = null;
        },

        // SEND EMAIL
        sendEmail() {
            if (!this.leadEmail || !this.selectedPackage) {
                alert("Email & Package are required.");
                return;
            }

            const payload = {
                lead_name: this.shareLeadName,
                package_id: this.selectedPackage,
                email: this.leadEmail,
                documents: this.selectedDocs,
            };

            this.sending = true;

            fetch("{{ route('leads.sendPackageEmail') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(response => {
                this.sending = false;

                if (response.success) {
                    alert("📧 Package Email Sent Successfully!");
                    this.closeShare();
                } else {
                    alert("Failed to send email.");
                }
            })
            .catch(err => {
                this.sending = false;
                console.error(err);
                alert("Error sending email.");
            });
        }
    }
}
</script>
</x-app-layout>
