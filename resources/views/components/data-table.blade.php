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
            <table id="{{ $id }}" class="table table-striped table-bordered dark:bg-gray-800 dark:text-gray-200" style="width:100%">
                <thead class="bg-green-500 dark:bg-gray-800">
                    <tr>
                        @foreach ($headers as $header)
                            <th class="text-center align-middle text-white dark:text-gray-200">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="dark:bg-gray-800 dark:text-white">
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
</div>

<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">


<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script>
    $(document).ready(function() {
        let actionColumnIndex = -1;
        $('#{{ $id }} thead th').each(function(index) {
            if ($(this).text().trim().toLowerCase() === 'action') {
                actionColumnIndex = index;
            }
        });

        var buttons = [];




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