<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-7xl">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-people-group text-blue-600"></i>
                        Leads
                    </h2>
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
                    <x-data-table id="Leads-table" :headers="['ID', 'Client Infor', 'Country', 'Reminder', 'Inquiry For', 'Status', 'Action']" :excel="true" :print="true" title="Leads"
                        resourceName="Leads">
                        @foreach ($leads as $lead)
                            <tr class="border-t">
                                <td class="p-3">{{ $loop->iteration }}</td>
                                <td class="p-3">
                                    {{ $lead->name }}
                                    <hr>
                                    <a href="mailto:{{ $lead->email }}" class="text-blue-600">
                                        {{ $lead->email }}
                                    </a>
                                    <hr>
         @php
                                     $masked = str_repeat('*', strlen($lead->phone_number) - 4) . substr($lead->phone_number, -4);
                                        $created_type = is_object($lead->created_at)
                                            ? get_class($lead->created_at)
                                            : gettype($lead->created_at);
                                        $created = \Carbon\Carbon::parse($lead->created_at);
                                        $daysFloor = (int) floor($created->diffInRealDays());
                                        $daysFromSeconds = (int) floor($created->diffInSeconds() / 86400);
                                    @endphp

<a target="_blank" class="text-green-600 hover:underline">
    +{{ $lead->phone_code }} {{ $masked }}
</a>
                                    <br>
                                  
                                    {{ $lead->created_at->format('d-M-y') }}
                                    <a class="bg-blue-600 p-1 rounded text-white">
                                        {{ $daysFloor }} days old

                                    </a>
                                </td>

                                <td class="p-3">
                                    {{ $lead->country }}<br>
                                    {{ $lead->district }}<br>
                                    {{ $lead->city }}<br>
                                </td>
                                <td class="p-3">
                                    Last Reminder
                                </td>
                                <td class="p-3" title="{{ $lead->package->package_name ?? $lead->inquiry_text }}">
                                    {{ $lead->package->package_name ?? \Illuminate\Support\Str::limit($lead->inquiry_text, 16) }}
                                </td>

                                <td class="p-3">{{ $lead->lead_status }}</td>

                                <td class="p-3 flex gap-2">
                                    <a href="{{ route('leads.show', $lead->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500 text-white 
                                              rounded-lg hover:bg-blue-600 shadow-sm transition-colors duration-500 ease-in-out">
                                        <i class="fa-solid fa-eye"></i>View</a>
                                    <a href="{{ route('leads.assign.form', $lead->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-purple-500 text-white 
                                              rounded-lg hover:bg-purple-600 shadow-sm transition-colors duration-500 ease-in-out">
                                        <i class="fa-solid fa-people-arrows"></i>Assign
                                    </a>
                                    <a href="{{ route('leads.edit', $lead->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-500 text-white  rounded-lg shadow-sm hover:bg-yellow-600 transition-colors duration-500 ease-in-out">
                                        <i class="fa-solid fa-pen-to-square"></i>Edit
                                    </a>

                                    <form action="{{ route('leads.destroy', $lead->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this lead?')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-red-600 text-white 
                                                   rounded-lg hover:bg-red-700 shadow-sm transition-colors duration-500 ease-in-out">
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
