<x-app-layout>
    <div class="ml-64 p-6">

        <x-slot name="header">
            <h2 class="text-xl font-semibold">Packages</h2>
        </x-slot>

        <div class="mb-4">
            <a href="{{ route('packages.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                + Add Package
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-500 text-white rounded">
                {{ session('success') }}
            </div>
        @endif

        <x-data-table 
            id="packages-table"
            :headers="['ID','Name','Type','Category','Difficulty','Days','Nights','Price','Action']"
            :excel="true"
            :print="true"
            title="Packages List"
            resourceName="Packages"
        >
            @foreach($packages as $package)
                <tr>
                    <td class="text-center">{{ $package->id }}</td>
                    <td>{{ $package->package_name }}</td>
                    <td>{{ $package->packageType->name ?? '' }}</td>
                    <td>{{ $package->packageCategory->name ?? '' }}</td>
                    <td>{{ $package->difficultyType->name ?? '' }}</td>
                    <td>{{ $package->package_days }}</td>
                    <td>{{ $package->package_nights }}</td>
                    <td>{{ $package->package_price }}</td>
                    <td class="text-center">
                        <a href="{{ route('packages.edit', $package->id) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</a>

                        <a href="{{ route('packages.show', $package->id) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Show</a>

                        <form action="{{ route('packages.destroy', $package->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this package?')">
                            @csrf
                            @method('DELETE')
                            <button class="px-2 py-1 bg-red-600 text-white rounded">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </x-data-table>

    </div>
</x-app-layout>
