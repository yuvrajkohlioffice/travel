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

<div class="w-full overflow-x-auto relative z-10  rounded-lg">
    <!-- Table Header -->
    <table id="{{ $id }}" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                @foreach ($headers as $header)
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class=" divide-y divide-gray-200 dark:divide-gray-700">
            {{ $slot }}
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {

        /* ----------------------------------------------------
         *  Detect Action Column Index
         * ---------------------------------------------------- */
        const actionColumnIndex = $('#{{ $id }} thead th')
            .toArray()
            .findIndex(th => $(th).text().trim().toLowerCase() === 'action');

        /* ----------------------------------------------------
         *  Prepare Export Buttons with FontAwesome Icons
         * ---------------------------------------------------- */
        const exportColumns = actionColumnIndex === -1
            ? ':visible'
            : `:not(:eq(${actionColumnIndex}))`;

        const buttons = [];

        @if ($excel)
            buttons.push({
                extend: "excelHtml5",
                text: '<i class="fas fa-file-excel mr-2"></i> Excel',
                title: "{{ $title }}",
                className: "dt-button bg-green-600 hover:bg-green-700 text-white rounded shadow px-3 py-1",
                exportOptions: { columns: exportColumns },
            });
        @endif

        @if ($print)
            buttons.push({
                extend: "print",
                text: '<i class="fas fa-print mr-2"></i> Print',
                title: "{{ $title }}",
                className: "dt-button bg-gray-600 hover:bg-gray-700 text-white rounded shadow px-3 py-1",
                exportOptions: { columns: exportColumns },
            });
        @endif

        /* ----------------------------------------------------
         *  Init DataTable with Tailwind Styling
         * ---------------------------------------------------- */
        $('#{{ $id }}').DataTable({
            dom: `
                <"flex flex-col md:flex-row justify-between items-center mb-4"
                    <"md:mb-0 mb-2" l>
                    <"md:mb-0 mb-2" f>
                >
                <"mb-4 flex space-x-2"B>
                <"w-full" tr>
                <"flex flex-col md:flex-row justify-between items-center mt-4"
                    <"md:mb-0 mb-2" i>
                    <"md:mb-0 mb-2" p>
                >
            `,
            buttons,
            responsive: true,
            pageLength: {{ $pageLength }},
            lengthMenu: [@json($lengthMenu), @json($lengthMenuLabels)],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "{{ $searchPlaceholder }}",
                lengthMenu: "Show _MENU_ {{ $resourceName }}",
            },
            columnDefs: actionColumnIndex !== -1 ? [{
                targets: actionColumnIndex,
                orderable: false,
                className: "text-center",
            }] : [],
            drawCallback: function(settings) {
                // Apply Tailwind classes to pagination
                $('.dataTables_paginate .paginate_button').addClass('px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors');
            }
        });

        // Style the search input
        $(`#{{ $id }}_filter input`).addClass('mt-1 block w-full md:w-64 rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90');
    });
</script>
