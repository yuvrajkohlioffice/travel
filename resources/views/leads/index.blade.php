<x-app-layout>

    <div x-data="followupModal()" x-cloak>
        <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
            <div class="w-full max-w-7xl">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                    <!-- Header -->
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
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
                        <x-data-table id="Leads-table" :headers="['ID', 'Client Info', 'Country', 'Reminder', 'Inquiry For', 'Status', 'Action']" :excel="true" :print="true"
                            title="Leads" resourceName="Leads">
                            @foreach ($leads as $lead)
                                <tr class="border-t">

                                    <!-- Serial -->
                                    <td class="p-3">{{ $loop->iteration }}</td>

                                    <!-- Client Info -->
                                    <td class="p-3">
                                        {{ $lead->name }}
                                        <hr>

                                        <a href="mailto:{{ $lead->email }}" class="text-blue-600">
                                            {{ $lead->email }}
                                        </a>
                                        <hr>

                                        @php
                                            $masked =
                                                str_repeat('*', strlen($lead->phone_number) - 4) .
                                                substr($lead->phone_number, -4);
                                            $created = \Carbon\Carbon::parse($lead->created_at);
                                            $daysFloor = floor($created->diffInRealDays());
                                        @endphp

                                        <a class="text-green-600 hover:underline">
                                            +{{ $lead->phone_code }} {{ $masked }}
                                        </a>

                                        <br>

                                        {{ $lead->created_at->format('d-M-y') }}

                                        <a class="bg-blue-600 p-1 rounded text-white">
                                            {{ $daysFloor }} days old
                                        </a>
                                    </td>

                                    <!-- Country -->
                                    <td class="p-3">
                                        {{ $lead->country }}<br>
                                        {{ $lead->district }}<br>
                                        {{ $lead->city }}<br>
                                    </td>

                                    <!-- Reminder (Followup Button) -->
                                    <td class="p-3">
                                        <button @click="openFollowModal({{ $lead->id }}, '{{ $lead->name }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-green-600 text-white 
                                               rounded-lg hover:bg-green-700 shadow-sm transition-colors duration-300">
                                            <i class="fa-solid fa-phone-volume"></i> Followup
                                        </button>
                                    </td>

                                    <!-- Package -->
                                    <td class="p-3"
                                        title="{{ $lead->package->package_name ?? $lead->inquiry_text }}">
                                        {{ $lead->package->package_name ?? \Illuminate\Support\Str::limit($lead->inquiry_text, 16) }}
                                    </td>

                                    <!-- Status -->
                                    <td class="p-3">{{ $lead->lead_status }}</td>

                                    <!-- Actions -->
                                    <td class="p-3 flex gap-2">
                                        <a href="{{ route('leads.show', $lead->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500 text-white 
                                              rounded-lg hover:bg-blue-600 shadow-sm transition-colors duration-300">
                                            <i class="fa-solid fa-eye"></i>View
                                        </a>

                                        <a href="{{ route('leads.assign.form', $lead->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-purple-500 text-white 
                                              rounded-lg hover:bg-purple-600 shadow-sm transition-colors duration-300">
                                            <i class="fa-solid fa-people-arrows"></i>Assign
                                        </a>

                                        <a href="{{ route('leads.edit', $lead->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-500 text-white  
                                               rounded-lg shadow-sm hover:bg-yellow-600 transition-colors duration-300">
                                            <i class="fa-solid fa-pen-to-square"></i>Edit
                                        </a>

                                        <form action="{{ route('leads.destroy', $lead->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this lead?')">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                class="inline-flex items-center gap-1 px-3 py-1 bg-red-600 text-white 
                                                   rounded-lg hover:bg-red-700 shadow-sm transition-colors duration-300">
                                                <i class="fa-solid fa-trash"></i> Delete
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

        <!-- Followup Modal -->
        <div x-show="open" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

            <div @click.outside="close" class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-xl shadow-xl p-6">

                <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">
                    Follow-Up for: <span class="text-blue-600" x-text="leadName"></span>
                </h2>

                <form action="{{ route('followup.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <input type="hidden" name="lead_id" x-model="leadId">

                    <!-- Reason -->
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">
                            Followup Reason
                        </label>
                        <select name="reason" required
                            class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600">
                            <option value="">Select Reason</option>
                            <option>Call Back Later</option>
                            <option>Call Me Tomorrow</option>
                            <option>Payment Tomorrow</option>
                            <option>Talk With My Partner</option>
                            <option>Work with other company</option>
                            <option>Not Interested</option>
                            <option>Interested</option>
                            <option>Wrong Information</option>
                            <option>Not Pickup</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <!-- Remark -->
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">
                            Remark
                        </label>
                        <textarea name="remark" rows="3" class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600"
                            placeholder="Write remark here..."></textarea>
                    </div>

                    <!-- Next Date + Time -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">
                                Next Followup Date
                            </label>
                            <input type="date" name="next_followup_date"
                                class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">
                                Time
                            </label>
                            <input type="time" name="next_followup_time"
                                class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" @click="close"
                            class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                            Cancel
                        </button>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Save Followup
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <!-- Alpine.js Script -->
    <script>
        function followupModal() {
            return {
                open: false,
                leadId: '',
                leadName: '',

                openFollowModal(id, name) {
                    this.leadId = id;
                    this.leadName = name;
                    this.open = true;
                },
                close() {
                    this.open = false;
                }
            }
        }
    </script>

</x-app-layout>
