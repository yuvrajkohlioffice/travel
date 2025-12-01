<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-lg">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add Pickup Point</h2>

                    <a href="{{ route('pickup-points.index') }}"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        ← Back
                    </a>
                </div>

                <div class="p-6">
                    <form action="{{ route('pickup-points.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                            <input type="text" name="name"
                                class="mt-1 block w-full rounded-lg border px-4 py-2 dark:bg-gray-900 dark:text-white"
                                required>
                        </div>

                        <button class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
                            Save
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
