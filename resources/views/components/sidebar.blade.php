<div class="h-screen w-64 bg-white text-gray-800 flex flex-col fixed left-0 top-0 z-50">

    <!-- Logo -->
    <div class="p-4 text-2xl font-bold border-b border-gray-200 tracking-wide flex items-center space-x-2">
        <i class="fas fa-route text-blue-500"></i>
        <span>TRAVEL</span>
    </div>

    <nav class="flex-1 p-4 space-y-2 shadow-lg ">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home w-6 text-lg"></i>
            Dashboard
        </a>

        <!-- Profile -->
        <a href="{{ route('profile.show') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="fas fa-user text-lg w-6"></i>
            Profile
        </a>

        <!-- Users -->

        <a href="{{ route('leads.index') }}" class="sidebar-link {{ request()->routeIs('leads.*') ? 'active' : '' }}">
            <i class="fas fa-people-group text-lg w-6"></i>
            Leads
        </a>

        <!-- Packages -->
        <a href="{{ route('packages.index') }}"
            class="sidebar-link {{ request()->routeIs('packages.*') ? 'active' : '' }}">
            <i class="fas fa-box-open text-lg w-6"></i>
            Packages
        </a>
        <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fas fa-user text-lg w-6"></i>
            Users
        </a>
        <a href="{{ route('pickup-points.index') }}"
            class="sidebar-link {{ request()->routeIs('pickup-points.*') ? 'active' : '' }}">
            <i class="fas fa-map-pin text-lg w-6"></i>
            Pickup Points
        </a>

        <!-- Roles -->
        <a href="{{ route('roles.index') }}" class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
            <i class="fas fa-user-shield text-lg w-6"></i>
            Roles
        </a>

        <!-- Package Categories -->
        <a href="{{ route('package-categories.index') }}"
            class="sidebar-link {{ request()->routeIs('package-categories.*') ? 'active' : '' }}">
            <i class="fas fa-layer-group text-lg w-6"></i>
            Package Categories
        </a>

        <!-- Package Types -->
        <a href="{{ route('package-types.index') }}"
            class="sidebar-link {{ request()->routeIs('package-types.*') ? 'active' : '' }}">
            <i class="fas fa-tags text-lg w-6"></i>
            Package Types
        </a>

        <!-- Cars -->
        <a href="{{ route('cars.index') }}" class="sidebar-link {{ request()->routeIs('cars.*') ? 'active' : '' }}">
            <i class="fas fa-car text-lg w-6"></i>
            Cars / Cabs
        </a>

        <!-- Hotels -->
        <a href="{{ route('hotels.index') }}"
            class="sidebar-link {{ request()->routeIs('hotels.*') ? 'active' : '' }}">
            <i class="fas fa-hotel text-lg w-6"></i>
            Hotels
        </a>

        <!-- Difficulty Types -->
        <a href="{{ route('difficulty-types.index') }}"
            class="sidebar-link {{ request()->routeIs('difficulty-types.*') ? 'active' : '' }}">
            <i class="fas fa-mountain text-lg w-6"></i>
            Difficulty Types
        </a>


    </nav>
</div>
