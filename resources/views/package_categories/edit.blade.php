<x-app-layout>
    <div class="ml-64 p-6">
        <x-slot name="header">
            <h2 class="text-xl font-semibold">Edit Package Category</h2>
        </x-slot>

        <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-md border border-gray-100">
            <form action="{{ route('package-categories.update', $packageCategory->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                    <input type="text" name="name" value="{{ $packageCategory->name }}" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                {{-- Only show Global option to Admins --}}
                @if(auth()->user()->role_id == 1)
                <div class="mb-6 flex items-center p-3 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <input type="checkbox" name="is_global" value="1" id="is_global" 
                           {{ $packageCategory->is_global ? 'checked' : '' }} 
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 w-5 h-5">
                    <label for="is_global" class="ml-3 block text-sm font-bold text-purple-700">
                        Mark as Global Category (Visible to all companies)
                    </label>
                </div>
                @endif

                <div class="flex items-center gap-3 border-t pt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
                        Update Category
                    </button>
                    <a href="{{ route('package-categories.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>