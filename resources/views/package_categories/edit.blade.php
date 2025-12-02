<x-app-layout>
    <div class="ml-64">

        <x-slot name="header">
            <h2 class="text-xl font-semibold">Edit Package Category</h2>
        </x-slot>

        <div class="max-w-7xl mx-auto p-6">
            <form action="{{ route('package-categories.update', $packageCategory->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Category Name</label>
                    <input type="text" name="name" value="{{ $packageCategory->name }}" 
                           class="w-full border border-gray-300 rounded p-2" required>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-green-700">
                        Update
                    </button>
                    <a href="{{ route('package-categories.index') }}"
                       class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Back</a>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
