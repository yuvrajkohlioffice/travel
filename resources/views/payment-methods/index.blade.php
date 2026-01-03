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
        <div x-show="modalOpen" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-2xl rounded-xl p-6 relative">

                <button @click="closeModal()" class="absolute top-3 right-3">✖</button>

                <h2 class="text-xl font-bold mb-4" x-text="modalTitle"></h2>

                <form @submit.prevent="saveMethod()" class="space-y-4">

                    <input type="hidden" x-model="method.id">

                    <!-- Name -->
                    <input x-model="method.name" class="w-full p-2 border rounded" placeholder="Payment Method Name">

                    <!-- Type -->
                    <select x-model="method.type" class="w-full p-2 border rounded">
                        <option value="">Select Type</option>
                        <option value="cash">Cash</option>
                        <option value="bank">Bank</option>
                        <option value="online">Online</option>
                        <option value="wallet">Wallet</option>
                    </select>

                    <!-- BANK FIELDS -->
                    <div x-show="method.type === 'bank'" class="grid grid-cols-2 gap-3">
                        <input x-model="method.bank_name" placeholder="Bank Name" class="p-2 border rounded">
                        <input x-model="method.account_name" placeholder="Account Name" class="p-2 border rounded">
                        <input x-model="method.account_number" placeholder="Account Number" class="p-2 border rounded">
                        <input x-model="method.ifsc_code" placeholder="IFSC Code" class="p-2 border rounded">
                    </div>

                    <!-- TAX -->
                    <label class="flex gap-2 items-center">
                        <input type="checkbox" x-model="method.is_tax_applicable">
                        Tax Applicable
                    </label>

                    <div x-show="method.is_tax_applicable" class="grid grid-cols-3 gap-3">
                        <input x-model="method.tax_percentage" placeholder="Tax %" class="p-2 border rounded">
                        <input x-model="method.tax_name" placeholder="GST/VAT" class="p-2 border rounded">
                        <input x-model="method.tax_number" placeholder="Tax Number" class="p-2 border rounded">
                    </div>

                    <!-- STATUS -->
                    <label class="flex gap-2 items-center">
                        <input type="checkbox" x-model="method.is_active">
                        Active
                    </label>

                    <textarea x-model="method.description" class="w-full p-2 border rounded" placeholder="Description"></textarea>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="closeModal()" class="px-4 py-2 bg-gray-200 rounded">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded">
                            Save
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- SCRIPT -->
        <script>
            function paymentMethodModal() {
                return {
                    modalOpen: false,
                    modalTitle: 'Add Payment Method',
                    table: null,

                    method: {},

                    resetForm() {
                        this.method = {
                            id: '',
                            name: '',
                            type: '',
                            is_tax_applicable: false,
                            tax_percentage: '',
                            tax_name: '',
                            tax_number: '',
                            bank_name: '',
                            account_name: '',
                            account_number: '',
                            ifsc_code: '',
                            description: '',
                            is_active: true,
                        }
                    },

                    init() {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            }
                        });
                        this.resetForm();

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

                        $('#paymentMethodsTable').on('click', '.edit-btn', e => {
                            this.openModal($(e.currentTarget).data('row'));
                        });

                        $('#paymentMethodsTable').on('click', '.delete-btn', e => {
                            this.deleteMethod($(e.currentTarget).data('id'));
                        });
                    },

                    openModal(data = null) {
                        this.modalTitle = data ? 'Edit Payment Method' : 'Add Payment Method';
                        this.resetForm();
                        if (data) this.method = data;
                        this.modalOpen = true;
                    },

                    closeModal() {
                        this.modalOpen = false;
                    },

                    saveMethod() {
                        let url = this.method.id ?
                            `/payment-methods/${this.method.id}` :
                            `/payment-methods`;

                        let type = this.method.id ? 'PUT' : 'POST';

                        $.ajax({
                            url,
                            type,
                            data: {
                                ...this.method,
                                is_active: this.method.is_active ? 1 : 0,
                                is_tax_applicable: this.method.is_tax_applicable ? 1 : 0,
                            },
                            success: () => {
                                this.table.ajax.reload(null, false);
                                this.closeModal();
                            }
                        });
                    },


                    deleteMethod(id) {
                        if (!confirm('Delete this method?')) return;

                        $.ajax({
                            url: `/payment-methods/${id}`,
                            type: 'DELETE',
                            success: () => {
                                this.table.ajax.reload(null, false);
                            }
                        });
                    }

                }
            }
        </script>

    </div>
</x-app-layout>
