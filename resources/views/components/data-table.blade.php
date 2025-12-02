@props([
    'id' => 'data-table',
    'headers' => [],
    'excel' => true,
    'print' => true,
    'pageLength' => 10,
    'lengthMenu' => [5, 10, 25, 50, -1],
    'lengthMenuLabels' => ['5', '10', '25', '50', 'All'],
    'title' => 'Data Export',
    'searchPlaceholder' => 'Search...',
    'resourceName' => 'entries',
])

<div class="w-full overflow-x-auto  ">
    <table id="{{ $id }}" class="min-w-full border border-gray-200">
        <thead class="bg-blue-600 text-white">
            <tr>
                @foreach ($headers as $header)
                    <th class="px-4 py-2 text-center font-semibold border border-white-700">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>

{{-- DATA TABLES CSS --}}
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet">

<style>
    
    /* Remove Bootstrap interference */
    table.dataTable.no-footer {
        border-bottom: 1px solid #e5e7eb !important;
    }

    /* Tailwind Pagination Buttons */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px !important;
        margin: 2px !important;
        border-radius: 6px !important;
        background: #f3f4f6 !important;
        border: 1px solid #d1d5db !important;
        color: #111827 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #22c55e !important;
        color: #fff !important;
        border: 1px solid #15803d !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #e2e8f0 !important;
        color: #000 !important;
    }

    /* Search Input */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d5db !important;
        border-radius: 6px !important;
        padding: 6px 10px !important;
        outline: none !important;
    }

    /* Length Dropdown */
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #d1d5db !important;
        border-radius: 6px !important;
        padding: 5px 32px 5px 10px !important;
    }

    /* Export Buttons Tailwind */
    .dt-buttons .dt-button {
        background-color: #22c55e !important;
        color: white !important;
        border-radius: 6px !important;
        padding: 6px 12px !important;
        margin-right: 6px !important;
        border: none !important;
    }

    .dt-buttons .dt-button:hover {
        background-color: #16a34a !important;
        color: white !important;
    }
</style>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DATATABLES -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function () {
    let actionColumnIndex = -1;

    $('#{{ $id }} thead th').each(function (index) {
        if ($(this).text().trim().toLowerCase() === 'action') {
            actionColumnIndex = index;
        }
    });

    const buttons = [];

    @if ($excel)
    buttons.push({
        extend: "excelHtml5",
        text: "Excel",
        className: "dt-button",
        title: "{{ $title }}",
        exportOptions: {
            columns: actionColumnIndex === -1 ? ':visible' : ':not(:eq(' + actionColumnIndex + '))'
        }
    });
    @endif

    @if ($print)
    buttons.push({
        extend: "print",
        text: "Print",
        className: "dt-button",
        title: "{{ $title }}",
        exportOptions: {
            columns: actionColumnIndex === -1 ? ':visible' : ':not(:eq(' + actionColumnIndex + '))'
        }
    });
    @endif

    $('#{{ $id }}').DataTable({
        dom: `
            <"flex flex-col md:flex-row justify-between items-center mb-4"
                <"mb-2 md:mb-0"l>
                <"mb-2 md:mb-0"f>
            >
            <"mb-4" B>
            <"w-full" tr>
            <"flex flex-col md:flex-row justify-between items-center mt-4"
                <"mb-2 md:mb-0" i>
                <"mb-2 md:mb-0" p>
            >
        `,
        buttons: buttons,
        responsive: true,
        pageLength: {{ $pageLength }},
        lengthMenu: [@json($lengthMenu), @json($lengthMenuLabels)],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "{{ $searchPlaceholder }}",
            lengthMenu: "Show _MENU_ {{ $resourceName }}",
        },
        columnDefs: [
            {
                targets: actionColumnIndex,
                orderable: false,
                className: 'text-center'
            }
        ],
    });

});
</script>
