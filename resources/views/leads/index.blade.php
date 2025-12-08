<x-app-layout>

    <div x-data="leadModals()" x-cloak class="min-h-screen bg-gradient-to-br from-gray-100 via-white to-gray-200">
        <div class="ml-64 flex justify-center items-start min-h-screen p-6">
            <div class="w-full max-w-7xl space-y-6">

                <!-- Header -->
                <div
                    class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 p-4 rounded-lg border bg-white">
                    <h2 class="text-2xl font-semibold flex items-center gap-2 text-gray-800">
                        <i class="fa-solid fa-people-group text-gray-700"></i>
                        Leads
                    </h2>

                    <div class="flex flex-col md:flex-row items-center gap-3">

                        <a href="/Example-Import-Leads.xlsx"
                            class="px-3 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-100 transition">
                            Import Template
                        </a>

                        <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data"
                            class="flex items-center gap-2">
                            @csrf
                            <input type="file" name="file" accept=".xlsx,.csv" required
                                class="text-sm file:bg-gray-200 file:border-0 file:rounded file:px-3 file:py-1 file:text-gray-700 cursor-pointer" />

                            <button type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-black transition">
                                Import
                            </button>
                        </form>

                        <a href="{{ route('leads.create') }}"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-100 transition">
                            + Add Lead
                        </a>
                    </div>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="p-3 bg-green-600 text-white rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Bulk Assign -->
                <div x-show="selected.length > 0" class="mb-3 flex items-center gap-3">
                    <span class="font-medium text-gray-700">Assign Selected Leads:</span>

                    <select x-model="bulkUser" class="w-1/6 rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">
                        <option value="">Select User</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>

                    <button @click="assignUser()"
                        class="px-3 py-1 bg-gray-800 text-white rounded hover:bg-black transition">
                        Assign
                    </button>

                    <span class="text-sm text-gray-500" x-text="selected.length + ' selected'"></span>
                </div>

                <!-- Table -->
                <form method="GET" x-data class="mb-4 bg-white p-4 rounded-lg shadow">

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">

                        <!-- Country -->
                        <select name="country" x-on:change="$el.form.submit()"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">
                            <option value="">Country</option>
                            @foreach ($leads->pluck('country')->unique() as $country)
                                <option value="{{ $country }}"
                                    {{ request('country') == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>

                        <!-- District -->
                        <select name="district" x-on:change="$el.form.submit()"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">
                            <option value="">District</option>
                            @foreach ($leads->pluck('district')->unique() as $district)
                                <option value="{{ $district }}"
                                    {{ request('district') == $district ? 'selected' : '' }}>
                                    {{ $district }}
                                </option>
                            @endforeach
                        </select>

                        <!-- City -->
                        <select name="city" x-on:change="$el.form.submit()"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">
                            <option value="">City</option>
                            @foreach ($leads->pluck('city')->unique() as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Lead Status -->
                        <select name="lead_status" x-on:change="$el.form.submit()"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">
                            <option value="">Lead Status</option>
                            <option value="Hot" {{ request('lead_status') == 'Hot' ? 'selected' : '' }}>Hot</option>
                            <option value="Warm" {{ request('lead_status') == 'Warm' ? 'selected' : '' }}>Warm
                            </option>
                            <option value="Cold" {{ request('lead_status') == 'Cold' ? 'selected' : '' }}>Cold
                            </option>
                        </select>

                        <!-- Stage -->
                        <select name="status" x-on:change="$el.form.submit()"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">
                            <option value="">Stage</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="Quotation Sent"
                                {{ request('status') == 'Quotation Sent' ? 'selected' : '' }}>Quotation Sent</option>
                            <option value="Follow-up Taken"
                                {{ request('status') == 'Follow-up Taken' ? 'selected' : '' }}>Follow-up Taken</option>

                            <option value="Lost" {{ request('status') == 'Lost' ? 'selected' : '' }}>Lost</option>
                            <option value="Converted" {{ request('status') == 'Converted' ? 'selected' : '' }}>
                                Converted</option>
                            <option value="On Hold" {{ request('status') == 'On Hold' ? 'selected' : '' }}>On Hold
                            </option>
                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>


                        <!-- Package -->
                        <select name="package_id" x-on:change="$el.form.submit()"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">
                            <option value="">Package</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}"
                                    {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->package_name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Created By -->
                        <select name="user_id" x-on:change="$el.form.submit()"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">
                            <option value="">Created By</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}"
                                    {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Assigned To -->
                        <select name="assigned_to" x-on:change="$el.form.submit()"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">
                            <option value="">Assigned To</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}"
                                    {{ request('assigned_to') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Reset button -->
                        <a href="{{ route('leads.index') }}"
                            class="w-full px-4 py-2 bg-gray-500 text-white rounded text-sm flex items-center justify-center">
                            Reset
                        </a>

                    </div>
                </form>


                <div class="flex flex-wrap gap-2 mb-4">
                    @php
                        $statuses = [
                            '' => 'All',
                            'Follow-up Taken' => 'Follow-up Taken',
                            'Converted' => 'Converted',
                            'Approved' => 'Approved',
                            'Rejected' => 'Rejected',
                        ];
                    @endphp

                    @foreach ($statuses as $value => $label)
                        <form method="GET" class="inline">
                            {{-- Keep all GET params except status --}}
                            @foreach (request()->except('status') as $name => $val)
                                <input type="hidden" name="{{ $name }}" value="{{ $val }}">
                            @endforeach

                            {{-- Only add status when not All --}}
                            @if ($value !== '')
                                <input type="hidden" name="status" value="{{ $value }}">
                            @endif

                            <button type="submit"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-sm
                {{ ($filters['status'] ?? '') == $value ? 'bg-blue-500 text-white' : 'bg-white' }}">
                                {{ $label }} ({{ $statusCounts[$value ?: 'All'] ?? 0 }})
                            </button>
                        </form>
                    @endforeach
                </div>
                @if (!empty($filters['status']))
                    <div class="flex flex-wrap gap-2 mb-4">
                        @php
                            $times = [
                                'all' => 'All',
                                'today' => 'Today',
                                'week' => 'This Week',
                                'month' => 'This Month',
                            ];
                        @endphp

                        @foreach ($times as $key => $label)
                            <form method="GET" class="inline">

                                {{-- Keep all GET params except time --}}
                                @foreach (request()->except('time') as $name => $value)
                                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                                @endforeach

                                <input type="hidden" name="time" value="{{ $key }}">

                                <button type="submit"
                                    class="px-4 py-2 rounded-lg border text-sm
                    {{ ($filters['time'] ?? 'all') == $key ? 'bg-blue-500 text-white' : 'bg-white' }}">
                                    {{ $label }} ({{ $timeCounts[$key] ?? 0 }})
                                </button>
                            </form>
                        @endforeach
                    </div>
                @endif







                <div class="bg-white rounded-lg border p-4 overflow-x-auto">

                    <x-data-table id="Leads-table" :headers="[
                        '#',
                        'ID',
                        'Client Info',
                        'Location',
                        'Reminder',
                        'Inquiry',
                        'Proposal',
                        'Status',
                        'Assigned',
                        'Action',
                    ]" :excel="true" :print="true"
                        title="Leads" resourceName="Leads">

                        @foreach ($leads as $lead)
                            @php
                                $maskedPhone =
                                    str_repeat('*', strlen($lead->phone_number) - 4) . substr($lead->phone_number, -4);
                                $stageClass =
                                    [
                                        'Pending' => 'bg-blue-400 text-white',
                                        'Approved' => 'bg-green-500 text-white',
                                        'Quotation Sent' => 'bg-indigo-500 text-white',
                                        'Follow-up Taken' => 'bg-purple-500 text-white',
                                        'Converted' => 'bg-teal-500 text-white',
                                        'Lost' => 'bg-gray-500 text-white',
                                        'On Hold' => 'bg-orange-400 text-white',
                                        'Rejected' => 'bg-red-600 text-white',
                                    ][$lead->status] ?? 'bg-gray-300 text-white';
                                $statusClass =
                                    [
                                        'Hot' => 'bg-red-500',
                                        'Warm' => 'bg-yellow-400',
                                        'Cold' => 'bg-gray-400',
                                        'Interested' => 'bg-green-500',
                                    ][$lead->lead_status] ?? 'bg-gray-300';
                            @endphp

                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 text-center">
                                    <input type="checkbox" value="{{ $lead->id }}" @change="toggleLead($event)"
                                        class="h-4 w-4 text-gray-700 border-gray-400">
                                </td>

                                <td class="p-3 text-center text-gray-700">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="p-3 text-gray-800">
                                    <div class="font-medium flex items-center gap-2">
                                        {{ $lead->name }}

                                        <!-- Simple Label -->
                                        <span
                                            class="px-2 py-0.5 rounded text-white font-extrabold {{ $statusClass }}">
                                            {{ $lead->lead_status ?? 'N/A' }}
                                        </span>

                                        <button @click="openEditModal({{ $lead->id }})"
                                            class="text-gray-600 hover:text-black">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </div>

                                    <a href="mailto:{{ $lead->email }}"
                                        class="text-gray-700 hover:underline text-sm">
                                        {{ $lead->email }}
                                    </a>

                                    <div class="text-gray-600 text-sm font-mono">
                                        +{{ $lead->phone_code }} {{ $maskedPhone }}
                                    </div>

                                    <div class="text-gray-500 text-xs">
                                        {{ $lead->created_at->format('d-M-y') }}
                                    </div>
                                </td>

                                <td class="p-3 text-center text-gray-700">
                                    {{ $lead->country }} <br>
                                    {{ $lead->district }} <br>
                                    {{ $lead->city }}
                                </td>

                                <td class="p-3 text-center">
                                    <button @click="openFollowModal({{ $lead->id }}, '{{ $lead->name }}')"
                                        class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm">
                                        Followup
                                    </button>

                                    @if ($lead->lastFollowup)
                                        <div class="text-xs text-gray-600 mt-2">
                                            <strong>Last:</strong> {{ $lead->lastFollowup->reason }}<br>
                                            <strong>By:</strong> {{ $lead->lastFollowup->user->name ?? 'N/A' }}
                                        </div>
                                    @endif
                                </td>

                                <td class="p-3 text-center text-gray-700">
                                    {{ $lead->package->package_name ?? Str::limit($lead->inquiry_text, 20) }}
                                </td>

                                <td class="p-3 text-center">
                                    <button
                                        @click="handleShare({{ $lead->id }}, '{{ $lead->name }}',
                                '{{ $lead->package->id ?? '' }}','{{ $lead->email }}')"
                                        class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm">
                                        <i class="fa-solid fa-share"></i>
                                    </button>

                                    <button
                                        @click="openInvoiceModal({{ $lead->id }}, '{{ $lead->name }}',
                                '{{ $lead->people_count }}','{{ $lead->child_count }}',
                                '{{ $lead->package->id ?? '' }}','{{ $lead->email }}')"
                                        class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm ml-1">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </button>
                                </td>

                                <td class="p-3 text-center">
                                    <div x-data="{ open: false, value: '{{ $lead->status }}' }" class="relative">

                                        <!-- Display Status (Shown Initially) -->
                                        <div x-show="!open" @click="open = true"
                                            class="cursor-pointer text-xs px-2 py-1 rounded {{ $stageClass }}">
                                            <span x-text="value || 'Select Status'"></span>
                                        </div>

                                        <!-- Dropdown (Hidden Until Click) -->
                                        <select x-show="open" x-cloak
                                            @change="
                                                        value = $event.target.value;
                                                        open = false;
                                                        updateStatus({{ $lead->id }}, value);"
                                            @click.outside="open = false"
                                            class="px-2 py-1 rounded text-xs border bg-white dark:bg-gray-800">
                                            <option value="">Select Status</option>
                                            <option value="Pending"
                                                {{ $lead->status == 'Pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="Approved"
                                                {{ $lead->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="Quotation Sent"
                                                {{ $lead->status == 'Quotation Sent' ? 'selected' : '' }}>Quotation
                                                Sent</option>
                                            <option value="Follow-up Taken"
                                                {{ $lead->status == 'Follow-up Taken' ? 'selected' : '' }}>Follow-up
                                                Taken</option>

                                            <option value="Lost" {{ $lead->status == 'Lost' ? 'selected' : '' }}>
                                                Lost
                                            </option>
                                            <option value="Converted"
                                                {{ $lead->status == 'Converted' ? 'selected' : '' }}>Converted</option>
                                            <option value="On Hold"
                                                {{ $lead->status == 'On Hold' ? 'selected' : '' }}>
                                                On Hold</option>
                                            <option value="Rejected"
                                                {{ $lead->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>

                                    </div>
                                </td>



                                <td class="p-3 text-center text-xs text-gray-700 space-y-1">
                                    <div>
                                        <strong>Assigned:</strong>
                                        {{ $lead->latestAssignedUser->user->name ?? 'N/A' }}
                                    </div>
                                    <div>
                                        <strong>By:</strong>
                                        {{ $lead->latestAssignedUser->assignedBy->name ?? 'N/A' }}
                                    </div>
                                    <div>
                                        <strong>Created:</strong>
                                        {{ $lead->createdBy->name ?? 'System' }}
                                    </div>
                                </td>

                                <td class="p-3 text-center">
                                    <a href="{{ route('leads.show', $lead->id) }}"
                                        class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm ml-1">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <a href="{{ route('leads.assign.form', $lead->id) }}"
                                        class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm ml-1">
                                        <i class="fa-solid fa-user-plus"></i>
                                    </a>

                                    <form action="{{ route('leads.destroy', $lead->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this lead?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm m-1 ">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </x-data-table>
                </div>

            </div>
        </div>


        {{-- Modals --}}
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
                openInvoiceModal(id, name, people = 1, child = 0, packageId = null, email = '') {
                    this.leadId = id;
                    this.leadName = name;
                    this.leadEmail = email;

                    this.peopleCount = Number(people) || 1;
                    this.childCount = Number(child) || 0;

                    this.selectedPackageInvoice = packageId || (this.packages[0]?.id ?? "");
                    this.invoiceOpen = true;

                    this.loadCars();

                    // Fetch package details only if a package is preselected
                    if (this.selectedPackageInvoice) {
                        this.fetchPackageDetails(this.selectedPackageInvoice);
                    } else {
                        this.packageData = null;
                    }
                },

                fetchPackageDetails(packageId) {
                    if (!packageId) return;

                    fetch(`/packages/${packageId}/json`)
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                this.packageData = res.package;

                                // Preselect first package item
                                if (this.packageData.packageItems?.length > 0) {
                                    this.selectedInvoiceItems = this.packageData.packageItems[0].id;
                                    this.updateInvoicePrice(this.packageData.packageItems[0]);
                                } else {
                                    this.selectedInvoiceItems = null;
                                    this.itemPrice = this.totalPrice = this.discountedPrice = 0;
                                }

                                this.calculateDiscountedPrice();
                            }
                        });
                },

                fetchFilteredItems() {
                    if (!this.selectedPackageInvoice) return;

                    const url =
                        `/package-items/filter?package_id=${this.selectedPackageInvoice}&adult_count=${this.peopleCount}&child_count=${this.childCount}&car_id=${this.selectedCar}`;

                    fetch(url)
                        .then(res => res.json())
                        .then(res => {
                            this.filteredItems = res.data;
                            this.packageData.packageItems = res.data;

                            if (res.data.length > 0) {
                                const firstItem = res.data[0];
                                this.selectedInvoiceItems = firstItem.id;
                                this.updateInvoicePrice(firstItem);
                            } else {
                                this.selectedInvoiceItems = null;
                                this.itemPrice = this.totalPrice = this.discountedPrice = 0;
                            }

                            this.calculateDiscountedPrice();
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

                    this.itemPrice = roomPrice;
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
                    this.selectedCar = "";
                },

                createQuickInvoice() {
                    if (!this.selectedPackageInvoice) return alert("Please select a package first!");

                    const payload = {
                        lead_id: this.leadId,
                        package_id: this.selectedPackageInvoice,
                        package_items_id: this.selectedInvoiceItems,
                        package_type: this.selectedRoomType,
                        adult_count: this.peopleCount,
                        child_count: this.childCount,
                        discount_amount: this.selectedDiscount,
                        price_per_person: this.discountedPrice,
                        travel_start_date: this.travelStartDate
                    };

                    fetch('/invoices/create-quick', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.data?.id) {
                                window.location.href = '{{ route('invoices.create') }}?invoice_id=' + data.data.id;
                            } else {
                                alert('Failed to create invoice.');
                            }
                        })
                        .catch(err => console.error(err));
                },

                loadCars() {
                    fetch('/api/cars')
                        .then(res => res.json())
                        .then(data => this.cars = data.data || []);
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

                    // Loader optional
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
                async submitEdit() {
                    try {
                        if (!this.editForm.id) {
                            alert("Lead ID missing—cannot update.");
                            return;
                        }

                        const response = await fetch(`/leads/${this.editForm.id}`, {
                            method: "PATCH",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute("content"),
                            },
                            body: JSON.stringify(this.editForm),
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            alert(result.message || "Validation failed");
                            return;
                        }

                        alert("Lead Updated Successfully");
                        this.closeEditModal();
                        window.location.reload();

                    } catch (error) {
                        console.error(error);
                        alert("Something went wrong while updating lead");
                    }
                },
                async updateStatus(id, newStatus) {
                    try {
                        const response = await fetch(`/leads/${id}/status`, {
                            method: "PATCH",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute("content"),
                            },
                            body: JSON.stringify({
                                status: newStatus
                            }),
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            alert(result.message || "Failed to update status");
                            return;
                        }
                        window.location.reload();
                        console.log("Status updated:", result.status);

                    } catch (error) {
                        console.error(error);
                        alert("Error while updating status");
                    }
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
