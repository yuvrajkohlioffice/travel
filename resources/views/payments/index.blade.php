<x-app-layout>
    <div x-data="paymentModal()"  x-cloak class="min-h-screen">

        <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
            <div class="w-full">

                <!-- Header -->
                <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-sm">
                            <i class="fas fa-credit-card text-xl text-gray-700 dark:text-gray-200"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                                Payments
                            </h1>
                        </div>
                    </div>


                </header>

                <!-- Table Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm overflow-x-auto">
                    <table id="paymentTable" class="min-w-full text-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invoice</th>
                                <th>Paid</th>
                                <th>Remaining</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <!-- Modal -->
            <div x-show="modalOpen" x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">

                <div x-transition class="bg-white dark:bg-gray-800 w-full max-w-xl rounded-3xl shadow-2xl p-6 relative">

                    <!-- Close -->
                    <button @click="closeModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>

                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 text-center"
                        x-text="modalTitle"></h2>

                    <form @submit.prevent="savePayment()" class="space-y-4">

                        <!-- Invoice -->
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Invoice</label>
                            <select x-model="payment.invoice_id" :disabled="payment.id"
                                class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700 disabled:bg-gray-200">
                                <option value="">Select Invoice</option>
                                @foreach ($invoices as $inv)
                                    <option value="{{ $inv->id }}">{{ $inv->invoice_no }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Paid Amount -->
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Paid Amount</label>
                            <input type="number" x-model="payment.paid_amount"
                                class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700"
                                placeholder="Enter paid amount">
                        </div>

                        <!-- Method -->
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Payment Method</label>
                            <input x-model="payment.payment_method"
                                class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700"
                                placeholder="Cash / UPI / Bank">
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Notes</label>
                            <textarea x-model="payment.notes"
                                class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700"
                                placeholder="Optional notes"></textarea>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" @click="closeModal()"
                                class="px-5 py-2 rounded-xl bg-gray-200 hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow">
                                Save
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>

        <script>
            function paymentModal() {
                return {
                    modalOpen: false,
                    modalTitle: 'Add Payment',
                    table: null,

                    payment: {
                        id: '',
                        invoice_id: '',
                        paid_amount: '',
                        payment_method: '',
                        notes: ''
                    },

                    init() {
                        const self = this;

                        $.ajaxSetup({
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });

                        this.table = $('#paymentTable').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: "{{ route('payments.index') }}",
                            columns: [
                                { data: 'id' },
                                { data: 'invoice_no' },
                                { data: 'paid_amount' },
                                { data: 'remaining_amount' },
                                { data: 'status' },
                                { data: 'action', orderable: false, searchable: false }
                            ]
                        });

                        window.addEventListener('edit-payment', e => {
                            self.openModal(e.detail);
                        });

                        $('#paymentTable').on('click', '.delete-btn', function () {
                            const id = $(this).data('id');

                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'This payment will be deleted!',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, delete it'
                            }).then(result => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: `payments/${id}`,
                                        type: 'DELETE',
                                        success: res => {
                                            self.toast(res.message, 'success');
                                            self.table.ajax.reload(null, false);
                                        }
                                    });
                                }
                            });
                        });
                    },

                    openModal(data = null) {
                        this.modalTitle = data ? 'Edit Payment' : 'Add Payment';
                        this.payment = data ? { ...data } : {
                            id: '',
                            invoice_id: '',
                            paid_amount: '',
                            payment_method: '',
                            notes: ''
                        };
                        this.modalOpen = true;
                    },

                    closeModal() {
                        this.modalOpen = false;
                    },

                    savePayment() {
                        const url = this.payment.id
                            ? `payments/${this.payment.id}`
                            : `payments`;

                        const method = this.payment.id ? 'PUT' : 'POST';

                        $.ajax({
                            url,
                            type: method,
                            data: this.payment,
                            success: res => {
                                this.toast(res.message, 'success');
                                this.table.ajax.reload(null, false);
                                this.closeModal();
                            },
                            error: err => {
                                this.toast('Validation error', 'error');
                            }
                        });
                    },

                    toast(message, icon = 'success') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon,
                            title: message,
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                }
            }
        </script>
    </div>
</x-app-layout>
