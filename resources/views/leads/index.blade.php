<x-app-layout>
    <div x-data="leadModals()" x-cloak class="min-h-screen bg-gradient-to-br from-gray-100 via-white to-gray-200">
        <div class="ml-64 flex justify-center items-start min-h-screen p-6">
            <div class="w-full max-w-7xl space-y-6">
                <!-- Header -->
                <div
                    class="flex flex-col md:flex-row md:justify-between md:items-center gap-4  from-blue-500 to-indigo-600  p-6 rounded-xl shadow-lg">
                    <!-- Title -->
                    <h2 class="text-3xl font-bold flex items-center gap-3">
                        <i class="fa-solid fa-people-group"></i> Leads
                    </h2>
                    <!-- Actions -->
                    <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                        <!-- Import Template -->
                        <a href="/Example-Import-Leads.xlsx"
                            class="px-4 py-2 bg-white text-blue-600 font-semibold rounded-lg shadow hover:bg-gray-100 transition">
                            Import Template
                        </a>
                        <!-- Import File Form -->
                        <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data"
                            class="flex items-center gap-2">
                            @csrf
                            <input type="file" name="file" accept=".xlsx,.csv" required
                                class="text-sm text-white file:bg-white file:border-0 file:rounded-lg file:px-3 file:py-1 file:font-semibold file:hover:bg-white cursor-pointer" />
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                                Import Leads
                            </button>
                        </form>
                        <!-- Add Lead Button -->
                        <a href="{{ route('leads.create') }}"
                            class="px-5 py-2 bg-white text-blue-600 font-semibold rounded-lg shadow hover:bg-gray-100 transition">
                            + Add Lead
                        </a>
                    </div>
                </div>
                <!-- Success Message -->
                @if (session('success'))
                    <div class="p-4 bg-green-500 text-white rounded-lg shadow">
                        {{ session('success') }}
                    </div>
                @endif
                <div x-show="selected.length > 0" class="mb-4 flex items-center gap-3">
                    <span class="font-medium">Assign Selected Leads:</span>
                    <select x-model="bulkUser" class="border border-gray-300 rounded px-2 py-1">
                        <option value="">Select User</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <button @click="assignUser()"
                        class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
                        Assign
                    </button>
                    <span class="text-sm text-gray-500" x-text="selected.length + ' lead(s) selected'"></span>
                </div>


                <!-- Leads Table -->
                <div class="bg-white rounded-xl shadow overflow-x-auto p-4">
                    <x-data-table id="Leads-table" :headers="[
                        '#',
                        'ID',
                        'Client Info',
                        'Country',
                        'Reminder',
                        'Inquiry For',
                        'Proposal',
                        'Status',
                        'Action',
                    ]" :excel="true" :print="true" title="Leads"
                        resourceName="Leads">

                        @foreach ($leads as $lead)
                            @php
                                // Lead Status Colors
                                $statusColors = [
                                    'Hot' => 'bg-red-500',
                                    'Warm' => 'bg-yellow-400',
                                    'Cold' => 'bg-gray-400',
                                    'Interested' => 'bg-green-500',
                                ];
                                $statusClass = $statusColors[$lead->lead_status] ?? 'bg-gray-300';

                                // Lead Stage/Type Colors
                                $stageColors = [
                                    'Pending' => 'bg-blue-400',
                                    'Approved' => 'bg-green-500',
                                    'Quotation Sent' => 'bg-indigo-500',
                                    'Follow-up Taken' => 'bg-purple-500',
                                    'Converted' => 'bg-teal-500',
                                    'Lost' => 'bg-gray-500',
                                    'On Hold' => 'bg-orange-400',
                                    'Rejected' => 'bg-red-600',
                                ];
                                $stageClass = $stageColors[$lead->status] ?? 'bg-gray-300';
                            @endphp

                            <tr class="border-b hover:bg-gray-50 transition-colors">
                                <!-- Checkbox + User Dropdown -->
                                <td class="p-3 text-center">
                                    <input type="checkbox" value="{{ $lead->id }}" @change="toggleLead($event)"
                                        class="form-checkbox h-4 w-4 text-blue-600 rounded">
                                </td>
                                <!-- Serial -->
                                <td class="p-3 text-center">{{ $loop->iteration }}</td>

                                <!-- Client Info -->
                                <td class="p-3 ">
                                    <div class="font-semibold text-gray-800">{{ $lead->name }} <span
                                            class="px-3 py-1 rounded-full text-white text-xs {{ $statusClass }}">
                                            {{ $lead->lead_status ?? 'N/A' }}
                                        </span> <button @click="openEditModal({{ $lead->id }})"
                                            class="btn-edit mx-1" title="Update  ({{ $lead->name }})">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button></div>

                                    <hr>
                                    <a href="mailto:{{ $lead->email }}"
                                        class="text-blue-600 hover:underline">{{ $lead->email }}</a>
                                    @php
                                        $masked =
                                            str_repeat('*', strlen($lead->phone_number) - 4) .
                                            substr($lead->phone_number, -4);
                                        $created = \Carbon\Carbon::parse($lead->created_at);
                                        $daysFloor = floor($created->diffInRealDays());
                                    @endphp
                                    <div class="text-green-600 font-mono">+{{ $lead->phone_code }} {{ $masked }}
                                    </div>

                                    <div class="text-gray-500 text-sm">
                                        {{ $lead->created_at->format('d-M-y') }} •
                                        <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded">{{ $daysFloor }}
                                            days old</span>
                                    </div>
                                </td>

                                <!-- Country -->
                                <td class="p-3 text-center">
                                    <div>{{ $lead->country }}</div>
                                    <div>{{ $lead->district }}</div>
                                    <div>{{ $lead->city }}</div>
                                </td>

                                <!-- Reminder -->
                                <td class="p-3 text-center">
                                    <button @click="openFollowModal({{ $lead->id }}, '{{ $lead->name }}')"
                                        class="border-2 border-green-500 text-green-500 px-4 py-1 rounded-lg hover:bg-green-500 hover:text-white transition duration-300">
                                        <i class="fa-solid fa-phone-volume mr-1"></i> Followup
                                    </button>
                                    @if ($lead->lastFollowup)
                                        <div class="text-sm text-gray-700">
                                            <strong>Last:</strong> {{ $lead->lastFollowup->reason }}<br>
                                            <strong>By:</strong> {{ $lead->lastFollowup->user->name ?? 'N/A' }}<br>
                                            <span
                                                class="text-xs text-gray-500">{{ $lead->lastFollowup->created_at->format('d M Y h:i A') }}</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Package / Inquiry -->
                                <<td class="p-3 text-center"
                                    title="{{ $lead->package->package_name ?? $lead->inquiry_text }}">
                                    {{ $lead->package->package_name ?? \Illuminate\Support\Str::limit($lead->inquiry_text, 20) }}
                                    </td>

                                    <!-- Proposal Buttons -->
                                    <td class="p-3 text-center">
                                        <button
                                            @click="handleShare({{ $lead->id }}, '{{ $lead->name }}', '{{ $lead->package->id ?? '' }}','{{ $lead->email }}')"
                                            class="border-2 border-green-500 text-green-500 px-4 py-1 rounded-lg hover:bg-green-500 hover:text-white transition duration-300">
                                            <i class="fa-solid fa-share"></i>
                                        </button>
                                        <button
                                            @click="openInvoiceModal({{ $lead->id }}, '{{ $lead->name }}','{{ $lead->people_count }}','{{ $lead->child_count }}','{{ $lead->package->id ?? '' }}','{{ $lead->email }}')"
                                            class="border-2 border-green-500 text-green-500 px-4 py-1 rounded-lg hover:bg-green-500 hover:text-white transition">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </button>

                                        <button @click="openOtherModal({{ $lead->id }}, '{{ $lead->name }}')"
                                            class="border-2 border-green-500 text-green-500 px-4 py-1 rounded-lg hover:bg-green-500 hover:text-white transition duration-300">
                                            Other
                                        </button>
                                    </td>

                                    <!-- Status & Stage -->
                                    <td class="p-3 text-center">
                                        <span class="px-3 py-1 rounded-full text-white {{ $stageClass }}">
                                            {{ $lead->status ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-3 text-center">
                                        <a href="{{ route('leads.show', $lead->id) }}" class="btn-view m-1"><i
                                                class="fa-solid fa-eye"></i></a>
                                        <a href="{{ route('leads.assign.form', $lead->id) }}" class="btn-assign m-1"><i
                                                class="fa-solid fa-user-plus"></i></a>
                                        <form action="{{ route('leads.destroy', $lead->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this lead?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete m-1"><i
                                                    class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                            </tr>
                        @endforeach
                    </x-data-table>
                </div>

            </div>
        </div>
        <x-edit-lead />
        <x-followup-modal :packgaes="$packages" />
        <x-share-modal :packgaes="$packages" />
        <x-invoice-modal :packgaes="$packages" />
    </div>
    <script>
        function leadModals() {
            return {

                /* ---------------- STATE ---------------- */
                invoiceOpen: false,
                followOpen: false,
                shareOpen: false,
                editOpen: false,

                /* Lead Info */
                leadId: "",
                leadName: "",
                leadEmail: "",
                peopleCount: 1,
                childCount: 0,

                /* Package Data */
                packages: @json($packages),
                selectedPackageInvoice: "",
                packageData: null,
                selectedInvoiceItems: null,
                selectedRoomType: 'standard_price',
                filteredItems: [],

                /* Pricing */
                packagePrice: 0,
                itemPrice: 0,
                totalPrice: 0,
                discountedPrice: 0,
                finalPricePerAdult: 0,
                selectedDiscount: 0,
                travelStartDate: "",
                animatedPrice: 0,

                /* Cars */
                cars: [],
                selectedCar: "",

                /* Follow-up */
                phoneNumber: '',
                phoneCode: '',
                fullNumber: '',
                selectedReason: '',
                followups: [],
                reasons: [
                    'Call Back Later', 'Call Me Tomorrow', 'Payment Tomorrow',
                    'Talk With My Partner', 'Work with other company',
                    'Not Interested', 'Interested', 'Wrong Information',
                    'Not Pickup', 'Other'
                ],

                /* Share */
                shareLeadId: '',
                shareLeadName: '',
                selectedPackage: '',
                selectedPackageName: '',
                selectedPackageDocs: [],
                selectedPackagePdf: null,
                selectedDocs: [],
                showDropdown: false,
                showSelectedPackage: false,
                allPackages: @json($packages),

                /* Edit */
                editForm: {},

                /* Bulk */
                selected: [],
                bulkUser: '',


                /* ---------------- INIT HELPERS ---------------- */

                loadCars() {
                    fetch('/api/cars')
                        .then(res => res.json())
                        .then(data => this.cars = data.data || []);
                },


                /* ---------------- INVOICE MODAL ---------------- */

                openInvoiceModal(id, name, people = 1, child = 0, packageId = null, email = '') {
                    this.leadId = id;
                    this.leadName = name;
                    this.leadEmail = email;

                    this.peopleCount = Number(people) || 1;
                    this.childCount = Number(child) || 0;

                    this.selectedPackageInvoice = packageId || (this.packages[0]?.id ?? "");

                    this.invoiceOpen = true;

                    this.loadCars(); // Fetch cars on open
                    this.fetchFilteredItems(true);
                },

                fetchFilteredItems(auto = false) {
                    if (!this.selectedPackageInvoice) {
                        alert("Please select a package first.");
                        return;
                    }

                    const url =
                        `/package-items/filter?package_id=${this.selectedPackageInvoice}&adult_count=${this.peopleCount}&child_count=${this.childCount}&car_id=${this.selectedCar}`;

                    fetch(url)
                        .then(res => res.json())
                        .then(res => {
                            this.filteredItems = res.data;
                            this.packageData = {
                                packageItems: res.data
                            };

                            if (res.data.length > 0) {
                                const first = res.data[0];
                                this.selectedInvoiceItems = first.id;
                                this.updateInvoicePrice(first);
                            } else {
                                this.selectedInvoiceItems = null;
                                this.itemPrice = 0;
                                this.totalPrice = 0;
                                this.discountedPrice = 0;
                            }

                            if (auto) this.calculateDiscountedPrice();
                        });
                },

                updateInvoicePrice(item = null) {
                    if (!this.packageData || !this.selectedInvoiceItems) return;

                    if (!item) {
                        item = this.packageData.packageItems.find(i => i.id == this.selectedInvoiceItems);
                    }
                    if (!item) return;

                    const roomPrice = Number(item[this.selectedRoomType]) || 0;
                    const carPrice = item.car?.price?.per_day ? Number(item.car.price.per_day) : 0;

                    this.itemPrice = roomPrice + carPrice;

                    const oldTotal = this.totalPrice;
                    this.totalPrice = this.itemPrice;

                    this.animateNumber(oldTotal, this.totalPrice);
                    this.calculateDiscountedPrice();
                },

                calculateDiscountedPrice() {
                    const discount = parseFloat(this.selectedDiscount) || 0;
                    const base = this.totalPrice * (1 - discount / 100);

                    this.finalPricePerAdult = base;

                    const adults = base * this.peopleCount;
                    const children = (base / 2) * this.childCount;

                    this.discountedPrice = (adults + children).toFixed(2);
                },

                animateNumber(from, to, duration = 400) {
                    const start = performance.now();
                    const animate = (time) => {
                        const p = Math.min((time - start) / duration, 1);
                        this.animatedPrice = Math.floor(from + (to - from) * p);
                        if (p < 1) requestAnimationFrame(animate);
                    };
                    requestAnimationFrame(animate);
                },

                closeInvoice() {
                    this.invoiceOpen = false;
                    this.packageData = null;
                    this.selectedPackageInvoice = "";
                    this.selectedInvoiceItems = null;
                    this.travelStartDate = '';
                    this.selectedDiscount = 0;
                    this.totalPrice = 0;
                    this.discountedPrice = 0;
                    this.selectedRoomType = 'standard_price';
                },


                /* ---------------- SEND INVOICE ---------------- */

                sendInvoice() {
                    if (!this.selectedPackageInvoice) {
                        alert("Please select a package first!");
                        return;
                    }

                    alert("Invoice sent!");
                },


                /* ---------------- FOLLOW-UP MODAL ---------------- */

                openFollowModal(id, name) {
                    this.leadId = id;
                    this.leadName = name;
                    this.followOpen = true;

                    fetch(`/leads/${id}/details`)
                        .then(res => res.json())
                        .then(data => {
                            const phone = data.phone || {};
                            this.phoneNumber = phone.phone_number || '';
                            this.phoneCode = phone.phone_code || '';
                            this.fullNumber = phone.full_number || '';
                            this.followups = data.followups || [];
                        });
                },

                closeFollow() {
                    this.followOpen = false;
                },


                /* ---------------- SHARE MODAL ---------------- */

                handleShare(id, name, packageId = null, email = '') {
                    this.shareLeadId = id;
                    this.shareLeadName = name;
                    this.leadEmail = email;

                    this.selectedPackage = packageId || (this.allPackages[0]?.id ?? "");

                    this.showDropdown = true;
                    this.showSelectedPackage = true;

                    this.fetchPackageDocs(this.selectedPackage);
                    this.shareOpen = true;
                },

                fetchPackageDocs(packageId) {
                    fetch(`/packages/${packageId}/json`)
                        .then(res => res.json())
                        .then(data => {
                            let docs = data.package.package_docs_url;

                            if (typeof docs === 'string') docs = [docs];
                            if (!Array.isArray(docs)) docs = [];

                            this.selectedPackageDocs = docs;
                            this.selectedPackagePdf = docs[0] || null;
                            this.selectedDocs = [...docs];

                            this.selectedPackageName = data.package.package_name;
                        });
                },

                closeShare() {
                    this.shareOpen = false;
                    this.selectedPackageDocs = [];
                    this.selectedDocs = [];
                    this.selectedPackagePdf = null;
                },


                /* ---------------- EDIT MODAL ---------------- */

                openEditModal(id) {
                    fetch(`/leads/${id}/json`)
                        .then(res => res.json())
                        .then(data => {
                            this.editForm = {
                                ...data
                            };
                            this.editOpen = true;
                        });
                },

                closeEditModal() {
                    this.editOpen = false;
                },


                /* ---------------- BULK ASSIGN ---------------- */

                toggleLead(event) {
                    const id = parseInt(event.target.value);
                    if (event.target.checked) this.selected.push(id);
                    else this.selected = this.selected.filter(i => i !== id);
                },

                assignUser() {
                    if (!this.bulkUser) return alert('Select a user');
                    if (!this.selected.length) return alert('Select at least one lead');

                    fetch('{{ route('leads.bulkAssign') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                lead_ids: this.selected,
                                user_id: this.bulkUser
                            })
                        })
                        .then(res => res.json())
                        .then(resp => {
                            if (resp.success) window.location.reload();
                        });
                }

            };
        }
    </script>




</x-app-layout>
