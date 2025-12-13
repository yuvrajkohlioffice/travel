<x-app-layout>
    <div x-data="followupReasonModal()" x-cloak class="min-h-screen">

        <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
            <div class="w-full">

                <!-- Header -->
                <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-sm">
                            <i class="fa-solid fa-notes-medical text-xl text-gray-700 dark:text-gray-200"></i>
                        </div>
                        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Followup Reasons</h1>
                    </div>
                    <button @click="openModal()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Followup Reason
                    </button>
                </header>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="p-3 bg-green-600 text-white rounded shadow-sm mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Followup Reasons Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm overflow-x-auto">
                    <table id="followupReasonTable" class="min-w-full border-gray-200 dark:border-gray-700 text-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Company</th>
                                <th>Name</th>
                                <th>Remark</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Email Template</th>
                                <th>WhatsApp Template</th>
                                <th>Active</th>
                                <th>Global</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <!-- Modal -->
            <div x-show="modalOpen" x-transition.opacity
                class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">

                <div @click.outside="closeModal()" x-transition
                    class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-xl relative
                max-h-[80vh] overflow-y-auto p-6">



                    <!-- Close Button -->
                    <button @click="closeModal()"
                        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 transition">
                        <i class="fa-solid fa-xmark text-2xl"></i>
                    </button>

                    <!-- Modal Title -->
                    <div class="text-center border-b pb-3 mb-4">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white" x-text="modalTitle"></h2>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="saveFollowupReason()" class="space-y-4">

                        <input type="hidden" x-model="followupReason.id">

                        <!-- Company -->
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Company</label>
                            <select x-model="followupReason.company_id"
                                :disabled="{{ $user->role_id != 1 ? 'true' : 'false' }}"
                                class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Name -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" x-model="followupReason.name"
                                    class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700" required>
                            </div>

                            <!-- Remark -->
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Remark (Yes/No)</label>
                                <select x-model="followupReason.remark"
                                    class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Date Required</label>
                                <select x-model="followupReason.date"
                                    class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Time Required</label>
                                <select x-model="followupReason.time"
                                    class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <!-- Date & Time -->
                        <div class="grid grid-cols-2 gap-4">
                            
                        </div>

                        <!-- Templates -->
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Email Template</label>
                            <textarea x-model="followupReason.email_template" class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700"></textarea>
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">WhatsApp Template</label>
                            <textarea x-model="followupReason.whatsapp_template" class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700"></textarea>
                        </div>

                        <!-- Active & Global -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Active</label>
                                <select x-model="followupReason.is_active"
                                    class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Global</label>
                                <select x-model="followupReason.is_global"
                                    class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" @click="closeModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-xl">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl">Save</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <script>
            function followupReasonModal() {
                return {
                    modalOpen: false,
                    modalTitle: 'Add Followup Reason',
                    followupReason: {
                        id: '',
                        company_id: '',
                        name: '',
                        remark: 0,
                        date: 0,
                        time: 0,
                        email_template: '',
                        whatsapp_template: '',
                        is_active: 1,
                        is_global: 0
                    },
                    table: null,

                    init() {
                        const self = this;
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        // Destroy existing DataTable if already initialized
                        if ($.fn.DataTable.isDataTable('#followupReasonTable')) {
                            $('#followupReasonTable').DataTable().destroy();
                        }

                        // Initialize DataTable
                        this.table = $('#followupReasonTable').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: "{{ route('followup-reasons.index') }}",
                            columns: [{
                                    data: 'id',
                                    name: 'id'
                                },
                                {
                                    data: 'company',
                                    name: 'company'
                                },
                                {
                                    data: 'name',
                                    name: 'name'
                                },
                                {
                                    data: 'remark',
                                    name: 'remark',
                                    searchable: false
                                },
                                {
                                    data: 'date',
                                    name: 'date',
                                    searchable: false
                                },
                                {
                                    data: 'time',
                                    name: 'time',
                                    searchable: false
                                },
                                {
                                    data: 'email_template',
                                    name: 'email_template',
                                    orderable: false
                                },
                                {
                                    data: 'whatsapp_template',
                                    name: 'whatsapp_template',
                                    orderable: false
                                },
                                {
                                    data: 'is_active',
                                    name: 'is_active',
                                    searchable: false
                                },
                                {
                                    data: 'is_global',
                                    name: 'is_global',
                                    searchable: false
                                },
                                {
                                    data: 'action',
                                    name: 'action',
                                    orderable: false,
                                    searchable: false
                                }
                            ],
                            order: [
                                [0, 'asc']
                            ]
                        });

                        // Edit listener
                        window.addEventListener('edit-followup', e => self.openModal(e.detail));

                        // Delete listener
                        $('#followupReasonTable').on('click', '.delete-btn', function() {
                            const id = $(this).data('id');
                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'This action cannot be undone!',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, delete it!',
                                cancelButtonText: 'Cancel'
                            }).then(result => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: `{{ url('followup-reasons') }}/${id}`,
                                        type: 'DELETE',
                                        success: res => {
                                            self.toast(res.message, 'success');
                                            self.table.ajax.reload(null, false);
                                        },
                                        error: () => self.toast('Delete failed', 'error')
                                    });
                                }
                            });
                        });
                    },

                    openModal(reason = null) {
                        this.modalTitle = reason ? 'Edit Followup Reason' : 'Add Followup Reason';
                        this.followupReason = reason ? {
                            id: reason.id,
                            company_id: reason.company_id,
                            name: reason.name,
                            remark: reason.remark ? 1 : 0,
                            date: reason.date ? 1 : 0,
                            time: reason.time ? 1 : 0,
                            email_template: reason.email_template,
                            whatsapp_template: reason.whatsapp_template,
                            is_active: reason.is_active ? 1 : 0,
                            is_global: reason.is_global ? 1 : 0
                        } : {
                            ...this.followupReason,
                            id: ''
                        };
                        this.modalOpen = true;
                    },

                    closeModal() {
                        this.modalOpen = false;
                    },

                    saveFollowupReason() {
                        // Convert selects (0/1) into booleans for Laravel validation
                        const payload = {
                            ...this.followupReason,
                            remark: this.followupReason.remark == 1 || this.followupReason.remark === true,
                            date: this.followupReason.date == 1 || this.followupReason.date === true,
                            time: this.followupReason.time == 1 || this.followupReason.time === true,
                            is_active: this.followupReason.is_active == 1 || this.followupReason.is_active === true,
                            is_global: this.followupReason.is_global == 1 || this.followupReason.is_global === true
                        };


                        if (payload.is_global) payload.company_id = null;

                        const url = payload.id ?
                            `{{ url('followup-reasons') }}/${payload.id}` :
                            `{{ route('followup-reasons.store') }}`;
                        const method = payload.id ? 'PUT' : 'POST';

                        $.ajax({
                            url,
                            type: method,
                            data: payload,
                            success: res => {
                                this.toast(res.message, 'success');
                                this.table.ajax.reload(null, false);
                                this.closeModal();
                            },
                            error: err => {
                                // Show detailed validation errors if any
                                if (err.responseJSON?.errors) {
                                    const errors = Object.values(err.responseJSON.errors).flat().join('<br>');
                                    this.toast(errors, 'error');
                                } else {
                                    this.toast(err.responseJSON?.message || 'Validation error', 'error');
                                }
                            }
                        });
                    },

                    toast(message, icon = 'success') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon,
                            html: message,
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                    }
                }
            }
        </script>

</x-app-layout>
