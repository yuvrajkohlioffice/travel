<x-app-layout>
    <div x-data="paymentMethodModal()" x-cloak class="min-h-screen">

        <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
            <div class="w-full">

                <!-- Header -->
                <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-sm">
                            <i class="fas fa-university text-xl text-gray-700 dark:text-gray-200"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                                Payment Methods
                            </h1>
                        </div>
                    </div>
                    <button @click="openModal()" class="px-5 py-2 rounded-xl bg-green-600 text-white">
                        + Add Method
                    </button>

                </header>


                <!-- Table -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <table id="paymentMethodsTable" class="min-w-full text-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Type</th>
                                <th>Tax</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>

        <!-- MODAL -->
        <!-- Modal -->
        <div x-show="modalOpen"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 transition-opacity duration-300"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div
                class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-xl shadow-xl p-6 relative overflow-y-auto max-h-[90vh]">

                <!-- Close Button -->
                <button @click="closeModal()"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 text-xl">
                    ✖
                </button>

                <!-- Modal Title -->
                <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100" x-text="modalTitle"></h2>

                <!-- Form -->
                <form @submit.prevent="saveMethod()" class="space-y-5">

                    <!-- Hidden ID -->
                    <input type="hidden" x-model="method.id">

                    <!-- Payment Method Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Payment Method
                            Name</label>
                        <input x-model="method.name"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                            placeholder="Enter payment method name">
                    </div>

                    <!-- Type & Company -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Type</label>
                            <select x-model="method.type"
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                <option value="">Select Type</option>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                                <option value="online">Online</option>
                                <option value="wallet">Wallet</option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Company</label>
                            <select x-model="method.company_id"
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Bank Fields -->
                    <div x-show="method.type === 'bank'" class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Bank
                                Name</label>
                            <input x-model="method.bank_name"
                                class="w-full p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                placeholder="Bank Name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Account
                                Name</label>
                            <input x-model="method.account_name"
                                class="w-full p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                placeholder="Account Name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Account
                                Number</label>
                            <input x-model="method.account_number"
                                class="w-full p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                placeholder="Account Number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">IFSC
                                Code</label>
                            <input x-model="method.ifsc_code"
                                class="w-full p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                placeholder="IFSC Code">
                        </div>
                    </div>

                    <!-- Toggles -->
                    <div class="grid md:grid-cols-3 gap-4">

                        <!-- Tax Applicable -->
                        <div class="flex items-center gap-2">
                            <input type="checkbox" x-model="method.is_tax_applicable" class="toggle-checkbox"
                                id="taxToggle">
                            <label for="taxToggle" class="text-gray-700 dark:text-gray-200">Tax Applicable</label>
                        </div>

                        <!-- Image Proof Required -->
                        <div class="flex items-center gap-2">
                            <input type="checkbox" x-model="method.image_proof_required" class="toggle-checkbox"
                                id="imageProofToggle">
                            <label for="imageProofToggle" class="text-gray-700 dark:text-gray-200">Image Proof
                                Required</label>
                        </div>

                        <!-- Active -->
                        <div class="flex items-center gap-2">
                            <input type="checkbox" x-model="method.is_active" class="toggle-checkbox" id="activeToggle">
                            <label for="activeToggle" class="text-gray-700 dark:text-gray-200">Active</label>
                        </div>

                    </div>

                    <!-- Tax Details -->
                    <div x-show="method.is_tax_applicable" class="grid md:grid-cols-3 gap-4">
                        <input x-model="method.tax_percentage" placeholder="Tax %"
                            class="w-full p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                        <input x-model="method.tax_name" placeholder="GST/VAT"
                            class="w-full p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                        <input x-model="method.tax_number" placeholder="Tax Number"
                            class="w-full p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    </div>

                    <!-- Description -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Description</label>
                        <textarea x-model="method.description" rows="3"
                            class="w-full p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                            placeholder="Enter description"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="closeModal()"
                            class="px-5 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Save
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Custom Toggle Styles -->
        <style>
            .toggle-checkbox {
                width: 2.5rem;
                height: 1.25rem;
                appearance: none;
                background-color: #e5e7eb;
                border-radius: 9999px;
                position: relative;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            .toggle-checkbox:checked {
                background-color: #10b981;
            }

            .toggle-checkbox::before {
                content: '';
                position: absolute;
                width: 1rem;
                height: 1rem;
                border-radius: 50%;
                background-color: white;
                top: 1px;
                left: 1px;
                transition: transform 0.3s;
            }

            .toggle-checkbox:checked::before {
                transform: translateX(1.25rem);
            }
        </style>


        <!-- SCRIPT -->
        <script>
            function paymentMethodModal() {
                return {
                    modalOpen: false,
                    modalTitle: 'Add Payment Method',
                    table: null,
                    loading: false,
                    method: {},

                    // Initialize/reset the form
                    resetForm() {
                        this.method = {
                            id: '',
                            name: '',
                            type: '',
                            company_id: '',
                            bank_name: '',
                            account_name: '',
                            account_number: '',
                            ifsc_code: '',
                            is_tax_applicable: false,
                            tax_percentage: '',
                            tax_name: '',
                            tax_number: '',
                            description: '',
                            is_active: true,
                            image_proof_required: false,
                        };
                    },

                    // Initialize DataTable
                    init() {
                        const self = this;

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        this.resetForm();

                        // Initialize DataTable
                        this.table = $('#paymentMethodsTable').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: "{{ route('payment-methods.index') }}",
                            columns: [{
                                    data: 'DT_RowIndex',
                                    orderable: false,
                                    searchable: false
                                },
                                {
                                    data: 'name'
                                },
                                {
                                    data: 'company'
                                },
                                {
                                    data: 'type'
                                },
                                {
                                    data: 'tax'
                                },
                                {
                                    data: 'status'
                                },
                                {
                                    data: 'action',
                                    orderable: false,
                                    searchable: false
                                },
                            ]
                        });

                        // Edit button click
                        $('#paymentMethodsTable').on('click', '.edit-btn', function() {
                            const data = $(this).data('row');
                            if (data) self.openModal(data);
                        });

                        // Soft delete
                        $('#paymentMethodsTable').on('click', '.delete-btn', function() {
                            const id = $(this).data('id');
                            if (!id) return;
                            if (!confirm('Are you sure you want to delete this payment method?')) return;

                            $.ajax({
                                url: `/payment-methods/${id}`,
                                type: 'DELETE',
                                success: function(res) {
                                    alert(res.message);
                                    self.table.ajax.reload(null, false);
                                },
                                error: function(err) {
                                    console.error(err);
                                    alert('Failed to delete.');
                                }
                            });
                        });

                        // Restore
                        $('#paymentMethodsTable').on('click', '.restore-btn', function() {
                            const id = $(this).data('id');
                            if (!id) return;

                            $.post(`/payment-methods/restore/${id}`, {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }, function(res) {
                                alert(res.message);
                                self.table.ajax.reload(null, false);
                            });
                        });

                        // Hard delete
                        $('#paymentMethodsTable').on('click', '.hard-delete-btn', function() {
                            const id = $(this).data('id');
                            if (!id) return;
                            if (!confirm('Are you sure to permanently delete?')) return;

                            $.ajax({
                                url: `/payment-methods/force-delete/${id}`,
                                type: 'DELETE',
                                success: function(res) {
                                    alert(res.message);
                                    self.table.ajax.reload(null, false);
                                },
                                error: function(err) {
                                    console.error(err);
                                    alert('Failed to delete permanently.');
                                }
                            });
                        });
                    },

                    // Open modal
                    openModal(data = null) {
                        this.resetForm();
                        if (data) {
                            this.method = {
                                id: data.id,
                                name: data.name,
                                type: data.type,
                                company_id: data.company_id,
                                bank_name: data.bank_name,
                                account_name: data.account_name,
                                account_number: data.account_number,
                                ifsc_code: data.ifsc_code,
                                is_tax_applicable: Boolean(data.is_tax_applicable),
                                tax_percentage: data.tax_percentage,
                                tax_name: data.tax_name,
                                tax_number: data.tax_number,
                                description: data.description,
                                is_active: Boolean(data.is_active),
                                image_proof_required: Boolean(data.image_proof_required),
                            };
                            this.modalTitle = 'Edit Payment Method';
                        } else {
                            this.modalTitle = 'Add Payment Method';
                        }
                        this.modalOpen = true;
                    },

                    // Close modal
                    closeModal() {
                        this.modalOpen = false;
                    },

                    // Save method
                    saveMethod() {
                        this.loading = true;
                        const self = this;
                        const url = this.method.id ? `/payment-methods/${this.method.id}` : `/payment-methods`;
                        const type = this.method.id ? 'PUT' : 'POST';

                        const payload = {
                            ...this.method,
                            is_active: this.method.is_active ? 1 : 0,
                            is_tax_applicable: this.method.is_tax_applicable ? 1 : 0,
                            image_proof_required: this.method.image_proof_required ? 1 : 0,
                        };

                        $.ajax({
                            url,
                            type,
                            data: payload,
                            success(res) {
                                self.table.ajax.reload(null, false);
                                self.closeModal();
                                self.loading = false;
                                alert('Payment method saved successfully.');
                            },
                            error(err) {
                                self.loading = false;
                                console.error(err);
                                alert('Failed to save payment method.');
                            }
                        });
                    }
                }
            }
        </script>



    </div>
</x-app-layout>
