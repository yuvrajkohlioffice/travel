<div class="h-screen w-64 bg-white text-gray-800 flex flex-col fixed left-0 top-0 z-50 shadow-lg">

    <!-- Logo -->
    <div class="p-4 text-2xl font-bold border-b border-gray-200 tracking-wide flex items-center space-x-2">
        <i class="fas fa-route text-blue-500"></i>
        <span>TRAVEL</span>
    </div>

    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">

        @php
            $links = [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fas fa-home'],
                ['route' => 'profile.show', 'label' => 'Profile', 'icon' => 'fas fa-user'],
                ['route' => 'companies.index', 'label' => 'Companies', 'icon' => 'fas fa-building'],
                ['route' => 'leads.index', 'label' => 'Leads', 'icon' => 'fas fa-people-group'],
                ['route' => 'packages.index', 'label' => 'Packages', 'icon' => 'fas fa-box-open'],
                ['route' => 'invoices.index', 'label' => 'Invoices', 'icon' => 'fas fa-file-invoice'],
                ['route' => 'users.index', 'label' => 'Users', 'icon' => 'fas fa-users'],
                ['route' => 'pickup-points.index', 'label' => 'Pickup Points', 'icon' => 'fas fa-map-pin'],
                ['route' => 'roles.index', 'label' => 'Roles', 'icon' => 'fas fa-user-shield'],
                ['route' => 'package-categories.index', 'label' => 'Package Categories', 'icon' => 'fas fa-layer-group'],
                ['route' => 'package-types.index', 'label' => 'Package Types', 'icon' => 'fas fa-tags'],
                ['route' => 'cars.index', 'label' => 'Cars / Cabs', 'icon' => 'fas fa-car'],
                ['route' => 'hotels.index', 'label' => 'Hotels', 'icon' => 'fas fa-hotel'],
                ['route' => 'difficulty-types.index', 'label' => 'Difficulty Types', 'icon' => 'fas fa-mountain'],
                ['route' => 'templates.index', 'label' => 'Message Templates', 'icon' => 'fas fa-envelope-open-text'],
            ];
        @endphp

        <!-- Normal Sidebar Links -->
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}"
                class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all
                {{ request()->routeIs(Str::replaceLast('.index', '*', $link['route'])) ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-700 dark:text-gray-200' }}">
                <i class="{{ $link['icon'] }} w-5"></i>
                <span>{{ $link['label'] }}</span>
            </a>
        @endforeach

        <!-- Divider -->
        <div class="border-t border-gray-200 my-3"></div>

        <!-- Action Buttons -->
        <div class="space-y-1">
            <a href="{{ url('/deploy') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-green-50 hover:text-green-600 text-gray-700 transition-all">
                <span>🚀 Run Deploy</span>
            </a>
            <a href="{{ url('/run-npm-build') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-green-50 hover:text-green-600 text-gray-700 transition-all">
                <span>📦 NPM Build</span>
            </a>
            <a href="{{ url('/optimize-app') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-green-50 hover:text-green-600 text-gray-700 transition-all">
                <span>⚡ Optimize</span>
            </a>
            <a href="{{ url('/link-storage') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-green-50 hover:text-green-600 text-gray-700 transition-all">
                <span>🔗 Storage Link</span>
            </a>
        </div>

    </nav>
</div>
