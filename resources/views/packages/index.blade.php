<x-app-layout>
    <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full">

            {{-- Header / Title + Actions --}}
            <header
                class="mb-3 bg-white/80 backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="rounded-lg p-3 bg-gray-100/60">
                        <i class="fa-solid fa-box-open text-gray-700 text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                            Packages</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manage all travel packages — create, edit, link
                            relations, view and delete.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('packages.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-800 font-medium rounded-lg shadow-sm border border-gray-200 hover:shadow-md hover:bg-gray-50 transition">
                        <i class="fa-solid fa-plus"></i>
                        <span class="hidden sm:inline">Add Package</span>
                    </a>
                </div>
            </header>

            {{-- Flash Success --}}
            @if (session('success'))
                <div
                    class="flex items-start gap-3 bg-green-50 border border-green-100 rounded-lg p-4 text-sm md:text-base shadow-sm">
                    <div class="text-green-600 mt-0.5">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="text-gray-800">{{ session('success') }}</div>
                    <button onclick="this.parentElement.remove()"
                        class="ml-auto text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Dismiss">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            {{-- Controls + Table Card --}}
            <section class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">


                {{-- Table wrapper --}}
                <div class="p-4 md:p-6">
                    <div class="overflow-x-auto">
                        {{-- Keep x-data-table as requested. Styled the slot rows/cells below. --}}
                        <x-data-table id="packages-table" :headers="[
                            'ID',
                            'Name',
                            'Type',
                            'Category',
                            'Company',
                            'Difficulty',
                            'Days',
                            'Nights',
                            'Price',
                            'Action',
                        ]" :excel="true" :print="true"
                            title="Packages List" resourceName="Packages">
                            @foreach ($packages as $package)
                                <tr
                                    class="border-b border-gray-200 dark:border-gray-700
                   hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors
                   {{ $package->trashed() ? 'bg-red-50 opacity-70' : '' }}">
                                    {{-- ID --}}
                                    <td class="p-3 text-center text-sm font-medium text-gray-700 {{ $package->trashed() ? 'bg-red-50 opacity-70' : '' }} ">
                                        {{ $package->id }}
                                    </td>

                                    {{-- Name --}}
                                    <td class="p-3 text-sm font-semibold text-gray-800 {{ $package->trashed() ? 'bg-red-50 opacity-70' : '' }}">
                                        <div class="truncate max-w-[260px]">
                                            {{ $package->package_name }}
                                        </div>
                                    </td>

                                    {{-- Type --}}
                                    <td class="p-3 text-center text-sm text-gray-600">
                                        {{ $package->packageType->name ?? '-' }}
                                    </td>

                                    {{-- Category --}}
                                    <td class="p-3 text-center text-sm text-gray-600">
                                        {{ $package->packageCategory->name ?? '-' }}
                                    </td>

                                    {{-- Company --}}
                                    <td class="p-3 text-center text-sm text-gray-600">
                                        {{ $package->company->company_name ?? '-' }}
                                    </td>

                                    {{-- Difficulty --}}
                                    <td class="p-3 text-center text-sm text-gray-600">
                                        {{ $package->difficultyType->name ?? '-' }}
                                    </td>

                                    {{-- Days --}}
                                    <td class="p-3 text-center text-sm text-gray-600">
                                        {{ $package->package_days ?? '-' }}
                                    </td>

                                    {{-- Nights --}}
                                    <td class="p-3 text-center text-sm text-gray-600">
                                        {{ $package->package_nights ?? '-' }}
                                    </td>

                                    {{-- Price --}}
                                    <td class="p-3 text-center text-sm">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-md
                             text-green-700 bg-green-50 border border-green-100 font-semibold">
                                            ₹{{ number_format($package->package_price) }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="p-3 text-sm">
                                        <div class="flex flex-wrap justify-center gap-2">
                                            @if (!$package->trashed())
                                                {{-- Edit --}}
                                                <a href="{{ route('packages.edit', $package->id) }}"
                                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg
                                  border bg-white hover:bg-gray-50 text-sm">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                    <span class="hidden sm:inline">Edit</span>
                                                </a>

                                                {{-- Show --}}
                                                <a href="{{ route('packages.show', $package->id) }}"
                                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg
                                  bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
                                                    <i class="fa-solid fa-eye"></i>
                                                    <span class="hidden sm:inline">Show</span>
                                                </a>

                                                {{-- Package Details --}}
                                                <a href="{{ route('packages.edit-relations', $package->id) }}"
                                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg
                                  bg-emerald-600 text-white hover:bg-emerald-700 text-sm">
                                                    <i class="fa-solid fa-link"></i>
                                                    <span class="hidden sm:inline">Package Details</span>
                                                </a>

                                                {{-- Delete --}}
                                                <form action="{{ route('packages.destroy', $package->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirmDelete(event, '{{ addslashes($package->package_name) }}')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg
                                       bg-red-600 text-white hover:bg-red-700 text-sm">
                                                        <i class="fa-solid fa-trash"></i>
                                                        <span class="hidden sm:inline">Delete</span>
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Restore --}}
                                                <form action="{{ route('packages.restore', $package->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg
                                       bg-green-600 text-white hover:bg-green-700 text-sm">
                                                        <i class="fa-solid fa-rotate-left"></i>
                                                        Restore
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </x-data-table>

                    </div>
                </div>
            </section>

        </div>
    </div>
    </div>

    {{-- Minimal JS interactions (vanilla JS) --}}
    <script>
        // Confirm delete with package name for clarity
        function confirmDelete(event, name) {
            event = event || window.event;
            const message = `Are you sure you want to delete the package "${name}"? This action cannot be undone.`;
            return confirm(message);
        }

        // Example export triggers; customize to interact with your x-data-table's API if available.
        function triggerExport(type) {
            // If x-data-table provides export buttons, trigger them here.
            // Fallback: notify user visually (you can wire this to x-data-table actions).
            alert('Export: ' + type +
                '. If your data table supports programmatic export, wire triggerExport() accordingly.');
        }

        // Local search that filters visible table rows (best-effort UI complement to x-data-table)
        function applyLocalTableSearch(q) {
            q = (q || '').trim().toLowerCase();
            const table = document.querySelector('#packages-table');
            if (!table) return;
            // Find all rows but ignore header rows
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
            });
        }

        function clearSearch() {
            const input = document.getElementById('packages-search');
            if (input) {
                input.value = '';
                applyLocalTableSearch('');
            }
        }

        // Accessibility: focus-visible friendly outlines
        (function() {
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    document.documentElement.classList.add('user-is-tabbing');
                }
            });
        })();
    </script>

    {{-- Optional: tiny inline style for focus visible using Tailwind's utilities are sufficient.
               If you prefer purely utility classes, ensure your Tailwind config enables focus-visible variants. --}}

</x-app-layout>
