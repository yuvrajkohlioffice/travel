<x-app-layout>
    <div class="ml-64">

        <x-slot name="header">
            <h2 class="text-xl font-semibold">Add Difficulty Type</h2>
        </x-slot>

        <div class="max-w-7xl mx-auto p-6">
            <form action="{{ route('difficulty-types.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Name</label>
                    <input type="text" name="name" class="w-full border border-gray-300 rounded p-2" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Level</label>
                    <input type="number" name="level" class="w-full border border-gray-300 rounded p-2" min="1" required>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-green-700">
                        Save
                    </button>
                    <a href="{{ route('difficulty-types.index') }}"
                       class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Back</a>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
