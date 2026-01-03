<x-app-layout>
    <div x-data="{
        table: null,
        type: 'today',
        startDate: '',
        endDate: '',
    
        init() {
            this.initTable();
        },
    
        initTable() {
            if (this.table) return; // Prevent re-initialization
    
            this.table = $('#followupReportTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('followup.report.data') }}',
                    data: (d) => {
                        d.type = this.type;
                        d.start_date = this.startDate;
                        d.end_date = this.endDate;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'user', name: 'user' },
                    { data: 'total_calls', name: 'total_calls' },
                    { data: 'reason_counts', name: 'reason_counts' },
                ]
            });
        },
    
        setType(value) {
            this.type = value;
            this.startDate = '';
            this.endDate = '';
            if (this.table) this.table.ajax.reload();
        },
    
        reloadTable() {
            if (this.table) this.table.ajax.reload();
        }
    }" x-init="init()" x-cloak class="min-h-screen">

        <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
            <div class="w-full">

                <!-- Header -->
                <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl shadow-sm">
                            <i class="fas fa-chart-line text-xl text-gray-700 dark:text-gray-200"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                                Follow-up Report
                            </h1>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex gap-2 items-center">
                        <button @click="setType('today')" :class="type == 'today' ? 'bg-blue-600 text-white' : 'bg-gray-200'"
                            class="px-4 py-2 rounded-lg">Today</button>
                        <button @click="setType('yesterday')"
                            :class="type == 'yesterday' ? 'bg-blue-600 text-white' : 'bg-gray-200'"
                            class="px-4 py-2 rounded-lg">Yesterday</button>
                        <button @click="setType('yearly')"
                            :class="type == 'yearly' ? 'bg-blue-600 text-white' : 'bg-gray-200'"
                            class="px-4 py-2 rounded-lg">Yearly</button>

                        <input type="date" x-model="startDate" class="px-3 py-2 border rounded-lg">
                        <input type="date" x-model="endDate" class="px-3 py-2 border rounded-lg">
                        <button @click="reloadTable()"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg">Filter</button>
                    </div>
                </header>

                <!-- Table -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <table id="followupReportTable" class="min-w-full text-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Total Calls</th>
                                <th>Reason Counts</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
    <!-- Modal -->
<div id="leadModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-3xl rounded-xl p-6 relative">
        <button id="closeModal" class="absolute top-3 right-3">✖</button>
        <h2 class="text-xl font-bold mb-4" id="modalTitle">Leads</h2>
        <table id="leadsTable" class="table-auto w-full border">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lead Name</th>
                    <th>Contact</th>
                    <th>Reason</th>
                    <th>Remark</th>
                    <th>Next Follow-up</th>
                    <th>Created At</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
$(document).on('click', '.show-leads', function(e){
    e.preventDefault();
    let userId = $(this).data('user-id');
    let userName = $(this).data('user');
    $('#modalTitle').text(`Leads for ${userName}`);

    $('#leadModal').removeClass('hidden');

    // Initialize DataTable
    if ($.fn.DataTable.isDataTable('#leadsTable')) {
        $('#leadsTable').DataTable().destroy();
    }

    $('#leadsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("followup_report.leads") }}',
            data: { user_id: userId }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'lead_name', name: 'lead_name' },
            { data: 'lead_contact', name: 'lead_contact' },
            { data: 'reason', name: 'reason' },
            { data: 'remark', name: 'remark' },
            { data: 'next_followup', name: 'next_followup' },
            { data: 'created_at', name: 'created_at' }
        ]
    });
});

$('#closeModal').click(function(){
    $('#leadModal').addClass('hidden');
});
</script>

</x-app-layout>
