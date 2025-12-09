@props([
    'id' => 'data-table',
    'headers' => [],
    'rows' => [],
    'excel' => true,
    'print' => true,
    'pageLength' => 10,
    'lengthMenu' => [5, 10, 25, 50, -1],
    'lengthMenuLabels' => ['5', '10', '25', '50', 'All'],
    'title' => 'Data Export',
    'searchPlaceholder' => 'Search...',
    'resourceName' => 'entries',
])

<div class="row">
    <div class="col-12">
        <div class="table-responsive">
            <table id="{{ $id }}" class="min-w-full divide-y divide-gray-100 text-sm" style="width:100%">
                <thead class="table-success bg-gray-50">
                    <tr>
                        @foreach ($headers as $header)
                            <th class="px-4 py-3 text-left font-medium text-xs text-gray-600 uppercase tracking-wider">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- DataTables JS -->
<script>
    $(document).ready(function() {
        let actionColumnIndex = -1;
        $('#{{ $id }} thead th').each(function(index) {
            if ($(this).text().trim().toLowerCase() === 'action') {
                actionColumnIndex = index;
            }
        });

        var buttons = [];

        @if ($excel)
            buttons.push({
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel me-1"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: '{{ $title }}',
                exportOptions: {
                    columns: actionColumnIndex === -1 ? ':visible' : ':not(:eq(' + actionColumnIndex +
                        '))'
                },
                customize: function(xlsx) {
                    // Access worksheet
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];

                    // Change all header (first row) font color to black
                    $('row:first c', sheet).attr('s', '2'); // Style index 2 usually is black text

                    // Alternatively, to ensure black color:
                    $('row:first c', sheet).each(function() {
                        var cell = $(this);
                        var style = cell.attr('s');
                        if (!style) style = '2';
                        cell.attr('s', style);
                    });
                }
            });
        @endif

        @if ($print)
            buttons.push({
                extend: 'print',
                text: '<i class="fas fa-print me-1"></i> Print',
                className: 'btn btn-primary btn-sm',
                title: '{{ $title }}',
                exportOptions: {
                    columns: actionColumnIndex === -1 ? ':visible' : ':not(:eq(' + actionColumnIndex +
                        '))'
                },
                customize: function(win) {
                    $(win.document.body)
                        .find('table thead th')
                        .css('color', 'black')
                        .css('background-color', '#ffffff')
                        .find('table thead td')
                        .css('color', 'black')
                        .css('background-color', '#ffffff')
                }

            });
        @endif


        $('#{{ $id }}').DataTable({
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"B>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: buttons,
            responsive: true,
            order: [],
            pageLength: {{ $pageLength }},
            lengthMenu: [@json($lengthMenu), @json($lengthMenuLabels)],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "{{ $searchPlaceholder }}",
                lengthMenu: "Show _MENU_ {{ $resourceName }}",
                info: "Showing _START_ to _END_ of _TOTAL_ {{ $resourceName }}",
                infoEmpty: "No {{ $resourceName }} available",
                paginate: {
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>'
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: actionColumnIndex,
                    className: 'text-center'
                },
                {
                    targets: '_all',
                    className: 'align-middle'
                }
            ],
            initComplete: function() {
                $('.btn').removeClass('btn-secondary dt-button');
                $('.dataTables_filter input').addClass('form-control form-control-sm');
                $('.dataTables_length select').addClass('form-control form-control-sm');
            }
        });
    });
</script>