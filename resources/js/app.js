// app.js

// -----------------------------
// Libraries
// -----------------------------
import Chart from 'chart.js/auto';
import Swal from 'sweetalert2';

// Make libraries globally accessible
window.Chart = Chart;
window.Swal = Swal;

// -----------------------------
// SweetAlert Toast Helper
// -----------------------------

// Override default alert with SweetAlert2 toast
window.alert = function(response) {
    let message = '';
    let icon = null; // default: no icon for plain strings

    // If it's an AJAX error object
    if (response && response.responseJSON && response.responseJSON.message) {
        message = response.responseJSON.message;
        icon = 'error';
    }
    // If it's an object with a message property
    else if (response && response.message) {
        message = response.message;
        icon = response.success === false ? 'error' : 'success';
    }
    // If it's a plain string
    else if (typeof response === 'string') {
        message = response;
        icon = 'info'; // neutral icon
    }
    // Default fallback
    else {
        message = 'Operation completed!';
        icon = 'info';
    }

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,           // 'success', 'error', or 'info'
        title: message,
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true
    });
};



// -----------------------------
// DataTable Initialization
// -----------------------------
$(document).ready(function () {

    // Check if the users table exists
    const $usersTable = $('#users-table');
    if ($usersTable.length) {

        $usersTable.DataTable({
            processing: true,
            serverSide: true,
            ajax: $usersTable.data('source'),

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
            ],

            order: [[0, 'asc']],
            responsive: true,
        });

    }

});
