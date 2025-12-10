<x-app-layout>
    <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full">

            {{-- Header --}}
            <header
                class="mb-3 bg-white/80 backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="rounded-lg p-3 bg-gray-100/60">
                        <i class="fa-solid fa-location-dot text-gray-700 text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">Pickup Points</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manage all travel Pickup Points — create, edit, view and delete.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('pickup-points.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-800 font-medium rounded-lg shadow-sm border border-gray-200 hover:shadow-md hover:bg-gray-50 transition">
                        <i class="fa-solid fa-plus"></i>
                        <span class="hidden sm:inline">Add Pickup Point</span>
                    </a>
                </div>
            </header>

            {{-- Success --}}
            @if (session('success'))
                <div
                    class="flex items-start gap-3 bg-green-50 border border-green-100 rounded-lg p-4 text-sm md:text-base shadow-sm mb-4">
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

            {{-- Table Card --}}
            <section class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="p-4 md:p-6">
                    <div class="overflow-x-auto">
                        <x-data-table 
                            id="pickup-points-table" 
                            :headers="['ID', 'Name', 'Action']" 
                            :excel="true" 
                            :print="true" 
                            title="Pickup Points List" 
                            resourceName="Pickup Points"
                        >
                            @foreach ($pickupPoints as $point)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors transition-colors">

                                    {{-- ID --}}
                                    <td class="p-3 text-center text-sm text-gray-800 font-medium">
                                        {{ $point->id }}
                                    </td>

                                    {{-- Name --}}
                                    <td class="p-3 text-sm text-gray-700">
                                        {{ $point->name }}
                                    </td>

                                    {{-- Action --}}
                                    <td class="p-3 text-center text-sm text-gray-700">
                                        <div class="flex flex-wrap items-center justify-center gap-2">

                                            {{-- Edit --}}
                                            <a href="{{ route('pickup-points.edit', $point->id) }}"
                                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 hover:shadow-sm transition text-sm">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                <span class="hidden sm:inline">Edit</span>
                                            </a>

                                            {{-- Delete --}}
                                            <form action="{{ route('pickup-points.destroy', $point->id) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Delete this pickup point?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-600 text-white hover:bg-red-700 transition text-sm shadow-sm">
                                                    <i class="fa-solid fa-trash"></i>
                                                    <span class="hidden sm:inline">Delete</span>
                                                </button>
                                            </form>

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
</x-app-layout>
