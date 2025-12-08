<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
 <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@latest/dist/turbo.es2017-esm.min.js"></script>
 <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet"><style>
    /* Remove Bootstrap interference */
    table.dataTable.no-footer {
        border-bottom: 1px solid #e5e7eb !important;
    }

    table.dataTable tbody th,
    table.dataTable tbody td {
        border: 0.2px solid rgb(136, 136, 136) !important;

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
    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased" data-user-id="{{ auth()->id() }}">


    <div class="min-h-screen bg-gray-100">
        <x-sidebar />
        <x-navigation-menu />

        <!-- Page Heading -->


        <!-- Page Content -->
        <main class="mt-[60px] z-48">
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts
</body>

</html>
