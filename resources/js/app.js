
import Chart from 'chart.js/auto';
window.Chart = Chart;

// ✅ DataTable init
$(document).ready(function () {

    // Prevent re-init error
    if ($('#users-table').length) {

        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: $('#users-table').data('source'),

            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role.name', orderable: false },
                { data: 'company', name: 'company.company_name', orderable: false },
                { data: 'whatsapp_api_key', name: 'whatsapp_api_key' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

    }
    
});
