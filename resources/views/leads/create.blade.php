<x-app-layout>
    {{-- Main Container --}}
    <div x-data="leadForm()" class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-5xl">
            
            {{-- Validation Errors Alert --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r shadow-sm">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul class="list-disc ml-5 mt-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">

                {{-- Header --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Create New Lead</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Fill in the details below to add a potential client.</p>
                    </div>
                    
                    <div class="flex gap-3 mt-4 sm:mt-0">
                        {{-- Share Button (Client-side trigger) --}}
                        <button 
                            type="button"
                            @click="validateAndShare()"
                            class="flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-share-alt mr-2"></i> Share Info
                        </button>

                        <a href="{{ route('leads.index') }}" 
                           class="flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </a>
                    </div>
                </div>

                {{-- Form --}}
                <div class="p-6 md:p-8">
                    <form method="POST" action="{{ route('leads.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf

                        {{-- 1. Name --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                Client Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" x-model="lead.name" placeholder="Ex: John Doe"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                                required>
                        </div>

                        {{-- 2. Company Name --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Company Name</label>
                            <input type="text" name="company_name" x-model="lead.company_name" placeholder="Ex: Acme Corp"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        </div>

                        {{-- 3. Email --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                            <input type="email" name="email" x-model="lead.email" placeholder="client@example.com"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        </div>

                        {{-- 4. Phone Section (Grid inside Grid) --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-1">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Code <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="phone_code" x-model="lead.phone_code" placeholder="91"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="phone_number" x-model="lead.phone_number" placeholder="9876543210"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                            </div>
                        </div>

                        {{-- 5. Location Details --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">City</label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Country</label>
                            <input type="text" name="country" value="{{ old('country') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        </div>

                        {{-- 6. People Count --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                    Adults <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="people_count" value="{{ old('people_count', 1) }}" min="1"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Children</label>
                                <input type="number" name="child_count" value="{{ old('child_count', 0) }}" min="0"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                            </div>
                        </div>

                        {{-- 7. Status & Package --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Lead Status</label>
                            <select name="lead_status"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                @foreach(['Hot', 'Warm', 'Cold'] as $status)
                                    <option value="{{ $status }}" {{ old('lead_status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Interested Package</label>
                            <select name="package_id" x-model="lead.package_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                                <option value="">-- Select a Package --</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}">{{ $package->package_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 8. Additional Info --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Lead Source</label>
                            <input type="text" name="lead_source" value="{{ old('lead_source') }}" placeholder="e.g. Facebook, Referral"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Website</label>
                            <input type="url" name="website" value="{{ old('website') }}" placeholder="https://"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        </div>

                        {{-- 9. Inquiry Text (Full Width) --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Inquiry Details / Notes</label>
                            <textarea name="inquiry_text" rows="3"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">{{ old('inquiry_text') }}</textarea>
                        </div>

                        {{-- Buttons --}}
                        <div class="md:col-span-2 flex justify-end gap-3 mt-4">
                            <button type="reset" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Reset
                            </button>
                            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <i class="fas fa-save mr-2"></i> Save Lead
                            </button>
                        </div>

                    </form>
                </div>
                
                {{-- Share Modal Component Inclusion --}}
                <x-share-modal />

            </div>
        </div>
    </div>

    {{-- Script Logic --}}
    <script>
        function leadForm() {
            return {
                // Initialize with Old data for validation persistence
                lead: {
                    name: "{{ old('name', '') }}",
                    company_name: "{{ old('company_name', '') }}",
                    email: "{{ old('email', '') }}",
                    phone_code: "{{ old('phone_code', '91') }}",
                    phone_number: "{{ old('phone_number', '') }}",
                    package_id: "{{ old('package_id', '') }}"
                },
                
                // State for Share functionality
                allPackages: @json($packages ?? []),
                shareOpen: false,
                shareLeadName: "",
                leadEmail: "",
                leadPhone: "", // Combined Code + Number
                selectedPackage: "",
                selectedPackageDocs: [],
                selectedPackagePdf: null,
                selectedDocs: [],
                selectedPackageName: "",
                sending: false,

                // Computed: Check if ready to share
                get canShare() {
                    return (
                        this.lead.name.trim() !== '' &&
                        this.lead.phone_code.trim() !== '' &&
                        this.lead.phone_number.trim() !== ''
                    );
                },

                // Trigger Share Modal
                validateAndShare() {
                    if (!this.canShare) {
                        alert('Please enter at least the Name and Phone Number to share a package.');
                        return;
                    }
                    this.handleShare();
                },

                handleShare() {
                    this.shareLeadName = this.lead.name;
                    this.leadEmail = this.lead.email;
                    // Format phone for WhatsApp: Remove + or spaces, Ensure Code + Number
                    let code = this.lead.phone_code.replace(/\D/g, '');
                    let num = this.lead.phone_number.replace(/\D/g, '');
                    this.leadPhone = code + num;

                    // Set package if selected in form, otherwise default to first
                    this.selectedPackage = this.lead.package_id || (this.allPackages[0]?.id ?? "");
                    
                    if(this.selectedPackage) {
                        this.fetchPackageDocs(this.selectedPackage);
                    }
                    
                    this.shareOpen = true;
                },

                fetchPackageDocs(packageId) {
                    if(!packageId) return;
                    
                    fetch(`/packages/${packageId}/json`)
                        .then(res => res.json())
                        .then(data => {
                            let docs = data.package.package_docs_url;
                            
                            // Normalize docs to array
                            if (typeof docs === "string") docs = [docs];
                            if (!Array.isArray(docs)) docs = [];
                            
                            this.selectedPackageDocs = docs;
                            this.selectedPackagePdf = docs[0] || null;
                            this.selectedDocs = [...docs]; // Default select all
                            this.selectedPackageName = data.package.package_name;
                        })
                        .catch(err => console.error('Error fetching docs:', err));
                },

                async sendWhatsApp() {
                    if (!this.leadPhone || !this.selectedPackage || !this.selectedPackagePdf) {
                        alert("Phone number & Package PDF are required.");
                        return;
                    }

                    const payload = { 
                        recipient: this.leadPhone, 
                        text: `Hello ${this.shareLeadName}, here are the details for ${this.selectedPackageName}.`, 
                        mediaUrl: this.selectedPackagePdf 
                    };

                    this.sending = true;
                    try {
                        const res = await fetch("{{ url('whatsapp/send-media') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(payload),
                        });
                        
                        let data = await res.json();
                        
                        if (data.success || data.status === "success" || (data.message && data.message.includes("sent"))) {
                            alert("📨 WhatsApp sent successfully!");
                            this.closeShare();
                        } else {
                            alert(data.error || data.message || "Failed to send WhatsApp.");
                        }
                    } catch (err) {
                        console.error(err);
                        alert("Network error sending WhatsApp.");
                    } finally { 
                        this.sending = false; 
                    }
                },

                sendEmail() {
                    if (!this.leadEmail || !this.selectedPackage) { 
                        alert("Email Address & Package are required."); 
                        return; 
                    }

                    const payload = { 
                        lead_name: this.shareLeadName, 
                        package_id: this.selectedPackage, 
                        email: this.leadEmail, 
                        documents: this.selectedDocs 
                    };

                    this.sending = true;
                    fetch("{{ route('leads.sendPackageEmail') }}", {
                        method: "POST",
                        headers: { 
                            "Content-Type": "application/json", 
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(response => {
                        this.sending = false;
                        if (response.success) { 
                            alert("📧 Email Sent Successfully!"); 
                            this.closeShare(); 
                        } else { 
                            alert("Failed to send email: " + (response.message || 'Unknown error')); 
                        }
                    })
                    .catch(err => { 
                        this.sending = false; 
                        console.error(err); 
                        alert("System error sending email."); 
                    });
                },

                closeShare() {
                    this.shareOpen = false;
                }
            }
        }
    </script>
</x-app-layout>