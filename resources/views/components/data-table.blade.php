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

<div class="w-full overflow-x-auto relative z-10">
    <table id="{{ $id }}" class="min-w-full border border-gray-200">
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th class="px-4 py-2 text-center font-extrabold"
                        style="border: 0.2px solid #888 !important;">
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

<script>
    $(document).ready(function () {

        /* ----------------------------------------------------
         *  Detect Action Column Index
         * ---------------------------------------------------- */
        const actionColumnIndex = $('#{{ $id }} thead th')
            .toArray()
            .findIndex(th => $(th).text().trim().toLowerCase() === 'action');

        /* ----------------------------------------------------
         *  Prepare Export Buttons
         * ---------------------------------------------------- */
        const exportColumns = actionColumnIndex === -1
            ? ':visible'
            : `:not(:eq(${actionColumnIndex}))`;

        const buttons = [];

        @if ($excel)
            buttons.push({
                extend: "excelHtml5",
                text: "Excel",
                title: "{{ $title }}",
                className: "dt-button",
                exportOptions: { columns: exportColumns },
            });
        @endif

        @if ($print)
            buttons.push({
                extend: "print",
                text: "Print",
                title: "{{ $title }}",
                className: "dt-button",
                exportOptions: { columns: exportColumns },
            });
        @endif

        /* ----------------------------------------------------
         *  Init DataTable
         * ---------------------------------------------------- */
        $('#{{ $id }}').DataTable({
            dom: `
                <"flex flex-col md:flex-row justify-between items-center mb-4"
                    <"md:mb-0 mb-2" l>
                    <"md:mb-0 mb-2" f>
                >
                <"mb-4" B>
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
        });
    });
</script>
