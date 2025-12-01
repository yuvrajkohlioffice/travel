<div class="h-screen w-64 bg-gray-900 text-gray-200 flex flex-col fixed left-0 top-0 shadow-lg z-50">

    <!-- Logo / Brand -->
    <div class="p-6 text-2xl font-bold border-b border-gray-800 tracking-wide">
        TRAVEL
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1">

        <!-- Dashboard -->
        <a 
            href="{{ route('dashboard') }}"
            class="flex items-center px-4 py-3 rounded-lg font-medium transition-all duration-200
            {{ request()->routeIs('dashboard') 
                ? 'bg-gray-800 text-white shadow-inner' 
                : 'hover:bg-gray-800 hover:text-white' }}"
        >
            <svg class="w-6 h-6 mr-3 opacity-90" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4" />
            </svg>
            Dashboard
        </a>


        <!-- Profile -->
        <a 
            href="{{ route('profile.show') }}"
            class="flex items-center px-4 py-3 rounded-lg font-medium transition-all duration-200
            {{ request()->routeIs('profile.show') 
                ? 'bg-gray-800 text-white shadow-inner' 
                : 'hover:bg-gray-800 hover:text-white' }}"
        >
            <svg class="w-6 h-6 mr-3 opacity-90" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z" />
            </svg>
            Profile
        </a>
         <a 
            href="{{ route('users.index') }}"
            class="flex items-center px-4 py-3 rounded-lg font-medium transition-all duration-200
            {{ request()->routeIs('users.index') 
                ? 'bg-gray-800 text-white shadow-inner' 
                : 'hover:bg-gray-800 hover:text-white' }}"
        >
            <svg class="w-6 h-6 mr-3 opacity-90" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z" />
            </svg>
            Users
        </a>

        <!-- Settings -->
        

    </nav>
</div>
