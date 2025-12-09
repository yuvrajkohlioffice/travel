<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-7xl">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-building text-green-600"></i>
                        Companies
                    </h2>
                    <a href="{{ route('companies.create') }}"
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Company
                    </a>
                </div>

                <!-- Success message -->
                @if (session('success'))
                    <div class="m-6 p-4 bg-green-500 text-white rounded flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <!-- Companies Table -->
                <div class="p-6 overflow-x-auto">
                    <x-data-table 
                        id="companies-table"
                        :headers="['ID', 'Company Name', 'Owner', 'Team', 'Action']"
                        :excel="true" :print="true"
                        title="Companies List"
                        resourceName="Companies"
                    >
                        @foreach ($companies as $company)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="text-center">{{ $company->id }}</td>
                                <td class="flex items-center gap-2">
                                    <i class="fas fa-building text-gray-400"></i> {{ $company->company_name }}
                                </td>
                                <td>
                                    <i class="fas fa-user text-gray-400"></i> {{ $company->owner?->name ?? '—' }}
                                </td>
                                <td>
                                    <i class="fas fa-users text-gray-400"></i> {{ $company->team_name ?? '—' }}
                                </td>
                                <td class="text-center space-x-2 flex justify-center">
                                    <a href="{{ route('companies.edit', $company->id) }}"
                                       class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 flex items-center gap-1 justify-center">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('companies.destroy', $company->id) }}" method="POST"
                                          onsubmit="return confirm('Delete this company?')" class="inline">
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
