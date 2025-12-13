<x-app-layout>
    <div x-data="leadStatusModal()" x-init="init()" x-cloak
        class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">

        <!-- Table Card -->
        <div class="w-full">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-blue-600"></i>
                        Lead Statuses
                    </h2>
                    <div x-show="alert.show" x-transition class="fixed top-6 right-6 z-50 px-4 py-3 rounded-lg shadow-lg"
                        :class="alert.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
                        x-text="alert.message">
                    </div>

                    <button @click="openModal()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Lead Status
                    </button>
                </div>

                <!-- Table -->
                <div class="p-6 overflow-x-auto">
                    <table id="leadStatusTable" class="w-full table-auto">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Color</th>
                                <th>Status</th>
                                <th>Global</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="modalOpen" x-transition.opacity
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div @click.outside="closeModal()" x-transition
                class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-xl p-6 relative">

                <!-- Close -->
                <button @click="closeModal()"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 transition">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>

                <!-- Header -->
                <div class="text-center border-b pb-3 mb-4">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white" x-text="modalTitle"></h2>
                </div>

                <!-- Form -->
                <form @submit.prevent="saveLeadStatus()" class="space-y-4">
                    <input type="hidden" x-model="leadStatus.id">

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" x-model="leadStatus.name"
                            class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700" required>
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Color</label>
                        <input type="text" x-model="leadStatus.color"
                            class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Status</label>
                            <select x-model="leadStatus.is_active"
                                class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Global</label>
                            <select x-model="leadStatus.is_global"
                                class="w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

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
        function leadStatusModal() {
            return {
                modalOpen: false,
                modalTitle: 'Add Lead Status',

                alert: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                leadStatus: {
                    id: '',
                    name: '',
                    color: '',
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

                    // Init DataTable
                    this.table = $('#leadStatusTable').DataTable({
                        processing: true,
                        serverSide: true,
                        destroy: true,
                        ajax: "{{ route('lead-statuses.index') }}",
                        columns: [{
                                data: 'id',
                                name: 'id'
                            },
                            {
                                data: 'name',
                                name: 'name'
                            },
                            {
                                data: 'color',
                                name: 'color',
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
                                data: 'created_at',
                                name: 'created_at'
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

                    // Edit
                    window.addEventListener('edit-lead', e => {
                        self.openModal(e.detail);
                    });

                    // Delete
                    $('#leadStatusTable').on('click', '.delete-btn', function() {
                        const id = $(this).data('id');

                        if (!confirm('Are you sure?')) return;

                        $.ajax({
                            url: `{{ url('lead-statuses') }}/${id}`,
                            type: 'DELETE',
                            success: res => {
                                self.showAlert(res.message, 'success');
                                self.table.ajax.reload(null, false);
                            },
                            error: () => self.showAlert('Delete failed', 'error')
                        });
                    });
                },

                openModal(lead = null) {
                    this.modalTitle = lead ? 'Edit Lead Status' : 'Add Lead Status';

                    this.leadStatus = lead ?
                        {
                            ...lead,
                            is_active: lead.is_active ? 1 : 0,
                            is_global: lead.is_global ? 1 : 0,
                        } :
                        {
                            id: '',
                            name: '',
                            color: '',
                            is_active: 1,
                            is_global: 0
                        };

                    this.modalOpen = true;
                },


                closeModal() {
                    this.modalOpen = false;
                },

                saveLeadStatus() {
                    const payload = {
                        ...this.leadStatus,
                        is_active: this.leadStatus.is_active == 1,
                        is_global: this.leadStatus.is_global == 1
                    };

                    const url = payload.id ?
                        `{{ url('lead-statuses') }}/${payload.id}` :
                        `{{ route('lead-statuses.store') }}`;

                    const method = payload.id ? 'PUT' : 'POST';

                    $.ajax({
                        url,
                        type: method,
                        data: payload,
                        success: res => {
                            this.showAlert(res.message, 'success');
                            this.table.ajax.reload(null, false);
                            this.closeModal();
                        },
                        error: err => {
                            if (err.responseJSON?.message) {
                                this.showAlert(err.responseJSON.message, 'error');
                            } else {
                                this.showAlert('Validation error', 'error');
                            }
                        }
                    });
                },

                showAlert(message, type = 'success') {
                    this.alert = {
                        show: true,
                        message,
                        type
                    };
                    setTimeout(() => this.alert.show = false, 3000);
                }
            }
        }
    </script>
</x-app-layout>
