<x-app-layout>
    <div x-data="leadModals()" x-cloak class="min-h-screen ">
        <div class="ml-64 flex justify-center items-start min-h-screen p-6">
            <div class="w-full max-w-7xl space-y-6">

                <!-- Header -->
                <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-sm">
                            <i class="fa-solid fa-people-group text-xl text-gray-700 dark:text-gray-200"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Leads</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage leads, follow-ups, and assignments</p>
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
                        <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                            @csrf
                            <label class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm px-3 py-2 cursor-pointer hover:shadow transition">
                                <span class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                                    <i class="fa-solid fa-upload"></i>
                                    <span>Upload</span>
                                </span>
                                <input type="file" name="file" accept=".xlsx,.csv" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm hover:opacity-95 transition">
                                Import
                            </button>
                        </form>

                        {{-- Add lead --}}
                        <a href="{{ route('leads.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
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

                <!-- Filters & Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg  p-4 shadow-sm overflow-x-auto">
                    <!-- Filters -->
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

                    <!-- Status buttons -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button data-value=""
                            class="status-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-50 transition">All</button>
                        @foreach (['Follow-up Taken', 'Converted', 'Approved', 'Rejected'] as $s)
                            <button data-value="{{ $s }}"
                                class="status-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-50 transition">{{ $s }}</button>
                        @endforeach
                    </div>

                    <!-- Date range -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button data-value="all"
                            class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-50 transition">All
                            <span id="count-all" class="ml-2">0</span></button>
                        <button data-value="today"
                            class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-50 transition">Today
                            <span id="count-today" class="ml-2">0</span></button>
                        <button data-value="week"
                            class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-50 transition">This
                            Week <span id="count-week" class="ml-2">0</span></button>
                        <button data-value="month"
                            class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-50 transition">This
                            Month <span id="count-month" class="ml-2">0</span></button>
                        <button data-value="yesterday"
                            class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-blue-50 transition">Yesterday
                            <span id="count-yesterday" class="ml-2">0</span></button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table id="Leads-table" class="min-w-full  border-gray-200 dark:border-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="w-10 p-2 text-left">
                                        <input type="checkbox" id="selectAll"
                                            class="rounded border-gray-300 focus:ring-2 focus:ring-blue-500">
                                    </th>
                                    <th class="w-20 p-2 text-left">ID</th>
                                    <th class="w-48 p-2 text-left">Client Info</th>
                                    <th class="w-40 p-2 text-left">Location</th>
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
                ajax: {
                    url: "{{ route('leads.data') }}",
                    data: function(d) {
                        d.id = $('#filter-id').val();
                        d.client_name = $('#filter-client').val();
                        d.location = $('#filter-location').val();
                        d.status = selectedStatus;
                        d.assigned = $('#filter-assigned').val();
                        d.date_range = selectedDateRange;
                        d.search_query = $('.dataTables_filter input').val();
                    }
                },
                columns: [{
                        // render checkbox using ID
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'p-2',
                        render: function(data, type, row, meta) {
                            return `<input type="checkbox" class="row-checkbox rounded border-gray-300 focus:ring-2 focus:ring-blue-500" data-id="${data}">`;
                        }
                    },
                    {
                        data: 'id',
                        className: 'p-2'
                    },
                    {
                        data: 'client_info',
                        orderable: false,
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
                buttons: [{
                        extend: 'excelHtml5',
                        text: 'Excel',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ],
                pageLength: 50,
                order: [
                    [1, 'desc']
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
            document.querySelectorAll('.date-range-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.date-range-btn').forEach(b => b.classList.remove(
                        'bg-blue-500', 'text-white'));
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
                    alert('Please select a user to assign.');
                    return;
                }

                // Validate selected leads
                if (selectedIds.size === 0) {
                    alert('No leads selected.');
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

                        alert(json.message || "Leads assigned successfully.");
                    } else {
                        alert(json.message || "Failed to assign leads.");
                    }

                } catch (error) {
                    console.error(error);
                    alert("An unexpected error occurred while assigning leads.");
                }

                // Re-enable button
                bulkAssignBtn.disabled = false;
                bulkAssignBtn.innerHTML = '<i class="fa-solid fa-user-check mr-2"></i> Assign';

            });


            // ---------- Keep counts updated ----------
            function loadCounts() {
                fetch("{{ route('leads.counts') }}?id=" + encodeURIComponent($('#filter-id').val() || '') +
                        "&client_name=" + encodeURIComponent($('#filter-client').val() || '') +
                        "&location=" + encodeURIComponent($('#filter-location').val() || '') +
                        "&assigned=" + encodeURIComponent($('#filter-assigned').val() || '') +
                        "&status=" + encodeURIComponent(selectedStatus || '') +
                        "&date_range=" + encodeURIComponent(selectedDateRange || ''), {
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

    <!-- Modals -->





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
                reasons: [
                    'Call Back Later', 'Call Me Tomorrow', 'Payment Tomorrow',
                    'Talk With My Partner', 'Work with other company',
                    'Not Interested', 'Interested', 'Wrong Information',
                    'Not Pickup', 'Other'
                ],

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
                paymentInvoiceId: '', // Invoice ID
                paymentInvoiceNumber: '', // Invoice number
                amount: 0, // Total invoice amount
                remainingAmount: 0, // Remaining amount
                paidAmount: 0, // Paid amount input
                paymentMethod: '', // Payment method
                transactionId: '', // Transaction ID
                nextPaymentDate: '', // Next payment date (for partial)
                paymentNotes: '', // Notes
                partialPaymentWithoutNextDate: false, // Validation flag

                /* ---------------- PAYMENT MODAL FUNCTIONS ---------------- */

                // Open payment modal with invoice data
                openPaymentModal(invoice) {
                    this.paymentInvoiceId = invoice.id;
                    this.paymentInvoiceNumber = invoice.invoice_no;
                    this.amount = Number(invoice.amount || invoice.remaining_amount || 0);
                    this.remainingAmount = Number(invoice.remaining_amount || this.amount);
                    this.paidAmount = 0;
                    this.paymentMethod = '';
                    this.transactionId = '';
                    this.nextPaymentDate = '';
                    this.paymentNotes = '';
                    this.partialPaymentWithoutNextDate = false;
                    this.paymentOpen = true;
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
                    return parseFloat(value).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                // Submit payment logic (handles full/partial)
                submitPayment() {
                    // Validation
                    if (this.paidAmount <= 0) {
                        alert('Paid amount must be greater than 0');
                        return;
                    }
                    if (this.paidAmount > this.remainingAmount) {
                        alert('Paid amount cannot exceed remaining amount');
                        return;
                    }
                    if (this.paidAmount < this.remainingAmount && !this.nextPaymentDate) {
                        this.partialPaymentWithoutNextDate = true;
                        return;
                    } else {
                        this.partialPaymentWithoutNextDate = false;
                    }

                    // Prepare payload
                    const payload = {
                        invoice_id: this.paymentInvoiceId,
                        amount: this.amount,
                        paid_amount: this.paidAmount,
                        remaining_amount: this.remainingAmount - this.paidAmount,
                        status: this.paidAmount === this.remainingAmount ? 'paid' : 'partial',
                        payment_method: this.paymentMethod,
                        transaction_id: this.transactionId,
                        notes: this.paymentNotes,
                        next_payment_date: this.paidAmount < this.remainingAmount ? this.nextPaymentDate : null,
                    };

                    // Submit to backend
                    fetch('/payments', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                alert('Payment recorded successfully!');
                                this.closePaymentModal();
                                // TODO: refresh table or update UI
                            } else if (data.errors) {
                                alert(Object.values(data.errors).flat().join('\n'));
                            } else {
                                alert('Failed to record payment.');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Something went wrong.');
                        });
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
                },

                closeFollow() {
                    this.followOpen = false;
                },


                /* ---------------- SHARE MODAL ---------------- */

                handleShare(lead) {
                    this.shareLeadId = lead.id;
                    this.shareLeadName = lead.name;
                    this.leadEmail = lead.email;

                    this.leadPhone = `${lead.phone_code}${lead.phone_number}`;

                    this.peopleCount = lead.people_count ?? 1;
                    this.childCount = lead.child_count ?? 0;

                    this.selectedPackage = lead.package_id || (this.allPackages[0]?.id ?? "");

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
                async sendWhatsApp() {
                    // Basic validations
                    if (!this.leadPhone || !this.selectedPackage) {
                        alert("Phone number & Package are required.");
                        return;
                    }

                    if (!this.selectedPackagePdf) {
                        alert("PDF URL is required to send WhatsApp message.");
                        return;
                    }

                    // Construct payload
                    const payload = {
                        recipient: this.leadPhone,
                        text: this.whatsappMessage?.trim() || "Please check the attached package details.",
                        mediaUrl: this.selectedPackagePdf,
                    };

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

                        let data;
                        try {
                            data = await res.json();
                        } catch {
                            throw new Error("Invalid JSON response from server");
                        }

                        // Success condition: check multiple possible responses
                        const successMessage = data.message?.toLowerCase() ?? "";
                        if (data.success || data.status === "success" || successMessage.includes("sent successfully")) {
                            alert("📨 WhatsApp sent successfully!");
                            this.closeShare();
                            return;
                        }

                        // If failed
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
