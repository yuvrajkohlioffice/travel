<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-4xl">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                        Edit Cars & Hotels for {{ $package->package_name }}
                    </h2>
                    <a href="{{ route('packages.index') }}"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back
                    </a>
                </div>

                
                    <div class="max-w-6xl mx-auto p-6">

                        <h1 class="text-2xl font-bold mb-6">Edit Package Items — {{ $package->name }}</h1>

                        <form action="{{ route('packages.update-relations', $package->id) }}" method="POST">
                            @csrf
                            @method('POST')

                            <div id="items-wrapper" class="space-y-6">
                                @foreach ($package->packageItems as $index => $item)
                                    @include('packages.partials.package-item-row', [
                                        'index' => $index,
                                        'item' => $item,
                                        'allCars' => $allCars,
                                        
                                    ])
                                @endforeach
                            </div>

                            <!-- Add Item Button -->
                            <button type="button" id="add-item"
                                class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg">
                                + Add Item
                            </button>

                            <button type="submit" class="mt-6 px-6 py-3 bg-green-600 text-white rounded-xl">
                                Save Package Items
                            </button>

                        </form>
                    </div>

                    <script>
                        let index = {{ count($package->packageItems) }};

                        // Add new item row
                        document.getElementById('add-item').addEventListener('click', () => {
                            fetch("{{ route('packages.partialItemRow') }}?index=" + index)
                                .then(res => res.text())
                                .then(html => {
                                    document.getElementById('items-wrapper').insertAdjacentHTML('beforeend', html);
                                    index++;
                                });
                        });
                    </script>



    </x-app-layout>
