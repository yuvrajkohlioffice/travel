<!-- Table -->

<x-app-layout>
    <div x-data="leadStatusModal()" x-init="init()" x-cloak class="min-h-screen ">

        <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
            <div class="w-full">

                <!-- Header -->
                <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-sm">
                            <i class="fa-solid fas fa-tags text-xl text-gray-700 dark:text-gray-200"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Lead Statuses
                            </h1>

                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Import template link --}}
                        <button @click="openModal()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                            <i class="fas fa-plus"></i> Add Lead Status
                        </button>
                    </div>
                </header>

                <!-- Success -->
                @if (session('success'))
                    <div class="p-3 bg-green-600 text-white rounded shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Bulk Assign bar (visible when selections exist) -->



                <!-- Filters & Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg  p-4 shadow-sm overflow-x-auto">


                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table id="leadStatusTable" class="min-w-full  border-gray-200 dark:border-gray-700 text-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Color</th>
                                    <th>Status</th>
                                    <th>Global</th>

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
                            <label class="block font-medium text-gray-700 mb-1">
                                Color (
                                <a href="https://tailwindcss.com/docs/colors" target="_blank" rel="noopener noreferrer"
                                    class="text-blue-500 hover:underline">
                                    Click here
                                </a>
                                )
                            </label>
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

                        // Delete with SweetAlert
                        $('#leadStatusTable').on('click', '.delete-btn', function() {
                            const id = $(this).data('id');

                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'This action cannot be undone!',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, delete it!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: `{{ url('lead-statuses') }}/${id}`,
                                        type: 'DELETE',
                                        success: res => {
                                            self.toast(res.message, 'success');
                                            self.table.ajax.reload(null, false);
                                        },
                                        error: () => {
                                            self.toast('Delete failed', 'error');
                                        }
                                    });
                                }
                            });
                        });
                    },

                    openModal(lead = null) {
                        this.modalTitle = lead ? 'Edit Lead Status' : 'Add Lead Status';

                        this.leadStatus = lead ? {
                            ...lead,
                            is_active: lead.is_active ? 1 : 0,
                            is_global: lead.is_global ? 1 : 0,
                        } : {
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
                                this.toast(res.message, 'success');
                                this.table.ajax.reload(null, false);
                                this.closeModal();
                            },
                            error: err => {
                                this.toast(
                                    err.responseJSON?.message || 'Validation error',
                                    'error'
                                );
                            }
                        });
                    },

                    // ✅ SweetAlert Toast (Global)
                    toast(message, icon = 'success') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: icon,
                            title: message,
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                    }
                }
            }
        </script>

</x-app-layout>
