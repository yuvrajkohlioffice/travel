<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-7xl">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Leads</h2>
                    <a href="{{ route('leads.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Add Lead
                    </a>
                </div>

                <!-- Success message -->
                @if (session('success'))
                    <div class="m-6 p-4 bg-green-500 text-white rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Users Table -->
                <div class="p-6 overflow-x-auto">
                    <x-data-table id="Leads-table" :headers="['ID', 'Name', 'Phone Number', 'Email', 'Inquiry For', 'Status', 'Action']" :excel="true" :print="true" title="Leads"
                        resourceName="Leads">
                        @foreach ($leads as $lead)
                            <tr class="border-t">
                                <td class="p-3">{{ $loop->iteration }}</td>
                                <td class="p-3">{{ $lead->name }}</td>
                                <td class="p-3">+{{ $lead->phone_code }} {{ $lead->phone_number }}</td>
                                <td class="p-3">{{ $lead->email }}</td>
                                <td class="p-3">{{ $lead->package->package_name ?? 'Custom Inquiry' }}</td>
                                <td class="p-3">{{ $lead->lead_status }}</td>

                                <td class="p-3 flex gap-2">
                                    <a href="{{ route('leads.show', $lead->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500 text-white 
                                              rounded-lg hover:bg-blue-600 shadow-sm">
                                        <i class="fa-solid fa-eye"></i>View</a>
                                    <a href="{{ route('leads.assign.form', $lead->id) }}"
                                        class="px-3 py-1 bg-purple-600 text-white rounded">
                                        Assign
                                    </a>
                                    <a href="{{ route('leads.edit', $lead->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-500 text-white 
                                              rounded-lg hover:bg-yellow-600 shadow-sm">
                                        <i class="fa-solid fa-pen-to-square"></i>Edit</a>

                                    <form action="{{ route('leads.destroy', $lead->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this lead?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-red-600 text-white 
                                                   rounded-lg hover:bg-red-700 shadow-sm">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </x-data-table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
