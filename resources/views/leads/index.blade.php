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

                    <select x-model="bulkUser"
                        class="w-1/6 rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">
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


                <div class="bg-white rounded-lg border p-4 overflow-x-auto">
                    <div class="w-full mb-4 flex flex-wrap gap-2 items-center">
                        <!-- Status counters -->


                        <!-- Filters -->
                        <input type="text" id="filter-id" placeholder="Search ID"
                            class="border px-4 py-2 rounded-lg border border-gray-300 text-sm">
                        <input type="text" id="filter-client" placeholder="Search Client"
                            class="border px-4 py-2 rounded-lg border border-gray-300 text-sm">
                        <input type="text" id="filter-location" placeholder="Search Location"
                            class="border px-4 py-2 rounded-lg border border-gray-300 text-sm">

                        <div id="status-buttons" class="flex flex-wrap gap-2 mb-4">
                            @php
                                $btnStatuses = ['Follow-up Taken', 'Converted', 'Approved', 'Rejected'];
                            @endphp

                            <button data-value=""
                                class="status-btn px-4 py-2 rounded-lg border border-gray-300 text-sm">
                                All
                            </button>

                            @foreach ($btnStatuses as $s)
                                <button data-value="{{ $s }}"
                                    class="status-btn px-4 py-2 rounded-lg border border-gray-300 text-sm">
                                    {{ $s }}
                            @endforeach
                        </div>
                        <div id="date-range-buttons" class="flex flex-wrap gap-2 mb-4">
                            <button class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm"
                                data-value="today">Today <span id="count-today">0</span></button>
                            <button class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm"
                                data-value="week">This Week <span id="count-week">0</span></button>
                            <button class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm"
                                data-value="month">This Month <span id="count-month">0</span< /button>
                                    <button class="date-range-btn px-4 py-2 rounded-lg border border-gray-300 text-sm"
                                        data-value="yesterday">Yesterday</button>
                        </div>


                        <select id="filter-assigned"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm bg-white">
                            <option value="">All Assigned</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->name }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <table id="Leads-table" class="min-w-full border border-gray-200">
                        <thead>
                            <tr>
                                <th class="w-10">#</th>
                                <th class="w-20">ID</th>
                                <th class="w-48">Client Info</th>
                                <th class="w-40">Location</th>
                                <th class="w-32">Reminder</th>
                                <th class="w-40">Inquiry</th>
                                <th class="w-40">Proposal</th>
                                <th class="w-32">Status</th>
                                <th class="w-40">Assigned</th>
                                <th class="w-24">Action</th>

                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <script>
                        $(document).ready(function() {
                            let selectedStatus = '';
                            let selectedDateRange = ''; // today, week, month, yesterday

                            // ----------------- Load Counts -----------------
                            function loadCounts() {
                                $.ajax({
                                    url: "{{ route('leads.counts') }}",
                                    data: {
                                        id: $('#filter-id').val(),
                                        client_name: $('#filter-client').val(),
                                        location: $('#filter-location').val(),
                                        assigned: $('#filter-assigned').val(),
                                        status: selectedStatus,
                                        date_range: selectedDateRange
                                    },
                                    success: function(res) {
                                        $('#count-today').text(res.today ?? 0);
                                        $('#count-week').text(res.week ?? 0);
                                        $('#count-month').text(res.month ?? 0);
                                        $('#count-yesterday').text(res.yesterday ?? 0);
                                        $('#count-all').text(res.all ?? 0);
                                    }
                                });
                            }

                            // ----------------- DataTable -----------------
                            let table = $('#Leads-table').DataTable({
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
                                        data: 'checkbox',
                                        orderable: false,
                                        searchable: false
                                    },
                                    {
                                        data: 'id'
                                    },
                                    {
                                        data: 'client_info',
                                        orderable: false
                                    },
                                    {
                                        data: 'location',
                                        orderable: false
                                    },
                                    {
                                        data: 'reminder',
                                        orderable: false
                                    },
                                    {
                                        data: 'inquiry',
                                        orderable: false
                                    },
                                    {
                                        data: 'proposal',
                                        orderable: false
                                    },
                                    {
                                        data: 'status',
                                        orderable: false
                                    },
                                    {
                                        data: 'assigned',
                                        orderable: false
                                    },
                                    {
                                        data: 'action',
                                        orderable: false
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
                                drawCallback: loadCounts
                            });

                            function redrawTable() {
                                table.draw();
                                loadCounts();
                            }

                            // ----------------- Status Buttons -----------------
                            $(".status-btn").on("click", function() {
                                $(".status-btn").removeClass("bg-blue-500 text-white");
                                $(this).addClass("bg-blue-500 text-white");
                                selectedStatus = $(this).data("value");
                                redrawTable();
                            });

                            // ----------------- Date Range Buttons -----------------
                            $(".date-range-btn").on("click", function() {
                                $(".date-range-btn").removeClass("bg-blue-500 text-white");
                                $(this).addClass("bg-blue-500 text-white");
                                selectedDateRange = $(this).data("value"); // 'today', 'week', 'month', 'yesterday'
                                redrawTable();
                            });

                            // ----------------- Text Filters -----------------
                            $('#filter-id, #filter-client, #filter-location, #filter-assigned')
                                .on('keyup change', redrawTable);

                            $('.dataTables_filter input').on('keyup', function() {
                                table.draw();
                            });

                            // ----------------- Select All -----------------
                            $('#selectAll').on('click', function() {
                                $('.row-checkbox').prop('checked', this.checked);
                            });

                            $('#Leads-table tbody').on('change', '.row-checkbox', function() {
                                $('#selectAll').prop('checked', $('.row-checkbox:checked').length === $('.row-checkbox')
                                    .length);
                            });

                            // ----------------- Initial load -----------------
                            loadCounts();
                        });
                    </script>







                </div>

            </div>
        </div>


        {{-- Modals --}}
        <x-edit-lead />
        <x-followup-modal :packgaes="$packages" />
        <x-share-modal :packgaes="$packages" />
        <x-invoice-modal :packgaes="$packages" />
        <x-payment-modal />

    </div>



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
