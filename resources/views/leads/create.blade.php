<x-app-layout>
<div x-data="leadForm()" class="ml-64 flex justify-center items-start min-h-screen p-6">

    <div class="w-full max-w-5xl">
        <div class=" shadow-xl rounded-2xl overflow-hidden">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white mb-3 sm:mb-0">Add Lead</h1>
                <div class="flex space-x-3">
                    <button 
                        type="button"
                        @click="canShare ? handleShare(null, lead.name, lead.package_id, lead.email) : alert('Please fill Name, Phone & Email first')"
                        class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition-colors">
                        <i class="fas fa-share mr-2"></i> Share Package
                    </button>
                    <a href="{{ route('leads.index') }}" 
                       class="flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-sm transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </div>

            <!-- Form Section -->
            <div class="p-6">
                <form method="POST" action="{{ route('leads.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @csrf

                    @php
                        $fields = [
                            ['label'=>'Name','name'=>'name','type'=>'text','required'=>true],
                            ['label'=>'Company Name','name'=>'company_name','type'=>'text'],
                            ['label'=>'Email','name'=>'email','type'=>'email'],
                            ['label'=>'Country','name'=>'country','type'=>'text'],
                            ['label'=>'District','name'=>'district','type'=>'text'],
                            ['label'=>'Phone Code','name'=>'phone_code','type'=>'text'],
                            ['label'=>'Phone Number','name'=>'phone_number','type'=>'text'],
                            ['label'=>'City','name'=>'city','type'=>'text'],
                            ['label'=>'Client Category','name'=>'client_category','type'=>'text'],
                            ['label'=>'Lead Source','name'=>'lead_source','type'=>'text'],
                            ['label'=>'Website','name'=>'website','type'=>'text'],
                            ['label'=>'Count of People Going','name'=>'people_count','type'=>'number','required'=>true],
                            ['label'=>'Count of Child Going','name'=>'child_count','type'=>'number'],
                        ];
                    @endphp

                    @foreach($fields as $field)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">{{ $field['label'] }}</label>
                            <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" x-model="lead.{{ $field['name'] }}"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                                   value="{{ old($field['name'], $lead->{$field['name']} ?? '') }}"
                                   @if(!empty($field['required'])) required @endif>
                        </div>
                    @endforeach

                    <!-- Lead Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Lead Status</label>
                        <select name="lead_status" x-model="lead.lead_status"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                            <option value="">Select Status</option>
                            @foreach(['Hot','Warm','Cold'] as $status)
                                <option value="{{ $status }}" {{ old('lead_status', $lead->lead_status ?? '') == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Package -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Select Package (Optional)</label>
                        <select name="package_id" x-model="lead.package_id"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                            <option value="">-- No Package --</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}" {{ old('package_id', $lead->package_id ?? '') == $package->id ? 'selected' : '' }}>
                                    {{ $package->package_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Inquiry Text -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Inquiry Text</label>
                        <textarea name="inquiry_text" x-model="lead.inquiry_text" rows="4"
                                  class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">{{ old('inquiry_text', $lead->inquiry_text ?? '') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="sm:col-span-2">
                        <button type="submit"
                                class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition-colors flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> Save Lead
                        </button>
                    </div>

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
        lead: {
            name: "",
            phone_code: "",
            phone_number: "",
            email: "",
            package_id: "",
            inquiry_text: ""
        },
        allPackages: @json($packages ?? []),
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
        get canShare() {
            return (
                this.lead.name?.trim() &&
                this.lead.phone_code?.trim() &&
                this.lead.phone_number?.trim() &&
                this.lead.email?.trim()
            );
        },
        handleShare(id, name, packageId = null, email = '') {
            this.shareLeadId = id;
            this.shareLeadName = name;
            this.leadEmail = email || this.lead.email;
            this.selectedPackage = packageId || (this.allPackages[0]?.id ?? "");
            this.fetchPackageDocs(this.selectedPackage);
            this.shareOpen = true;
        },
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
        async sendWhatsApp() {
            if (!this.leadPhone || !this.selectedPackage || !this.selectedPackagePdf) {
                alert("Phone number & Package PDF are required.");
                return;
            }
            const payload = { recipient: this.leadPhone, text: this.whatsappMessage?.trim() || "Please check the attached package details.", mediaUrl: this.selectedPackagePdf };
            this.sending = true;
            try {
                const res = await fetch("{{ url('whatsapp/send-media') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: "same-origin",
                    body: JSON.stringify(payload),
                });
                let data = await res.json();
                if (data.success || data.status === "success" || (data.message?.toLowerCase().includes("sent successfully"))) {
                    alert("📨 WhatsApp sent successfully!");
                    this.closeShare();
                    return;
                }
                alert(data.error ?? data.message ?? "Failed to send WhatsApp message.");
            } catch (err) {
                console.error(err);
                alert("Error sending WhatsApp.");
            } finally { this.sending = false; }
        },
        closeShare() {
            this.shareOpen = false;
            this.selectedPackageDocs = [];
            this.selectedDocs = [];
            this.selectedPackagePdf = null;
        },
        sendEmail() {
            if (!this.leadEmail || !this.selectedPackage) { alert("Email & Package are required."); return; }
            const payload = { lead_name: this.shareLeadName, package_id: this.selectedPackage, email: this.leadEmail, documents: this.selectedDocs };
            this.sending = true;
            fetch("{{ route('leads.sendPackageEmail') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(response => {
                this.sending = false;
                if (response.success) { alert("📧 Package Email Sent Successfully!"); this.closeShare(); }
                else { alert("Failed to send email."); }
            })
            .catch(err => { this.sending = false; console.error(err); alert("Error sending email."); });
        }
    }
}
</script>
</x-app-layout>
