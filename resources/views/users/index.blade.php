<x-app-layout>
    <div class="ml-64"> <!-- Shift content because of sidebar -->

        <x-slot name="header">
            <h2 class="text-xl font-semibold">Users</h2>
        </x-slot>

        <div class="max-w-7xl mx-auto p-6">

            <div class="flex justify-between mb-4">
                <a href="{{ route('users.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    + Add User
                </a>
            </div>

            @if (session('success'))
                <div class="p-3 bg-green-500 text-white rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- ✅ Using your DataTable Component -->
            <x-data-table 
                id="users-table"
                :headers="['ID', 'Name', 'Email', 'Action']"
                :excel="true"
                :print="true"
                title="Users List"
                resourceName="Users"
            >
                @foreach ($users as $user)
                    <tr>
                        <td class="text-center">{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>

                        <td class="text-center">

                            <a href="{{ route('users.edit', $user->id) }}"
                               class="px-3 py-1 bg-yellow-500 text-white rounded">Edit</a>

                            <form class="inline" 
                                  action="{{ route('users.destroy', $user->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this user?')">
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
