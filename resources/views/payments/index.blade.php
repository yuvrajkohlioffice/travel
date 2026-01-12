<x-app-layout>
    <div x-data="paymentPage({
        invoices: {{ Js::from($invoices) }},
        methods: {{ Js::from($paymentMethods) }}
    })" x-cloak class="min-h-screen">

        <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
            <div class="w-full">
                <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-sm">
                            <i class="fas fa-credit-card text-xl text-gray-700 dark:text-gray-200"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Payments</h1>
                            <p class="text-sm text-gray-500">Manage invoice payments</p>
                        </div>
                    </div>
                    <button @click="openModal()"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2 shadow-lg transition">
                        <i class="fas fa-plus"></i> Add Payment
                    </button>
                </header>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm overflow-x-auto">
                    <table id="paymentTable" class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="text-left p-3">ID</th>
                                <th class="text-left p-3">Invoice</th>
                                <th class="text-left p-3">Paid</th>
                                <th class="text-left p-3">Remaining</th>
                                <th class="text-left p-3">Method</th>
                                <th class="text-left p-3">Proof</th> <th class="text-left p-3">Status</th>
                                <th class="text-left p-3">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div x-show="modalOpen" x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">

                <div @click.outside="closeModal()" x-transition
                    class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-3xl shadow-2xl p-6 relative overflow-y-auto max-h-[90vh]">

                    <button @click="closeModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>

                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2 text-center" x-text="modalTitle"></h2>

                    <div x-show="selectedInvoice"
                        class="text-center mb-6 p-3 bg-blue-50 dark:bg-gray-700 rounded-lg border border-blue-100 dark:border-gray-600">
                        <p class="text-blue-600 dark:text-blue-300 font-semibold text-lg">
                            Invoice #<span x-text="selectedInvoice?.invoice_no"></span>
                        </p>
                        <div class="flex justify-center gap-6 text-sm mt-2 text-gray-600 dark:text-gray-300">
                            <div class="flex flex-col">
                                <span class="text-xs uppercase tracking-wide text-gray-400">Total Bill</span>
                                <strong class="text-base" x-text="formatCurrency(invoiceTotal)"></strong>
                            </div>
                            <div class="w-px bg-gray-300 dark:bg-gray-500"></div>
                            <div class="flex flex-col">
                                <span class="text-xs uppercase tracking-wide text-gray-400">Current Due</span>
                                <strong class="text-base text-red-500" x-text="formatCurrency(invoiceDue)"></strong>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="savePayment()" class="space-y-4" enctype="multipart/form-data">
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Invoice</label>
                            <select x-model="payment.invoice_id" @change="handleInvoiceChange()"
                                class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                <option value="">Select Invoice</option>
                                <template x-for="inv in invoices" :key="inv.id">
                                    <option :value="inv.id" x-text="`#${inv.invoice_no} (Pending: ₹${inv.pending_amount})`"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Payment Method</label>
                            <select x-model="payment.payment_method_id" @change="handleMethodChange()"
                                class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                <option value="">Select Method</option>
                                <template x-for="method in methods" :key="method.id">
                                    <option :value="method.id" x-text="method.name"></option>
                                </template>
                            </select>
                        </div>

                        <div x-show="selectedMethodDetails" x-transition class="space-y-3">
                            <div x-show="selectedMethodDetails?.bank_name"
                                class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border dark:border-gray-600 text-sm">
                                <p class="font-bold text-gray-700 dark:text-gray-200">Bank Details:</p>
                                <p class="text-gray-600 dark:text-gray-300">
                                    <span x-text="selectedMethodDetails?.bank_name"></span>
                                    <span x-show="selectedMethodDetails?.account_number"> - Account: <span
                                            x-text="selectedMethodDetails?.account_number"></span></span>
                                    <br x-show="selectedMethodDetails?.ifsc_code">
                                    <span x-show="selectedMethodDetails?.ifsc_code">IFSC: <span
                                            x-text="selectedMethodDetails?.ifsc_code"></span></span>
                                </p>
                            </div>
                            <div x-show="selectedMethodDetails?.is_tax_applicable"
                                class="p-3 bg-yellow-50 dark:bg-gray-700 rounded-lg border border-yellow-100 dark:border-gray-600 text-sm">
                                <p class="font-bold text-gray-700 dark:text-gray-200">Tax Information:</p>
                                <p class="text-gray-600 dark:text-gray-300">
                                    <span x-text="selectedMethodDetails?.tax_name"></span>: <span
                                        x-text="selectedMethodDetails?.tax_percentage + '%'"></span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Paid Amount (₹)</label>
                                <input type="number" step="0.01" x-model.number="payment.paid_amount"
                                    class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                    placeholder="0.00">
                                <p class="text-xs mt-1" :class="remainingAfterPayment < 0 ? 'text-red-500' : 'text-green-600'">
                                    Remaining after this: <span x-text="formatCurrency(remainingAfterPayment)"></span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Transaction ID</label>
                                <input type="text" x-model="payment.transaction_id"
                                    class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                    placeholder="e.g. UPI Ref / Cheque No">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div x-show="isPartialPayment">
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                                    Next Payment Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" x-model="payment.next_payment_date"
                                    class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            </div>

                            <div x-show="selectedMethodDetails?.image_proof_required || true">
                                <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                                    Proof/Screenshot <span x-show="selectedMethodDetails?.image_proof_required" class="text-red-500">*</span>
                                </label>
                                <input type="file" @change="handleFileChange" accept="image/*"
                                    class="mt-1 w-full p-2 block text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                                
                                <div x-show="currentImageUrl" class="mt-2 text-xs">
                                    <a :href="currentImageUrl" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1">
                                        <i class="fas fa-external-link-alt"></i> View Current Proof
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Notes</label>
                            <textarea x-model="payment.notes" rows="2"
                                class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                placeholder="Optional notes"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
                            <button type="button" @click="closeModal()"
                                class="px-5 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                                Cancel
                            </button>
                            <button type="submit" :disabled="isLoading"
                                class="px-6 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow font-semibold disabled:opacity-50">
                                <span x-show="!isLoading">Save Payment</span>
                                <span x-show="isLoading">Processing...</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <script>
            function paymentPage(backendData) {
                return {
                    invoices: backendData.invoices || [],
                    methods: backendData.methods || [],

                    modalOpen: false,
                    isLoading: false,
                    modalTitle: 'Add Payment',
                    table: null,
                    imageFile: null,
                    currentImageUrl: null, // New variable to store existing image URL

                    selectedInvoice: null,
                    selectedMethodDetails: null,

                    payment: {
                        id: '',
                        invoice_id: '',
                        paid_amount: '',
                        payment_method_id: '',
                        transaction_id: '',
                        next_payment_date: '',
                        notes: ''
                    },

                    get invoiceTotal() { return this.selectedInvoice ? parseFloat(this.selectedInvoice.final_price) : 0; },
                    get invoiceDue() { return this.selectedInvoice ? parseFloat(this.selectedInvoice.pending_amount) : 0; },
                    get remainingAfterPayment() {
                        const paid = parseFloat(this.payment.paid_amount) || 0;
                        return Math.max(this.invoiceDue - paid, 0);
                    },
                    get isPartialPayment() {
                        return this.remainingAfterPayment > 0 && (parseFloat(this.payment.paid_amount) > 0);
                    },
                    formatCurrency(value) {
                        return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(value);
                    },

                    init() {
                        const self = this;
                        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

                        this.table = $('#paymentTable').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: "{{ route('payments.index') }}",
                            columns: [
                                { data: 'id' },
                                { data: 'invoice_no' },
                                { data: 'paid_amount' },
                                { data: 'remaining_amount' },
                                { data: 'payment_method' },
                                { data: 'proof', orderable: false, searchable: false }, // ✅ Added Proof Column
                                { data: 'status' },
                                { data: 'action', orderable: false, searchable: false }
                            ]
                        });

                        window.addEventListener('edit-payment', e => {
                            self.openModal(e.detail);
                        });

                        $('#paymentTable').on('click', '.delete-btn', function() {
                            const id = $(this).data('id');
                            self.deletePayment(id);
                        });
                    },

                    handleInvoiceChange() {
                        const id = this.payment.invoice_id;
                        this.selectedInvoice = this.invoices.find(i => i.id == id) || null;
                    },

                    handleMethodChange() {
                        const id = this.payment.payment_method_id;
                        this.selectedMethodDetails = this.methods.find(m => m.id == id) || null;
                    },

                    handleFileChange(event) {
                        this.imageFile = event.target.files[0];
                    },

                    openModal(data = null) {
                        this.modalTitle = data ? 'Edit Payment' : 'Add Payment';
                        this.imageFile = null;
                        this.currentImageUrl = null; // Reset image URL

                        const fileInput = document.querySelector('input[type="file"]');
                        if (fileInput) fileInput.value = '';

                        if (data) {
                            // Edit Mode
                            this.payment = {
                                id: data.id,
                                invoice_id: data.invoice_id,
                                paid_amount: data.paid_amount,
                                payment_method_id: data.payment_method_id,
                                transaction_id: data.transaction_id,
                                next_payment_date: data.next_payment_date,
                                notes: data.notes
                            };
                            
                            // Set Current Image URL from Controller Data
                            this.currentImageUrl = data.image_url || null;

                            this.selectedInvoice = this.invoices.find(i => i.id == data.invoice_id) || null;
                            this.selectedMethodDetails = this.methods.find(m => m.id == data.payment_method_id);
                        } else {
                            // Add Mode
                            this.payment = {
                                id: '',
                                invoice_id: '',
                                paid_amount: '',
                                payment_method_id: '',
                                transaction_id: '',
                                next_payment_date: '',
                                notes: ''
                            };
                            this.selectedInvoice = null;
                            this.selectedMethodDetails = null;
                        }
                        this.modalOpen = true;
                    },

                    closeModal() {
                        this.modalOpen = false;
                    },

                    deletePayment(id) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'This payment will be deleted!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it'
                        }).then(result => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: `payments/${id}`,
                                    type: 'DELETE',
                                    success: res => {
                                        this.toast(res.message, 'success');
                                        this.table.ajax.reload(null, false);
                                    },
                                    error: err => this.toast('Error deleting payment', 'error')
                                });
                            }
                        });
                    },

                    savePayment() {
                        if (this.isPartialPayment && !this.payment.next_payment_date) {
                            this.toast('Next payment date is required for partial payments', 'warning');
                            return;
                        }
                        
                        // Check validation only for new payments or if file is mandatory and no existing image
                        if (this.selectedMethodDetails?.image_proof_required && !this.imageFile && !this.payment.id && !this.currentImageUrl) {
                            this.toast('Proof image is required for this method', 'warning');
                            return;
                        }

                        this.isLoading = true;

                        let formData = new FormData();
                        Object.keys(this.payment).forEach(key => {
                            formData.append(key, this.payment[key] || '');
                        });

                        if (this.imageFile) {
                            formData.append('image', this.imageFile);
                        }

                        let url = `{{ route('payments.store') }}`;

                        if (this.payment.id) {
                            url = `payments/${this.payment.id}`;
                            formData.append('_method', 'PUT');
                        }

                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: res => {
                                this.isLoading = false;
                                this.toast(res.message, 'success');
                                this.table.ajax.reload(null, false);
                                this.closeModal();
                            },
                            error: err => {
                                this.isLoading = false;
                                let errorMsg = err.responseJSON?.message || 'Validation error';
                                this.toast(errorMsg, 'error');
                            }
                        });
                    },

                    toast(message, icon = 'success') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: icon,
                            title: message,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                }
            }
        </script>
    </div>
</x-app-layout>