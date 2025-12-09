<div class="h-screen w-64 bg-white text-gray-800 flex flex-col fixed left-0 top-0 z-50">

    <!-- Logo -->
    <div class="p-4 text-2xl font-bold border-b border-gray-200 tracking-wide flex items-center space-x-2">
        <i class="fas fa-route text-blue-500"></i>
        <span>TRAVEL</span>
    </div>

    <nav class="flex-1 p-4 space-y-2 shadow-lg ">

        @php
            $links = [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fas fa-home'],
                ['route' => 'profile.show', 'label' => 'Profile', 'icon' => 'fas fa-user'],

                ['route' => 'leads.index', 'label' => 'Leads', 'icon' => 'fas fa-people-group'],
                ['route' => 'packages.index', 'label' => 'Packages', 'icon' => 'fas fa-box-open'],

                ['route' => 'invoices.index', 'label' => 'Invoices', 'icon' => 'fas fa-file-invoice'], // 🔥 Missing one added

                ['route' => 'users.index', 'label' => 'Users', 'icon' => 'fas fa-users'],
                ['route' => 'pickup-points.index', 'label' => 'Pickup Points', 'icon' => 'fas fa-map-pin'],
                ['route' => 'roles.index', 'label' => 'Roles', 'icon' => 'fas fa-user-shield'],
                [
                    'route' => 'package-categories.index',
                    'label' => 'Package Categories',
                    'icon' => 'fas fa-layer-group',
                ],
                ['route' => 'package-types.index', 'label' => 'Package Types', 'icon' => 'fas fa-tags'],
                ['route' => 'cars.index', 'label' => 'Cars / Cabs', 'icon' => 'fas fa-car'],
                ['route' => 'hotels.index', 'label' => 'Hotels', 'icon' => 'fas fa-hotel'],
                ['route' => 'difficulty-types.index', 'label' => 'Difficulty Types', 'icon' => 'fas fa-mountain'],
            ];
        @endphp


        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}"
                class="sidebar-link {{ request()->routeIs(Str::replaceLast('.index', '*', $link['route'])) ? 'active' : '' }}">
                <i class="{{ $link['icon'] }} w-5"></i>
                <span>{{ $link['label'] }}</span>
            </a>
        @endforeach


    </nav>
</div>
