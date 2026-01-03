<div
    class="h-screen w-64 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 flex flex-col fixed left-0 top-0 z-50 shadow-lg">

    <!-- Logo -->
    <div
        class="p-4 text-2xl font-bold border-b border-gray-200 dark:border-gray-700 tracking-wide flex items-center space-x-2">
        <i class="fas fa-route text-blue-500"></i>
        <span>TRAVEL</span>
    </div>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        @php
            $links = [
                /* =====================
       DASHBOARD
    ====================== */
                [
                    'route' => 'dashboard',
                    'label' => 'Dashboard',
                    'icon' => 'fas fa-chart-line',
                ],

                /* =====================
       USER & PROFILE
    ====================== */
                [
                    'route' => 'profile.show',
                    'label' => 'My Profile',
                    'icon' => 'fas fa-user-circle',
                ],

                [
                    'route' => 'users.index',
                    'label' => 'Users',
                    'icon' => 'fas fa-users',
                ],

                [
                    'route' => 'roles.index',
                    'label' => 'Roles & Permissions',
                    'icon' => 'fas fa-user-shield',
                ],

                /* =====================
       CRM / LEADS
    ====================== */
                [
                    'route' => 'companies.index',
                    'label' => 'Companies',
                    'icon' => 'fas fa-building',
                ],

                [
                    'route' => 'leads.index',
                    'label' => 'Leads',
                    'icon' => 'fas fa-user-check',
                ],

                [
                    'route' => 'lead-statuses.index',
                    'label' => 'Lead Statuses',
                    'icon' => 'fas fa-tags',
                ],

                [
                    'route' => 'followup-reasons.index',
                    'label' => 'Follow-up Reasons',
                    'icon' => 'fas fa-phone-alt',
                ],

                /* =====================
       PACKAGES & SERVICES
    ====================== */
                [
                    'route' => 'packages.index',
                    'label' => 'Packages',
                    'icon' => 'fas fa-suitcase-rolling',
                ],

                [
                    'route' => 'package-categories.index',
                    'label' => 'Package Categories',
                    'icon' => 'fas fa-layer-group',
                ],

                [
                    'route' => 'package-types.index',
                    'label' => 'Package Types',
                    'icon' => 'fas fa-list-ul',
                ],

                [
                    'route' => 'difficulty-types.index',
                    'label' => 'Difficulty Levels',
                    'icon' => 'fas fa-mountain',
                ],

                /* =====================
       LOGISTICS
    ====================== */
                [
                    'route' => 'cars.index',
                    'label' => 'Cars / Cabs',
                    'icon' => 'fas fa-car-side',
                ],

                [
                    'route' => 'hotels.index',
                    'label' => 'Hotels',
                    'icon' => 'fas fa-hotel',
                ],

                [
                    'route' => 'pickup-points.index',
                    'label' => 'Pickup Points',
                    'icon' => 'fas fa-map-marker-alt',
                ],

                /* =====================
       BILLING & PAYMENTS
    ====================== */
                [
                    'route' => 'invoices.index',
                    'label' => 'Invoices',
                    'icon' => 'fas fa-file-invoice-dollar',
                ],

                [
                    'route' => 'payments.index',
                    'label' => 'Payments',
                    'icon' => 'fas fa-credit-card',
                ],

                [
                    'route' => 'payment-methods.index',
                    'label' => 'Payment Methods',
                    'icon' => 'fas fa-university',
                ],
                [
                    'route' => 'followup.report',
                    'label' => 'Follow-up Report',
                    'icon' => 'fas fa-chart-line',
                ],

                /* =====================
       COMMUNICATION
    ====================== */
                [
                    'route' => 'templates.index',
                    'label' => 'Message Templates',
                    'icon' => 'fas fa-comment-dots',
                ],
            ];
        @endphp



        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}"
                class="flex items-center space-x-3 px-3 py-2 rounded-lg
                transition-all
                {{ request()->routeIs(Str::replaceLast('.index', '*', $link['route']))
                    ? 'bg-blue-50 text-blue-600 font-semibold dark:bg-blue-900 dark:text-blue-400'
                    : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-800 dark:hover:text-blue-400' }}">
                <i class="{{ $link['icon'] }} w-5"></i>
                <span>{{ $link['label'] }}</span>
            </a>
        @endforeach

        <!-- Divider -->
        <div class="border-t border-gray-200 dark:border-gray-700 my-3"></div>

        <!-- Action Buttons -->
        <div class="space-y-1">
            <a href="{{ url('/deploy') }}"
                class="flex items-center space-x-3 px-3 py-2 rounded-lg
                      transition-all text-gray-700 dark:text-gray-200
                      hover:bg-green-50 hover:text-green-600 dark:hover:bg-green-900 dark:hover:text-green-400">
                <span>🚀 Run Deploy</span>
            </a>
            <a href="{{ url('/run-npm-build') }}"
                class="flex items-center space-x-3 px-3 py-2 rounded-lg
                      transition-all text-gray-700 dark:text-gray-200
                      hover:bg-green-50 hover:text-green-600 dark:hover:bg-green-900 dark:hover:text-green-400">
                <span>📦 NPM Build</span>
            </a>
            <a href="{{ url('/optimize-app') }}"
                class="flex items-center space-x-3 px-3 py-2 rounded-lg
                      transition-all text-gray-700 dark:text-gray-200
                      hover:bg-green-50 hover:text-green-600 dark:hover:bg-green-900 dark:hover:text-green-400">
                <span>⚡ Optimize</span>
            </a>
            <a href="{{ url('/link-storage') }}"
                class="flex items-center space-x-3 px-3 py-2 rounded-lg
                      transition-all text-gray-700 dark:text-gray-200
                      hover:bg-green-50 hover:text-green-600 dark:hover:bg-green-900 dark:hover:text-green-400">
                <span>🔗 Storage Link</span>
            </a>
        </div>

    </nav>
</div>
