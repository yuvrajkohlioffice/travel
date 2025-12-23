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
                                    <th>Order by</th>

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
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">

                <div x-transition class="bg-white dark:bg-gray-800 w-full max-w-xl rounded-3xl shadow-2xl p-6 relative">

                    <!-- Close -->
                    <button @click="closeModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>

                    <!-- Title -->
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 text-center" x-text="modalTitle">
                    </h2>

                    <!-- Form -->
                    <form @submit.prevent="saveLeadStatus()" class="space-y-4">

                        {{-- Company --}}
                        @if (auth()->user()->role_id == 1)
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Company</label>
                                <select x-model="leadStatus.company_id" :disabled="leadStatus.is_global == 1"
                                    class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                                    <option value="">Select company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                                <p x-show="leadStatus.is_global == 1" class="text-xs text-gray-400 mt-1">
                                    Global status is not linked to a company
                                </p>
                            </div>
                        @endif

                        {{-- Name --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Status Name</label>
                            <input x-model="leadStatus.name"
                                class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700"
                                placeholder="e.g. New, Follow-up, Closed">
                        </div>

                        {{-- Color with preview --}}
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Color (Tailwind)</label>
                            <div class="flex items-center gap-3 mt-1">
                                <input x-model="leadStatus.color"
                                    class="flex-1 p-3 rounded-xl border bg-gray-50 dark:bg-gray-700"
                                    placeholder="bg-green-600">
                                <span class="px-4 py-2 rounded-xl text-white"
                                    :class="leadStatus.color || 'bg-gray-300'">
                                    Preview
                                </span>
                            </div>
                        </div>

                        {{-- Toggles --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Active</label>
                                <select x-model="leadStatus.is_active"
                                    class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-gray-600">Global</label>
                                <select x-model="leadStatus.is_global"
                                    class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Order</label>
                                <input type="number" x-model="leadStatus.order_by"
                                    class="mt-1 w-full p-3 rounded-xl border bg-gray-50 dark:bg-gray-700"
                                    placeholder="0">
                            </div>

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
            function leadStatusModal() {
                return {
                    modalOpen: false,
                    modalTitle: 'Add Lead Status',

                    leadStatus: {
                        id: '',
                        company_id: '',
                        name: '',
                        color: '',
                        order_by: 0,
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
                                    data: 'order_by',
                                    name: 'order_by',
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
                            id: lead.id,
                            company_id: lead.company_id ?? '',
                            name: lead.name,
                            color: lead.color,
                            order_by: lead.order_by ?? 0,
                            is_active: lead.is_active ? 1 : 0,
                            is_global: lead.is_global ? 1 : 0,
                        } : {
                            id: '',
                            company_id: '',
                            name: '',
                            color: '',
                            order_by: 0,
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
                            company_id: this.leadStatus.is_global == 1 ? null : this.leadStatus.company_id,
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
                                this.toast(err.responseJSON?.message || 'Validation error', 'error');
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
