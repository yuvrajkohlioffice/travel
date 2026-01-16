<x-app-layout>
    {{-- Main Container --}}
    <div x-data="leadModals()" x-cloak class="min-h-screen ">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 p-6 ml-64">

            {{-- 1. HEADER SECTION --}}
            <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <i class="fa-solid fa-people-group text-xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Leads Management</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Track, filter, and assign your leads
                            efficiently.</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    {{-- Import Actions --}}
                    <div class="flex items-center gap-2">
                        <a href="/Example-Import-Leads.xlsx"
                            class="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition shadow-sm"
                            title="Download Template">
                            <i class="fa-regular fa-file-excel mr-1"></i> Template
                        </a>

                        <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data"
                            class="flex items-center">
                            @csrf
                            <label
                                class="cursor-pointer px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-l-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 border-r-0 transition shadow-sm">
                                <i class="fa-solid fa-upload mr-1"></i> Choose
                                <input type="file" name="file" accept=".xlsx,.csv" class="hidden"
                                    onchange="this.form.submit()">
                            </label>
                            <button
                                class="px-3 py-2 bg-gray-800 dark:bg-gray-700 text-white text-sm rounded-r-lg hover:bg-black transition shadow-sm">
                                Import
                            </button>
                        </form>
                    </div>

                    {{-- Add Lead --}}
                    <a href="{{ route('leads.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-md transition transform hover:scale-105">
                        <i class="fa-solid fa-plus"></i> Add Lead
                    </a>
                </div>
            </header>

            {{-- 2. KPI CARDS (Status Counts) --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 gap-4 mb-6">
                @foreach ($statusOthersCounts as $status => $data)
                    <div data-value="{{ $status }}"
                               
                        class=" status-btn bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center gap-3 transition hover:shadow-md" style="background: white !important">
                        <div
                            class="w-10 h-10 flex items-center justify-center rounded-full text-white shadow-sm {{ $data['color'] }}">
                            <i class="fa-solid {{ $data['icon'] }} text-sm"></i>
                        </div>
                        <div >
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold tracking-wider">
                                {{ $status }}</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $data['count'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 3. FILTERS & TABLE CONTAINER --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">

                {{-- Search Inputs --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" id="filter-id" placeholder="ID"
                            class="w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" id="filter-client" placeholder="Client Name, Email, Phone"
                            class="w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" id="filter-location" placeholder="City, District, Country"
                            class="w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-user-tag absolute left-3 top-3 text-gray-400"></i>
                        <select id="filter-assigned"
                            class="w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">All Assigned Users</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->name }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-4 mb-6">

                    {{-- Dynamic Status Tabs --}}
                    <div class="flex flex-wrap gap-2 mb-4">
                        {{-- "All Status" Button (Default Active) --}}
                        <button data-value=""
                            class="status-btn px-5 py-2 rounded-full text-sm font-semibold border shadow-md transition-all duration-200 
               bg-blue-600 text-white border-blue-600 hover:shadow-lg transform active:scale-95">
                            All Status
                        </button>

                        {{-- Dynamic Status Buttons (Default Inactive) --}}
                        @foreach ($leadStatuses as $status)
                            <button data-value="{{ $status->name }}"
                                class="status-btn px-5 py-2 rounded-full text-sm font-medium border transition-all duration-200 
                   bg-white text-gray-600 border-gray-300 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transform active:scale-95">
                                {{ $status->name }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Categories & Date Range --}}
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        {{-- Categories --}}
                        <div class="flex flex-wrap gap-2">
                            <button data-value=""
                                class="category-btn px-3 py-1.5 rounded-lg border text-xs font-semibold uppercase tracking-wide transition bg-gray-800 text-white border-gray-800">
                                All Types
                            </button>
                            @foreach (['Hot', 'Warm', 'Cold'] as $cat)
                                <button data-value="{{ $cat }}"
                                    class="category-btn px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-600 text-xs font-semibold uppercase tracking-wide hover:bg-gray-50 transition">
                                    {{ $cat }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Date Ranges with Live Counts --}}
                        <div class="flex flex-wrap gap-1 bg-gray-100 dark:bg-gray-700 p-1 rounded-lg">
                            @foreach (['all' => 'All Time', 'today' => 'Today', 'yesterday' => 'Yesterday', 'week' => 'This Week', 'month' => 'This Month'] as $key => $label)
                                <button data-value="{{ $key }}"
                                    class="date-range-btn flex items-center gap-2 px-3 py-1.5 rounded-md text-sm font-medium transition {{ $loop->first ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                    {{ $label }}
                                    <span id="count-{{ $key }}"
                                        class="bg-gray-200 text-gray-700 text-xs px-1.5 py-0.5 rounded-full">0</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Bulk Actions Bar --}}
                <div id="bulkBar"
                    class="hidden animate-fade-in-down mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 p-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">BULK ACTION</span>
                        <span id="selectedCount" class="text-sm text-gray-700 dark:text-gray-300 font-medium">0 leads
                            selected</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <select id="bulkAssignUser"
                            class="text-sm rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600">
                            <option value="">Select User to Assign...</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button id="bulkAssignBtn"
                            class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            Apply Assignment
                        </button>
                    </div>
                </div>

                {{-- Data Table --}}
                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <table id="Leads-table" class="w-full text-sm text-left">
                        <thead
                            class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 font-semibold uppercase text-xs">
                            <tr>
                                <th class="w-10 p-4 text-center">
                                    <input type="checkbox" id="selectAll"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                </th>
                                <th class="p-3">Client Details</th>
                                <th class="p-3">Location</th>
                                <th class="p-3">Last Activity</th>
                                <th class="p-3">Inquiry</th>
                                <th class="p-3">Proposal</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Assigned To</th>
                                <th class="p-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            {{-- DataTables loads here --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <x-edit-lead />
            <x-followup-modal :packages="$packages" />
            <x-share-modal :packages="$packages" />
            <x-invoice-modal :packages="$packages" />
            <x-payment-modal />
        </div>
    </div>
    <script>
        const activeClasses = ['bg-blue-600', 'text-white', 'border-blue-600', 'shadow-md', 'font-semibold'];
        const inactiveClasses = ['bg-white', 'text-gray-600', 'border-gray-300', 'hover:bg-blue-50', 'hover:text-blue-600',
            'hover:border-blue-400', 'font-medium'
        ];

        // Helper function to toggle button styles
        function toggleButtonState(buttons, activeButton) {
            buttons.forEach(btn => {
                btn.classList.remove(...activeClasses);
                btn.classList.add(...inactiveClasses);
            });
            activeButton.classList.remove(...inactiveClasses);
            activeButton.classList.add(...activeClasses);
        }
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
                    // UI: Toggle Styles
                    toggleButtonState(document.querySelectorAll('.status-btn'), this);

                    const value = this.dataset.value || '';

                    // Logic: Special handling for "Follow-up Taken"
                    if (value === 'Follow-up Taken') {
                        // Note: Ensure this string matches your DB value exactly, or use the slug 'followup_taken'
                        selectedStatus = 'Follow-up Taken';

                        // Force "Today" logic when switching to Follow-ups
                        selectedDateRange = 'today';

                        // UI: Sync Date Range Buttons visually to "Today"
                        const todayBtn = document.querySelector(
                            '.date-range-btn[data-value="today"]');
                        if (todayBtn) {
                            toggleButtonState(document.querySelectorAll('.date-range-btn'),
                                todayBtn);
                        }
                    } else {
                        selectedStatus = value;
                    }

                    // Refresh Table
                    datatable.page(0).draw(false);
                    // loadCounts(); // Uncomment if you have the count function
                });
            });

            // 3. Handling Category Buttons (Hot/Warm/Cold)
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // UI: Toggle Styles
                    toggleButtonState(document.querySelectorAll('.category-btn'), this);

                    // Update Variable
                    selectedLeadStatus = this.dataset.value || '';

                    // Refresh Table
                    datatable.page(0).draw(false);
                    // loadCounts(); 
                });
            });

            // 4. Handling Date Range Buttons
            document.querySelectorAll('.date-range-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // UI: Toggle Styles
                    toggleButtonState(document.querySelectorAll('.date-range-btn'), this);

                    // Update Variable
                    selectedDateRange = this.dataset.value || 'all';

                    // Refresh Table
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
                /* ---------------- MODAL STATES ---------------- */
                invoiceOpen: false,
                followOpen: false,
                shareOpen: false,
                editOpen: false,
                paymentOpen: false,
                leadPhone: "",
                whatsappMessage: "",
                whatsappPdfUrl: "", // PDF URL you want to send
                sending: false,
                /* ---------------- LEAD INFO ---------------- */
                leadId: "",
                leadName: "",
                leadEmail: "",
                peopleCount: 1,
                childCount: 0,

                /* ---------------- PACKAGES ---------------- */
                packages: @json($packages),
                selectedPackageInvoice: "",
                packageData: null,
                selectedInvoiceItems: null,
                selectedRoomType: 'standard_price',
                filteredItems: [],

                /* ---------------- PRICING ---------------- */
                packagePrice: 0,
                itemPrice: 0,
                totalPrice: 0,
                discountedPrice: 0,
                finalPricePerAdult: 0,
                selectedDiscount: 0,
                travelStartDate: "",
                animatedPrice: 0,

                /* ---------------- CARS ---------------- */
                cars: [],
                selectedCar: "",

                /* ---------------- FOLLOW-UP ---------------- */
                phoneNumber: '',
                phoneCode: '',
                fullNumber: '',
                selectedReason: '',
                followups: [],
                reasons: [],

                /* ---------------- SHARE ---------------- */
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

                /* ---------------- EDIT ---------------- */
                editForm: {},

                /* ---------------- BULK ---------------- */
                selected: [],
                bulkUser: '',

                /* ---------------- PAYMENT FIELDS ---------------- */
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

                /* ---------------- PAYMENT MODAL FUNCTIONS ---------------- */

                // Open payment modal with invoice data

                openPaymentModal(invoice) {
                    this.paymentInvoiceId = invoice.id;
                    this.paymentInvoiceNumber = invoice.invoice_no;
                    this.amount = Number(invoice.final_price);
                    this.remainingAmount = Number(invoice.remaining_amount);

                    this.resetPaymentForm();
                    this.fetchPaymentMethods();
                    this.paymentOpen = true;
                },
                handleImageUpload(e) {
                    this.paymentImage = e.target.files?.[0] || null;
                },
                get partialPaymentWithoutNextDate() {
                    return this.isPartial && !this.nextPaymentDate;
                },
                get isPartial() {
                    return this.paidAmount > 0 && this.paidAmount < this.remainingAmount;
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
                init() {
                    this.$watch('paymentMethodId', id => {
                        this.selectedMethod =
                            this.paymentMethods.find(m => m.id == id) || null;
                    });
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



                // Close payment modal and reset fields
                closePaymentModal() {
                    this.paymentOpen = false;
                    this.paymentInvoiceId = '';
                    this.paymentInvoiceNumber = '';
                    this.amount = 0;
                    this.remainingAmount = 0;
                    this.paidAmount = 0;
                    this.paymentMethod = '';
                    this.transactionId = '';
                    this.nextPaymentDate = '';
                    this.paymentNotes = '';
                    this.partialPaymentWithoutNextDate = false;
                },

                // Reactive remaining amount display
                get remainingAmountReactive() {
                    return Math.max(this.remainingAmount - this.paidAmount, 0);
                },

                // Format numbers as currency
                formatCurrency(value) {
                    return Number(value || 0).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },
                fetchPaymentMethods() {
                    fetch('/payment-methods/active')
                        .then(res => res.json())
                        .then(data => this.paymentMethods = data || []);
                },
                // Submit payment logic (handles full/partial)
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

                    this.fetchFollowupReasons();
                },

                fetchFollowupReasons() {
                    fetch('/followup-reasons-api', {
                            method: 'GET',
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": '{{ csrf_token() }}',

                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                this.reasons = res.data;
                            }
                        });
                },


                handleReasonChange(reason) {
                    const now = new Date();
                    let followDate = new Date(now);

                    const isSunday = date => date.getDay() === 0;

                    const dateInput = document.querySelector('input[name="next_followup_date"]');
                    const timeInput = document.querySelector('input[name="next_followup_time"]');
                    const remarkInput = document.querySelector('textarea[name="remark"]');

                    const dateWrapper = document.getElementById('dateWrapper');
                    const timeWrapper = document.getElementById('timeWrapper');
                    const remarkWrapper = document.getElementById('remarkWrapper');

                    /* ================= DATE ================= */
                    if (reason.date) {
                        dateWrapper.classList.remove('hidden');
                        followDate.setDate(followDate.getDate() + 1);
                        while (isSunday(followDate)) {
                            followDate.setDate(followDate.getDate() + 1);
                        }
                        dateInput.value = followDate.toISOString().split('T')[0];
                        dateInput.setAttribute('required', 'required');
                    } else {
                        dateWrapper.classList.add('hidden');
                        dateInput.value = '';
                        dateInput.removeAttribute('required');
                    }

                    /* ================= TIME ================= */
                    if (reason.time) {
                        timeWrapper.classList.remove('hidden');
                        followDate.setHours(followDate.getHours() + 2);
                        timeInput.value = followDate.toTimeString().slice(0, 5);
                        timeInput.setAttribute('required', 'required');
                    } else {
                        timeWrapper.classList.add('hidden');
                        timeInput.value = '';
                        timeInput.removeAttribute('required');
                    }

                    /* ================= REMARK ================= */
                    if (reason.remark) {
                        remarkWrapper.classList.remove('hidden');
                        remarkInput.removeAttribute('disabled');
                        remarkInput.setAttribute('required', 'required');
                    } else {
                        remarkWrapper.classList.add('hidden');
                        remarkInput.value = '';
                        remarkInput.setAttribute('disabled', 'disabled');
                        remarkInput.removeAttribute('required');
                    }
                },





                closeFollow() {
                    this.followOpen = false;
                },


                /* ---------------- SHARE MODAL ---------------- */

                handleShare(event) {
                    const button = event.currentTarget;

                    // Read data attributes from button
                    const lead = {
                        id: button.dataset.id,
                        name: button.dataset.name,
                        email: button.dataset.email,
                        phone_code: button.dataset.phoneCode,
                        phone_number: button.dataset.phoneNumber,
                        package_id: button.dataset.packageId,
                        people_count: Number(button.dataset.peopleCount) || 1,
                        child_count: Number(button.dataset.childCount) || 0,
                    };

                    // Set modal state
                    this.shareLeadId = lead.id;
                    this.shareLeadName = lead.name;
                    this.leadEmail = lead.email;
                    this.leadPhone = `${lead.phone_code}${lead.phone_number}`;
                    this.peopleCount = lead.people_count;
                    this.childCount = lead.child_count;

                    // Default to first package if none selected
                    this.selectedPackage = lead.package_id || (this.allPackages[0]?.id ?? "");

                    // Show modal
                    this.showDropdown = true;
                    this.showSelectedPackage = true;
                    this.shareOpen = true;

                    // Fetch package docs for the selected package
                    if (this.selectedPackage) {
                        this.fetchPackageDocs(this.selectedPackage);
                    }
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

                            // Load template messages if exists
                            this.whatsappMessage = data.package.messageTemplate?.whatsapp?.text || "";
                            this.whatsappMedia = data.package.messageTemplate?.whatsapp?.media || "";
                            this.emailSubject = data.package.messageTemplate?.email?.subject || "";
                            this.emailBody = data.package.messageTemplate?.email?.body || "";
                            this.emailMedia = data.package.messageTemplate?.email?.media || "";

                            // Reset checkboxes
                            this.sendWhatsAppChecked = false;
                            this.sendEmailChecked = false;
                            this.selectedMediaType = 'template'; // default media type
                        });
                },

                closeShare() {
                    this.shareOpen = false;
                    this.selectedPackageDocs = [];
                    this.selectedDocs = [];
                    this.selectedPackagePdf = null;
                    this.whatsappMessage = "";
                    this.whatsappMedia = "";
                    this.emailSubject = "";
                    this.emailBody = "";
                    this.emailMedia = "";
                    this.sendWhatsAppChecked = false;
                    this.sendEmailChecked = false;
                    this.selectedMediaType = 'template';
                },

                sendSelected() {
                    if (this.sendEmailChecked) this.sendEmail();
                    if (this.sendWhatsAppChecked) this.sendWhatsApp();
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
                        subject: this.emailSubject,
                        body: this.emailBody,
                        media_type: this.selectedMediaType, // send media type
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
                },

                async sendWhatsApp() {
                    if (!this.leadPhone || !this.selectedPackage) {
                        alert("Phone number & Package are required.");
                        return;
                    }

                    if (!this.whatsappMessage && !this.whatsappMedia && !this.selectedPackagePdf) {
                        alert("WhatsApp message or media is required.");
                        return;
                    }

                    const payload = {
                        recipient: this.leadPhone,
                        text: this.whatsappMessage,
                        package_id: this.selectedPackage,
                        media_type: this.selectedMediaType // send selected media type
                    };

                    this.sending = true;

                    try {
                        const res = await fetch("{{ url('whatsapp/send-media-json') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            credentials: "same-origin",
                            body: JSON.stringify(payload),
                        });

                        let data = await res.json();

                        const successMessage = data.message?.toLowerCase() ?? "";
                        if (data.success || data.status === "success" || successMessage.includes("sent successfully")) {
                            alert("📨 WhatsApp sent successfully!");
                            this.closeShare();
                            return;
                        }

                        console.error("WhatsApp API Error:", data);
                        alert(data.error ?? data.message ?? "Failed to send WhatsApp message.");

                    } catch (err) {
                        console.error("WhatsApp Error:", err);
                        alert("Error sending WhatsApp. Please check logs.");
                    } finally {
                        this.sending = false;
                    }
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
