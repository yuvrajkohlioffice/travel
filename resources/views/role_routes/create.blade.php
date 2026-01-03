<x-app-layout>
    <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full">

            {{-- Header --}}
            <header class="mb-6 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700
                p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                
                <div class="flex items-center gap-4">
                    <div class="rounded-lg p-3 bg-gray-100/60 dark:bg-gray-700/50">
                        <i class="fa-solid fa-user-shield text-gray-700 dark:text-gray-200 text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-semibold text-gray-800 dark:text-gray-200 leading-tight"> Roles with Route Status</h1>
                        
                    </div>
                </div>

                <a href="{{ route('role_routes.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-medium rounded-lg 
                    shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                   <i class="fas fa-arrow-left"></i>
                    <span class="hidden sm:inline"> Back</span>
                </a>
                
            </header>

            {{-- Breadcrumb --}}
            

            {{-- Alerts --}}
            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-800">
                    <strong>Whoops!</strong> Please fix the errors below.
                    <ul class="list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden">
                <div class="flex justify-between items-center p-5 border-b border-gray-100 dark:border-gray-700">
                    <h5 class="text-green-600 font-semibold flex items-center gap-2"><i class="fas fa-plus-circle"></i> Assign Role Routes</h5>
                    
                </div>
                <div class="p-6">
                    <form action="{{ route('role_routes.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Select Role --}}
                        <div>
                            <label for="role_id" class="block text-gray-700 dark:text-gray-200 font-medium mb-1">Select Role</label>
                            <select name="role_id" id="role_id" required
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                                {{ isset($selectedRole) ? 'readonly disabled' : '' }}>
                                <option value="">-- Choose Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ isset($selectedRole) && $selectedRole->id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if (isset($selectedRole))
                                <input type="hidden" name="role_id" value="{{ $selectedRole->id }}">
                            @endif
                        </div>

                        {{-- Accordion for Routes --}}
                        <div class="space-y-3">
                            @php
                                $groupedRoutes = [
                                    'Index Routes' => [],
                                    'Create Routes' => [],
                                    'Edit Routes' => [],
                                    'Delete Routes' => [],
                                    'Show Routes' => [],
                                    'Others' => [],
                                ];
                                foreach ($routes as $route) {
                                    if (Str::endsWith($route, 'index')) {
                                        $groupedRoutes['Index Routes'][] = $route;
                                    } elseif (Str::endsWith($route, 'create') || Str::contains($route, '.create')) {
                                        $groupedRoutes['Create Routes'][] = $route;
                                    } elseif (Str::endsWith($route, 'edit') || Str::contains($route, '.edit')) {
                                        $groupedRoutes['Edit Routes'][] = $route;
                                    } elseif (Str::endsWith($route, 'destroy') || Str::contains($route, '.destroy') || Str::contains($route, 'delete')) {
                                        $groupedRoutes['Delete Routes'][] = $route;
                                    } elseif (Str::endsWith($route, 'show') || Str::contains($route, '.show')) {
                                        $groupedRoutes['Show Routes'][] = $route;
                                    } else {
                                        $groupedRoutes['Others'][] = $route;
                                    }
                                }
                            @endphp

                            @foreach ($groupedRoutes as $group => $groupRoutes)
                                <div x-data="{ open: false }" class="border rounded-lg overflow-hidden">
                                    <button type="button"
                                        @click="open = !open"
                                        class="w-full flex justify-between items-center px-4 py-3 bg-gray-100 dark:bg-gray-700/50 text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none">
                                        {{ $group }} ({{ count($groupRoutes) }})
                                        <svg :class="{'rotate-45': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-collapse class="p-4 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                                        @if(count($groupRoutes))
                                            <table class="w-full text-left border border-gray-200 dark:border-gray-700 rounded-lg">
                                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                                    <tr>
                                                        <th class="w-12 px-3 py-2">
                                                            <input type="checkbox" class="select-all-group" data-group="{{ Str::slug($group) }}">
                                                        </th>
                                                        <th class="px-3 py-2">Route Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($groupRoutes as $route)
                                                        <tr class="border-t border-gray-100 dark:border-gray-700">
                                                            <td class="px-3 py-2">
                                                                <input type="checkbox" name="route_names[]" value="{{ $route }}"
                                                                    class="route-checkbox group-{{ Str::slug($group) }}"
                                                                    {{ in_array($route, old('route_names', $selectedRoutes ?? [])) ? 'checked' : '' }}>
                                                            </td>
                                                            <td class="px-3 py-2">{{ $route }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p class="text-gray-500 dark:text-gray-400">No routes found.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Submit --}}
                        <div class="flex justify-end mt-4">
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow transition">
                                <i class="fas fa-save"></i> Save Assignments
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Select All Script --}}
            <script>
                document.querySelectorAll('.select-all-group').forEach(selectAll => {
                    selectAll.addEventListener('change', function(e) {
                        const group = e.target.dataset.group;
                        document.querySelectorAll('.group-' + group).forEach(cb => cb.checked = e.target.checked);
                    });
                });
            </script>

        </div>
    </div>
</x-app-layout>
