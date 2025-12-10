<x-app-layout>
    <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-people-group text-blue-600"></i>
                        Users
                    </h2>
                    <a href="{{ route('users.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add User
                    </a>
                </div>

                <!-- Success message -->
                @if (session('success'))
                    <div class="m-6 p-4 bg-green-500 text-white rounded flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <!-- Users Table -->
                <div class="p-6 overflow-x-auto">
                    <x-data-table 
                        id="users-table" 
                        :headers="['ID', 'Name', 'Email', 'Role', 'Company', 'WhatsApp API Key', 'Status', 'Created At', 'Action']" 
                        :excel="true" 
                        :print="true" 
                        title="Users List" 
                        resourceName="Users"
                    >
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="text-center">{{ $user->id }}</td>
                                <td class="flex items-center gap-2">
                                    <i class="fas fa-user text-gray-400"></i> {{ $user->name }}
                                </td>
                                <td>
                                    <i class="fas fa-envelope text-gray-400"></i> {{ $user->email }}
                                </td>
                                <td>
                                    <i class="fas fa-user-tag text-gray-400"></i> {{ $user->role->name ?? 'N/A' }}
                                </td>
                                <td>
                                    <i class="fas fa-building text-gray-400"></i> {{ $user->company->company_name ?? 'N/A' }}
                                </td>
                                <td>
                                    <i class="fab fa-whatsapp text-green-500"></i> {{ $user->whatsapp_api_key ?? '-' }}
                                </td>
                                <td>
                                    @if($user->status)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at }}</td>
                                <td class="flex justify-center gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 flex items-center gap-1 justify-center">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this user?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 justify-center">
                                            <i class="fas fa-trash-alt"></i> Delete
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
