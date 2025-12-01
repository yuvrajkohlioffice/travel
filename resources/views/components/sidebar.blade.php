<div class="h-screen w-64 bg-gray-900 text-gray-200 flex flex-col fixed shadow-xl">

    <!-- Logo -->
    <div class="p-6 text-2xl font-bold border-b border-gray-700 tracking-wide">
     Profile
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-2">

        <!-- Dashboard -->
        <a 
            href="{{ route('dashboard') }}"
            class="flex items-center px-4 py-3 rounded-lg transition duration-200 
            {{ request()->routeIs('dashboard') 
                ? 'bg-gray-800 text-white' 
                : 'hover:bg-gray-800 hover:text-white' }}"
        >
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4" />
            </svg>
            Dashboard
        </a>

        <!-- Users -->
        <a 
            href="{{ route('profile.show') }}"
            class="flex items-center px-4 py-3 rounded-lg transition duration-200 hover:bg-gray-800 hover:text-white"
        >
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z" />
            </svg>
            Users
        </a>

        <!-- Settings -->
        <a 
            href="#"
            class="flex items-center px-4 py-3 rounded-lg transition duration-200 hover:bg-gray-800 hover:text-white"
        >
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.325 4.317l.447-1.342a1 1 0 011.894 0l.447 1.342a1 1 0 00.758.683l1.414.29a1 1 0 01.564 1.657l-1.02 1.02a1 1 0 000 1.414l1.02 1.02a1 1 0 01-.564 1.657l-1.414.29a1 1 0 00-.758.683l-.447 1.342a1 1 0 01-1.894 0l-.447-1.342a1 1 0 00-.758-.683l-1.414-.29a1 1 0 01-.564-1.657l1.02-1.02a1 1 0 000-1.414l-1.02-1.02a1 1 0 01.564-1.657l1.414-.29a1 1 0 00.758-.683z" />
            </svg>
            Settings
        </a>

    </nav>

</div>
