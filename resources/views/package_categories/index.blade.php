<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-7xl">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-blue-600"></i>
                        Package Categories
                    </h2>
                    <a href="{{ route('package-categories.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Add Category
                    </a>
                </div>

                <!-- Success message -->
                @if (session('success'))
                    <div class="m-6 p-4 bg-green-500 text-white rounded">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="p-6 overflow-x-auto">

                    <x-data-table id="package-categories-table" :headers="['ID', 'Name', 'Action']" :excel="true" :print="true"
                        title="Package Categories List" resourceName="Package Categories">
                        @foreach ($categories as $category)
                            <tr>
                                <td class="text-center">{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td class="text-center">
                                    <a href="{{ route('package-categories.edit', $category->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-500 text-white  rounded-lg shadow-sm hover:bg-yellow-600 transition-colors duration-500 ease-in-out">
                                        <i class="fa-solid fa-pen-to-square"></i>Edit</a>

                                    <form class="inline"
                                        action="{{ route('package-categories.destroy', $category->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-red-600 text-white 
                                                   rounded-lg hover:bg-red-700 shadow-sm transition-colors duration-500 ease-in-out">
                                            <i class="fa-solid fa-trash"></i>Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </x-data-table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
