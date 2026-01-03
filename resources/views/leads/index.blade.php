<x-app-layout>
    <div x-data="leadModals()" x-cloak class="min-h-screen ">

        <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
            <div class="w-full">

                <!-- Header -->
                <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-sm">
                            <i class="fa-solid fa-people-group text-xl text-gray-700 dark:text-gray-200"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Leads</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage leads, follow-ups, and
                                assignments</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Import template link --}}
                        <a href="/Example-Import-Leads.xlsx"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:shadow transition">
                            <i class="fa-regular fa-file-excel"></i>
                            Import Template
                        </a>

                        {{-- Import form --}}
                        <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data"
                            class="flex items-center gap-2">
                            @csrf
                            <label
                                class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm px-3 py-2 cursor-pointer hover:shadow transition">
                                <span class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                                    <i class="fa-solid fa-upload"></i>
                                    <span>Upload</span>
                                </span>
                                <input type="file" name="file" accept=".xlsx,.csv" required
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                            <button type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm hover:opacity-95 transition">
                                Import
                            </button>
                        </form>

                        {{-- Add lead --}}
                        <a href="{{ route('leads.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <i class="fa-solid fa-plus"></i>
                            Add Lead
                        </a>
                    </div>
                </header>

                <!-- Success -->
                @if (session('success'))
                    <div class="p-3 bg-green-600 text-white rounded shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Bulk Assign bar (visible when selections exist) -->


                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm overflow-x-auto">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mt-4">

                        @foreach ($statusOthersCounts as $status => $data)
                            <div
                                class="flex items-center gap-3 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border dark:border-gray-700">

                                <div
                                    class="w-10 h-10 flex items-center justify-center rounded-lg text-white {{ $data['color'] }}">
                                    <i class="fa-solid {{ $data['icon'] }}"></i>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                        {{ $status }}
                                    </span>
                                    <span class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $data['count'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg  p-4 shadow-sm overflow-x-auto">
                    <div class="flex flex-wrap gap-2 mb-4 items-center">
                        <input type="text" id="filter-id" placeholder="Search ID"
                            class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 text-sm w-40 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        <input type="text" id="filter-client" placeholder="Search Client"
                            class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 text-sm w-48 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        <input type="text" id="filter-location" placeholder="Search Location"
                            class="border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 text-sm w-48 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        <select id="filter-assigned"
                            class="w-44 rounded-lg border border-gray-300 dark:border-gray-700 px-4 py-2 text-sm dark:bg-gray-900 dark:text-white">
                            <option value="">All Assigned</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->name }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button data-value=""
                            class="status-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-300 transition">
                            All
                        </button>
                        @foreach ([
        'pending' => 'Pending',
        'followup_taken' => 'Follow-up Taken',
        'converted' => 'Converted',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ] as $value => $label)
                            <button data-value="{{ $value }}"
                                class="status-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-300 transition">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button data-value=""
                            class="status-btn-lead px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-300 transition">All</button>
                        @foreach (['Hot', 'Cold', 'Warm'] as $s)
                            <button data-value="{{ $s }}"
                                class="status-btn-lead px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-300 transition">{{ $s }}</button>
                        @endforeach
                    </div>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button data-value="all"
                            class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-300 transition">All
                            <span id="count-all" class="ml-2">0</span></button>
                        <button data-value="today"
                            class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-300 transition">Today
                            <span id="count-today" class="ml-2">0</span></button>
                        <button data-value="yesterday"
                            class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-300 transition">Yesterday
                            <span id="count-yesterday" class="ml-2">0</span></button>
                        <button data-value="week"
                            class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-300 transition">This
                            Week <span id="count-week" class="ml-2">0</span></button>
                        <button data-value="month"
                            class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-300 transition">This
                            Month <span id="count-month" class="ml-2">0</span></button>

                    </div>
                    <div id="bulkBar"
                        class="hidden mb-3 flex flex-wrap items-center gap-3 bg-white dark:bg-gray-800 p-4 rounded shadow-sm border">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Assign Selected Leads:</span>
                        <select id="bulkAssignUser"
                            class="w-48 rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                            <option value="">Select User</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button id="bulkAssignBtn" disabled
                            class="px-3 py-1 bg-gray-800 text-white rounded hover:bg-black transition text-sm disabled:opacity-50">
                            <i class="fa-solid fa-user-check mr-2"></i> Assign
                        </button>
                        <span id="selectedCount" class="text-sm text-gray-500 dark:text-gray-400">0 selected</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table id="Leads-table" class="min-w-full  border-gray-200 dark:border-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="w-5 p-2 text-left">
                                        <input type="checkbox" id="selectAll"
                                            class="rounded border-gray-300 focus:ring-2 focus:ring-blue-500">
                                    </th>
                                    <th class="w-64 p-2 text-left">Client Info</th>
                                    <th class="w-32 p-2 text-left">Location</th>
                                    <th class="w-32 p-2 text-left">Reminder</th>
                                    <th class="w-40 p-2 text-left">Inquiry</th>
                                    <th class="w-40 p-2 text-left">Proposal</th>
                                    <th class="w-32 p-2 text-left">Status</th>
                                    <th class="w-40 p-2 text-left">Assigned</th>
                                    <th class="w-24 p-2 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <x-edit-lead />
        <x-followup-modal :packages="$packages" />
        <x-share-modal :packages="$packages" />
        <x-invoice-modal :packages="$packages" />
        <x-payment-modal />
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ---------- state ----------
            const selectedIds = new Set(); // persists across pages
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            let selectedStatus = '';
            let selectedLeadStatus = '';
            let selectedDateRange = 'all';
            let datatable = null;
            let debounceTimer = null;

            // Elements
            const selectAllCheckbox = document.getElementById('selectAll');
            const bulkBar = document.getElementById('bulkBar');
            const bulkAssignBtn = document.getElementById('bulkAssignBtn');
            const bulkAssignUser = document.getElementById('bulkAssignUser');
            const selectedCountEl = document.getElementById('selectedCount');

            // Utility: debounce
            const debounce = (fn, wait = 300) => {
                return (...args) => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => fn(...args), wait);
                }
            };

            // Update UI of bulk bar based on selectedIds.size
            function refreshBulkUI() {
                const count = selectedIds.size;
                selectedCountEl.textContent = `${count} selected`;
                if (count > 0) {
                    bulkBar.classList.remove('hidden');
                    bulkAssignBtn.disabled = false;
                } else {
                    bulkBar.classList.add('hidden');
                    bulkAssignBtn.disabled = true;
                }
            }

            // Update header checkbox based on currently visible page checkboxes
            function syncSelectAllOnPage() {
                const pageCheckboxes = Array.from(document.querySelectorAll('.row-checkbox'));
                if (pageCheckboxes.length === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                    return;
                }
                const checkedOnPage = pageCheckboxes.filter(cb => cb.checked).length;
                selectAllCheckbox.checked = checkedOnPage === pageCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedOnPage > 0 && checkedOnPage < pageCheckboxes.length;
            }

            // Handler when a row checkbox toggles
            function onRowCheckboxChange(e) {
                const id = e.target.dataset.id;
                if (!id) return;
                if (e.target.checked) selectedIds.add(id);
                else selectedIds.delete(id);
                refreshBulkUI();
                syncSelectAllOnPage();
            }

            // When table is redrawn — rebind per-row checkbox listeners and set their checked state from selectedIds
            function bindRowCheckboxes() {
                // attach listeners
                document.querySelectorAll('.row-checkbox').forEach(cb => {
                    cb.removeEventListener('change', onRowCheckboxChange);
                    cb.addEventListener('change', onRowCheckboxChange);
                    // set checked if id in set
                    const id = cb.dataset.id;
                    cb.checked = selectedIds.has(id);
                });
                syncSelectAllOnPage();
            }

            // --------- DataTables init ----------
            datatable = $('#Leads-table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,

                ajax: {
                    url: "{{ route('leads.data') }}",
                    data: function(d) {
                        d.id = $('#filter-id').val();
                        d.client_name = $('#filter-client').val();
                        d.location = $('#filter-location').val();
                        d.status = selectedStatus;
                        d.lead_status = selectedLeadStatus;
                        d.assigned = $('#filter-assigned').val();
                        d.date_range = selectedDateRange;
                        d.search_query = $('.dataTables_filter input').val();
                    }
                },
                columns: [{
                        // render checkbox using ID
                        data: 'id',
                        orderable: true,
                        searchable: true,
                        className: 'p-2',
                        render: function(data, type, row, meta) {
                            return `<input type="checkbox" class="row-checkbox rounded border-gray-300 focus:ring-2 focus:ring-blue-500" data-id="${data}">`;
                        }
                    },

                    {
                        data: 'client_info',
                        orderable: true,
                        className: 'p-2'
                    },

                    {
                        data: 'location',
                        orderable: false,
                        className: 'p-2'
                    },
                    {
                        data: 'reminder',
                        orderable: false,
                        className: 'p-2'
                    },
                    {
                        data: 'inquiry',
                        orderable: false,
                        className: 'p-2'
                    },
                    {
                        data: 'proposal',
                        orderable: false,
                        className: 'p-2'
                    },
                    {
                        data: 'status',
                        orderable: false,
                        className: 'p-2'
                    },
                    {
                        data: 'assigned',
                        orderable: false,
                        className: 'p-2'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'p-2'
                    }
                ],
                dom: 'Blfrtip',
                buttons: [],
                pageLength: 50,
                order: [
                    [3, 'asc']
                ],
                autoWidth: false,
                drawCallback: function() {
                    // Called each time table is drawn (pagination, filter)
                    bindRowCheckboxes();
                    loadCounts(); // refresh counters
                }
            });

            // ---------- Filters with debounced redraw ----------
            const redraw = debounce(() => datatable.draw(), 300);
            $('#filter-id, #filter-client, #filter-location, #filter-assigned').on('keyup change', redraw);
            $('.dataTables_filter input').on('keyup', redraw);

            // ---------- Status / Date buttons ----------
            document.querySelectorAll('.status-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.status-btn').forEach(b => b.classList.remove(
                        'bg-blue-500', 'text-white'));
                    this.classList.add('bg-blue-500', 'text-white');
                    selectedStatus = this.dataset.value || '';
                    datatable.page(0).draw(false);
                });
            });

            document.querySelectorAll('.status-btn').forEach(btn => {
                btn.addEventListener('click', function() {

                    document.querySelectorAll('.status-btn')
                        .forEach(b => b.classList.remove('bg-blue-500', 'text-white'));

                    this.classList.add('bg-blue-500', 'text-white');

                    const value = this.dataset.value || '';

                    // ✅ FOLLOW-UP TAKEN → lead_status
                    if (value === 'followup_taken') {
                        selectedLeadStatus = 'followup_taken';
                        selectedStatus = ''; // 🔥 clear normal status
                        selectedDateRange = 'today';

                        document.querySelectorAll('.date-range-btn')
                            .forEach(b => b.classList.remove('bg-blue-500', 'text-white'));

                        document.querySelector('.date-range-btn[data-value="today"]')
                            ?.classList.add('bg-blue-500', 'text-white');

                    } else {
                        selectedStatus = value;
                        selectedLeadStatus = '';
                    }

                    datatable.page(0).draw(false);
                });
            });



            document.querySelectorAll('.date-range-btn').forEach(btn => {
                btn.addEventListener('click', function() {



                    document.querySelectorAll('.date-range-btn')
                        .forEach(b => b.classList.remove('bg-blue-500', 'text-white'));

                    this.classList.add('bg-blue-500', 'text-white');
                    selectedDateRange = this.dataset.value || 'all';

                    datatable.page(0).draw(false);
                });
            });


            // ---------- Select All on current page ----------
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = Array.from(document.querySelectorAll('.row-checkbox'));
                checkboxes.forEach(cb => {
                    cb.checked = selectAllCheckbox.checked;
                    const id = cb.dataset.id;
                    if (cb.checked) selectedIds.add(id);
                    else selectedIds.delete(id);
                });
                refreshBulkUI();
            });

            // ---------- Bulk assign action ----------
            bulkAssignBtn.addEventListener('click', async function() {

                const userId = bulkAssignUser.value;

                // Validate user
                if (!userId) {
                    toast('Please select a user to assign.');
                    return;
                }

                // Validate selected leads
                if (selectedIds.size === 0) {
                    toast('No leads selected.');
                    return;
                }

                const leadIds = Array.from(selectedIds);

                // Disable button while processing
                bulkAssignBtn.disabled = true;
                bulkAssignBtn.textContent = 'Assigning...';

                try {
                    const response = await fetch("{{ route('leads.bulkAssign') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            lead_ids: leadIds, // FIXED → must match your controller
                            user_id: userId
                        })
                    });

                    const json = await response.json();

                    if (json.success) {
                        // Clear selected items
                        selectedIds.clear();
                        refreshBulkUI();

                        // Refresh table
                        datatable.draw(false);

                        // Show success toast
                        toast(json.message || "Leads assigned successfully.", 'success');
                    } else {
                        // Show error toast
                        toast(json.message || "Failed to assign leads.", 'error');
                    }


                } catch (error) {
                    console.error(error);
                    toast('An unexpected error occurred while assigning leads.', 'error');
                }

                // Re-enable button
                bulkAssignBtn.disabled = false;
                bulkAssignBtn.innerHTML = '<i class="fa-solid fa-user-check mr-2"></i> Assign';

            });


            // ---------- Keep counts updated ----------
            function loadCounts() {
                const params = new URLSearchParams({
                    id: $('#filter-id').val() || '',
                    client_name: $('#filter-client').val() || '',
                    location: $('#filter-location').val() || '',
                    assigned: $('#filter-assigned').val() || '',
                    status: selectedStatus || '',
                    lead_status: selectedLeadStatus || '', // <-- FIXED
                    date_range: selectedDateRange || ''
                });

                fetch("{{ route('leads.counts') }}?" + params.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('count-today').textContent = data.today ?? 0;
                        document.getElementById('count-yesterday').textContent = data.yesterday ?? 0;
                        document.getElementById('count-week').textContent = data.week ?? 0;
                        document.getElementById('count-month').textContent = data.month ?? 0;
                        document.getElementById('count-all').textContent = data.all ?? 0;
                    })
                    .catch(e => console.error('Counts error', e));
            }


            // initial load
            refreshBulkUI();
            loadCounts();

        }); // DOMContentLoaded
    </script>
    <script>
        function leadModals() {
            return {

                /* ======================================================
                   MODAL STATES
                ====================================================== */
                invoiceOpen: false,
                followOpen: false,
                shareOpen: false,
                editOpen: false,
                paymentOpen: false,
                sending: false,

                /* ======================================================
                   LEAD INFO
                ====================================================== */
                leadId: "",
                leadName: "",
                leadEmail: "",
                leadPhone: "",
                peopleCount: 1,
                childCount: 0,

                /* ======================================================
                   WHATSAPP
                ====================================================== */
                whatsappMessage: "",
                whatsappPdfUrl: "",

                /* ======================================================
                   PACKAGES
                ====================================================== */
                packages: @json($packages),
                allPackages: @json($packages),

                selectedPackageInvoice: "",
                selectedPackage: "",
                selectedPackageName: "",
                selectedPackageDocs: [],
                selectedPackagePdf: null,

                packageData: null,
                selectedInvoiceItems: null,
                selectedRoomType: "standard_price",
                filteredItems: [],

                /* ======================================================
                   PRICING
                ====================================================== */
                packagePrice: 0,
                itemPrice: 0,
                totalPrice: 0,
                discountedPrice: 0,
                finalPricePerAdult: 0,
                selectedDiscount: 0,
                travelStartDate: "",
                animatedPrice: 0,

                /* ======================================================
                   CARS
                ====================================================== */
                cars: [],
                selectedCar: "",

                /* ======================================================
                   FOLLOW-UP
                ====================================================== */
                phoneNumber: "",
                phoneCode: "",
                fullNumber: "",
                selectedReason: "",
                followups: [],
                reasons: [],

                /* ======================================================
                   SHARE
                ====================================================== */
                shareLeadId: "",
                shareLeadName: "",
                selectedDocs: [],
                showDropdown: false,
                showSelectedPackage: false,

                /* ======================================================
                   EDIT
                ====================================================== */
                editForm: {},

                /* ======================================================
                   BULK
                ====================================================== */
                selected: [],
                bulkUser: "",

                /* ======================================================
                   PAYMENT
                ====================================================== */
                paymentInvoiceId: null,
                paymentInvoiceNumber: "",
                amount: 0,
                remainingAmount: 0,

                paidAmount: 0,
                paymentMethodId: "",
                selectedMethod: null,
                paymentMethods: [],

                transactionId: "",
                nextPaymentDate: "",
                paymentNotes: "",
                paymentImage: null,

                partialPaymentWithoutNextDate: false,
                nextDateError: false,
                paymentMethods: [],
                paymentMethodId: '',


             
               /* ======================================================
   COMPUTED
====================================================== */

get isPartial() {
    return this.paidAmount > 0 && this.paidAmount < this.remainingAmount;
},

get remainingAmountReactive() {
    return Math.max(
        Number(this.remainingAmount) - Number(this.paidAmount || 0),
        0
    );
},

get hasBankDetails() {
    if (!this.selectedMethod) return false;

    return Boolean(
        this.selectedMethod.bank_name ||
        this.selectedMethod.account_name ||
        this.selectedMethod.account_number ||
        this.selectedMethod.ifsc_code
    );
},




                


                get partialPaymentWithoutNextDate() {
                    return this.isPartial && !this.nextPaymentDate;
                },

                /* ======================================================
                   HELPERS
                ====================================================== */
                formatCurrency(value) {
                    return Number(value || 0).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                resetPaymentForm() {
                    this.paidAmount = 0;
                    this.paymentMethodId = '';
                    this.selectedMethod = null;
                    this.transactionId = '';
                    this.nextPaymentDate = '';
                    this.paymentNotes = '';
                    this.paymentImage = null;
                    this.nextDateError = false;
                },

                handleImageUpload(e) {
                    this.paymentImage = e.target.files?.[0] || null;
                },

                /* ======================================================
                   PAYMENT MODAL
                ====================================================== */
                openPaymentModal(invoice) {
                    this.paymentInvoiceId = invoice.id;
                    this.paymentInvoiceNumber = invoice.invoice_no;
                    this.amount = Number(invoice.final_price);
                    this.remainingAmount = Number(invoice.remaining_amount);

                    this.resetPaymentForm();
                    this.fetchPaymentMethods();
                    this.paymentOpen = true;
                },

                closePaymentModal() {
                    this.paymentOpen = false;
                },
init() {
    this.$watch('paymentMethodId', id => {
        this.selectedMethod =
            this.paymentMethods.find(m => m.id == id) || null;
    });
},
                fetchPaymentMethods() {
                    fetch('/payment-methods/active')
                        .then(res => res.json())
                        .then(data => this.paymentMethods = data || []);
                },

                submitPayment() {
                    if (this.isPartial && !this.nextPaymentDate) {
                        this.nextDateError = true;
                        return;
                    }

                    if (this.selectedMethod?.image_proof_required && !this.paymentImage) {
                        alert('Payment proof image is required');
                        return;
                    }

                    const formData = new FormData();
                    [
                        ['invoice_id', this.paymentInvoiceId],
                        ['paid_amount', this.paidAmount],
                        ['payment_method_id', this.paymentMethodId],
                        ['transaction_id', this.transactionId],
                        ['notes', this.paymentNotes],
                    ].forEach(([k, v]) => formData.append(k, v ?? ''));

                    if (this.isPartial) {
                        formData.append('next_payment_date', this.nextPaymentDate);
                    }
                    if (this.paymentImage) {
                        formData.append('image', this.paymentImage);
                    }

                    fetch('/payments', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: formData
                        })
                        .then(res => res.json())
                        .then(() => {
                            this.closePaymentModal();
                            window.location.reload();
                        })
                        .catch(() => alert('Payment failed'));
                },

                /* ======================================================
                   INVOICE LOGIC
                ====================================================== */
                openInvoiceModal(id, name, people = 1, child = 0, packageId = null, email = '') {
                    Object.assign(this, {
                        leadId: id,
                        leadName: name,
                        leadEmail: email,
                        peopleCount: Number(people) || 1,
                        childCount: Number(child) || 0,
                        selectedPackageInvoice: packageId || this.packages?.[0]?.id || ""
                    });

                    this.invoiceOpen = true;
                    this.loadCars();

                    if (this.selectedPackageInvoice) {
                        this.fetchPackageDetails(this.selectedPackageInvoice);
                    }
                },

                fetchPackageDetails(packageId) {
                    if (!packageId) return;

                    fetch(`/packages/${packageId}/json`)
                        .then(res => res.json())
                        .then(({
                            success,
                            package
                        }) => {
                            if (!success) return;

                            this.packageData = package;
                            const firstItem = package.packageItems?.[0] || null;

                            this.selectedInvoiceItems = firstItem?.id || null;
                            this.updateInvoicePrice(firstItem);
                            this.calculateDiscountedPrice();
                        });
                },

                updateInvoicePrice(item) {
                    if (!item) return;

                    const price = Number(item[this.selectedRoomType]) || 0;
                    const oldTotal = this.totalPrice;

                    this.itemPrice = price;
                    this.totalPrice = price;

                    this.animateNumber(oldTotal, price);
                    this.calculateDiscountedPrice();
                },

                calculateDiscountedPrice() {
                    const discount = Number(this.selectedDiscount) || 0;
                    const base = this.totalPrice * (1 - discount / 100);

                    this.finalPricePerAdult = base;

                    const adults = base * this.peopleCount;
                    const children = (base / 2) * this.childCount;

                    this.discountedPrice = (adults + children).toFixed(2);
                },

                animateNumber(from, to, duration = 400) {
                    const start = performance.now();
                    const animate = (now) => {
                        const progress = Math.min((now - start) / duration, 1);
                        this.animatedPrice = Math.floor(from + (to - from) * progress);
                        if (progress < 1) requestAnimationFrame(animate);
                    };
                    requestAnimationFrame(animate);
                },

                /* ======================================================
                   CARS
                ====================================================== */
                loadCars() {
                    fetch('/api/cars')
                        .then(res => res.json())
                        .then(res => this.cars = res.data || []);
                },

                /* ======================================================
                   BULK ASSIGN
                ====================================================== */
                toggleLead(e) {
                    const id = Number(e.target.value);
                    e.target.checked ?
                        this.selected.push(id) :
                        this.selected = this.selected.filter(i => i !== id);
                },

                assignUser() {
                    if (!this.bulkUser || !this.selected.length) {
                        alert('Select user and at least one lead');
                        return;
                    }

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
                        .then(res => res.success && window.location.reload());
                }
            };
        }
    </script>

</x-app-layout>
