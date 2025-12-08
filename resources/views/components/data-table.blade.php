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

<div class="w-full overflow-x-auto  relative z-10">
    <table id="{{ $id }}" class="min-w-full border border-gray-200">
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th class="px-4 py-2 text-center font-extrabold" style="border: 0.2px solid rgb(136, 136, 136) !important;">
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





<script>
    $(document).ready(function() {
        let actionColumnIndex = -1;

        $('#{{ $id }} thead th').each(function(index) {
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
                    columns: actionColumnIndex === -1 ? ':visible' : ':not(:eq(' + actionColumnIndex +
                        '))'
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
                    columns: actionColumnIndex === -1 ? ':visible' : ':not(:eq(' + actionColumnIndex +
                        '))'
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
            columnDefs: [{
                targets: actionColumnIndex,
                orderable: false,
                className: 'text-center'
            }],
        });

    });
</script>
