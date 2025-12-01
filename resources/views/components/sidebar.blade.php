<div class="h-screen w-64 bg-gray-900 text-gray-200 flex flex-col fixed left-0 top-0 shadow-lg z-50">

    <!-- Logo / Brand -->
    <div class="p-6 text-2xl font-bold border-b border-gray-800 tracking-wide">
        TRAVEL
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="flex items-center px-4 py-3 rounded-lg font-medium transition-all duration-200
            {{ request()->routeIs('dashboard')
                ? 'bg-gray-800 text-white shadow-inner'
                : 'hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-6 h-6 mr-3 opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4" />
            </svg>
            Dashboard
        </a>

        <!-- Profile -->
        <a href="{{ route('profile.show') }}"
            class="flex items-center px-4 py-3 rounded-lg font-medium transition-all duration-200
            {{ request()->routeIs('profile.show')
                ? 'bg-gray-800 text-white shadow-inner'
                : 'hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-6 h-6 mr-3 opacity-90" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z" />
            </svg>
            Profile
        </a>

        <!-- Users -->
        <a href="{{ route('users.index') }}"
            class="flex items-center px-4 py-3 rounded-lg font-medium transition-all duration-200
            {{ request()->routeIs('users.index')
                ? 'bg-gray-800 text-white shadow-inner'
                : 'hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-6 h-6 mr-3 opacity-90" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z" />
            </svg>
            Users
        </a>

        <!-- Master Dropdown -->
        <div x-data="{ open: {{ request()->is('package-*') || request()->is('difficulty-types*') || request()->is('package-types*') || request()->is('roles*') ? 'true' : 'false' }} }" class="space-y-1">
            <button @click="open = !open"
                class="flex items-center w-full px-4 py-3 rounded-lg font-medium transition-all duration-200
                    {{ request()->is('package-*') ? 'bg-gray-800 text-white shadow-inner' : 'hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-6 h-6 mr-3 opacity-90" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Master
                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 ml-auto transition-transform duration-200"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" class="pl-8 mt-1 space-y-1" x-cloak>
                <a href="{{ route('roles.index') }}"
                    class="block px-4 py-2 rounded-lg font-medium transition-all duration-200
                   {{ request()->routeIs('roles.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    Roles
                </a>
                <a href="{{ route('package-categories.index') }}"
                    class="block px-4 py-2 rounded-lg font-medium transition-all duration-200
                   {{ request()->routeIs('package-categories.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    Package Categories
                </a>

                <!-- Add other master links here -->
                <a href="{{ route('package-types.index') }}"
                    class="block px-4 py-2 rounded-lg font-medium transition-all duration-200
                   {{ request()->routeIs('package-types.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    Package Types
                </a>

                <a href="{{ route('difficulty-types.index') }}"
                    class="block px-4 py-2 rounded-lg font-medium transition-all duration-200
                   {{ request()->routeIs('difficulty-types.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    Difficulty Types
                </a>
            </div>
        </div>

    </nav>
</div>
