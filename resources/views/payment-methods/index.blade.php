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
                    <select x-model="method.company_id" class="w-full p-2 border rounded">
                        <option value="">Select Company</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                        @endforeach


                    </select>

                    <!-- BANK FIELDS -->
                    <div x-show="method.type === 'bank'" class="grid grid-cols-2 gap-3">
                        <input x-model="method.bank_name" placeholder="Bank Name" class="p-2 border rounded">
                        <input x-model="method.account_name" placeholder="Account Name" class="p-2 border rounded">
                        <input x-model="method.account_number" placeholder="Account Number" class="p-2 border rounded">
                        <input x-model="method.ifsc_code" placeholder="IFSC Code" class="p-2 border rounded">
                    </div>

                    <!-- TAX -->
                    <!-- TAX -->
                    <div class="flex items-center gap-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" x-model="method.is_tax_applicable" class="sr-only">
                                <div class="w-11 h-6 bg-gray-300 rounded-full shadow-inner transition-colors duration-300"
                                    :class="{ 'bg-green-500': method.is_tax_applicable }"></div>
                                <div class="dot absolute w-5 h-5 bg-white rounded-full shadow -left-1 -top-1 transition transform duration-300"
                                    :class="{ 'translate-x-full': method.is_tax_applicable }"></div>
                            </div>
                            <span class="ml-3 text-gray-700 dark:text-gray-200">Tax Applicable</span>
                        </label>
                    </div>

                    <!-- IMAGE PROOF -->
                    <div class="flex items-center gap-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" x-model="method.image_proof_required" class="sr-only">
                                <div class="w-11 h-6 bg-gray-300 rounded-full shadow-inner transition-colors duration-300"
                                    :class="{ 'bg-green-500': method.image_proof_required }"></div>
                                <div class="dot absolute w-5 h-5 bg-white rounded-full shadow -left-1 -top-1 transition transform duration-300"
                                    :class="{ 'translate-x-full': method.image_proof_required }"></div>
                            </div>
                            <span class="ml-3 text-gray-700 dark:text-gray-200">Image Proof Required</span>
                        </label>
                    </div>

                    <!-- STATUS -->
                    <div class="flex items-center gap-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" x-model="method.is_active" class="sr-only">
                                <div class="w-11 h-6 bg-gray-300 rounded-full shadow-inner transition-colors duration-300"
                                    :class="{ 'bg-green-500': method.is_active }"></div>
                                <div class="dot absolute w-5 h-5 bg-white rounded-full shadow -left-1 -top-1 transition transform duration-300"
                                    :class="{ 'translate-x-full': method.is_active }"></div>
                            </div>
                            <span class="ml-3 text-gray-700 dark:text-gray-200">Active</span>
                        </label>
                    </div>


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
        loading: false, // optional loading state
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
                image_proof_required: false, // added
            };
        },

        // Initialize the DataTable and AJAX setup
        init() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            this.resetForm();

            this.table = $('#paymentMethodsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('payment-methods.index') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name' },
                    { data: 'company' },
                    { data: 'type' },
                    { data: 'tax' },
                    { data: 'status' },
                    { data: 'action', orderable: false, searchable: false },
                ]
            });

            // Edit button click
            $('#paymentMethodsTable').on('click', '.edit-btn', e => {
                const data = $(e.currentTarget).data('row');
                if (data) this.openModal(data);
            });

            // Delete button click
            $('#paymentMethodsTable').on('click', '.delete-btn', e => {
                const id = $(e.currentTarget).data('id');
                if (id) this.deleteMethod(id);
            });
        },

        // Open modal for Add/Edit
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

        // Save method via AJAX
        saveMethod() {
            this.loading = true;
            const url = this.method.id ? `/payment-methods/${this.method.id}` : `/payment-methods`;
            const type = this.method.id ? 'PUT' : 'POST';

            // Convert booleans to 1/0
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
                success: (res) => {
                    this.table.ajax.reload(null, false);
                    this.closeModal();
                    this.loading = false;
                    // Optional: show success toast
                },
                error: (err) => {
                    this.loading = false;
                    console.error('Error saving payment method:', err);
                    alert('Failed to save. Please check the console.');
                }
            });
        },

        // Delete method
        deleteMethod(id) {
            if (!confirm('Are you sure you want to delete this payment method?')) return;

            $.ajax({
                url: `/payment-methods/${id}`,
                type: 'DELETE',
                success: () => {
                    this.table.ajax.reload(null, false);
                    // Optional: show delete success toast
                },
                error: (err) => {
                    console.error('Error deleting payment method:', err);
                    alert('Failed to delete. Please check the console.');
                }
            });
        },
    };
}
</script>


    </div>
</x-app-layout>
