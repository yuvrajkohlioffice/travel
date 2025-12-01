<x-app-layout>
    <div class="ml-64">

        <x-slot name="header">
            <h2 class="text-xl font-semibold">Package Types</h2>
        </x-slot>

        <div class="max-w-7xl mx-auto p-6">

            <div class="flex justify-between mb-4">
                <a href="{{ route('package-types.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    + Add Package Type
                </a>
            </div>

            @if (session('success'))
                <div class="p-3 bg-green-500 text-white rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <x-data-table 
                id="package-types-table"
                :headers="['ID', 'Name', 'Action']"
                :excel="true"
                :print="true"
                title="Package Types List"
                resourceName="Package Types"
            >
                @foreach ($packageTypes as $type)
                    <tr>
                        <td class="text-center">{{ $type->id }}</td>
                        <td>{{ $type->name }}</td>
                        <td class="text-center">
                            <a href="{{ route('package-types.edit', $type->id) }}"
                               class="px-3 py-1 bg-yellow-500 text-white rounded">Edit</a>

                            <form class="inline" 
                                  action="{{ route('package-types.destroy', $type->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this type?')">
                                @csrf
                                @method('DELETE')

                                <button class="px-3 py-1 bg-red-600 text-white rounded">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-data-table>

        </div>
    </div>
</x-app-layout>
