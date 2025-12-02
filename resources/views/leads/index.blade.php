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
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-sm transition-colors duration-300">
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


        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-start justify-center overflow-auto p-4">

            <div @click.outside="close"
                class="bg-white dark:bg-gray-800 w-full max-w-6xl mt-16 rounded-xl shadow-xl p-6">

                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                        Follow-Up for:
                        <span class="text-blue-600" x-text="leadName"></span>
                        <span class="text-green-600 ml-2" x-text="phoneNumber ? '(' + phoneNumber + ')' : ''"></span>
                    </h2>
                    <button @click="close" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-xmark text-2xl"></i>
                    </button>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-4 border rounded p-4 space-y-4">
                        <form action="{{ route('followup.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="lead_id" x-model="leadId">
                            <div class="space-y-2">
                                <label class="font-semibold text-gray-700 dark:text-gray-300">Followup Reason</label>
                                <template x-for="reason in reasons" :key="reason">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" :value="reason" name="reason"
                                            x-model="selectedReason" class="h-4 w-4">
                                        <span x-text="reason" class="text-gray-700 dark:text-gray-300"></span>
                                    </div>
                                </template>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">
                                    Remark
                                </label>
                                <textarea name="remark" rows="3" class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600"
                                    placeholder="Write remark here..."></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">Next
                                        Followup Date</label>
                                    <input type="date" name="next_followup_date"
                                        class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300">Time</label>
                                    <input type="time" name="next_followup_time"
                                        class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600">
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end gap-3 mt-4">
                                <button type="button" @click="close"
                                    class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">Cancel</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Submit</button>
                            </div>
                        </form>
                    </div>

                    <!-- Followup Table Right Column -->
                    <div class="col-span-8  overflow-x-auto border rounded p-4">
                        <table class="table-auto w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="border p-2">Date</th>
                                    <th class="border p-2">Reason</th>
                                    <th class="border p-2">Remark</th>
                                    <th class="border p-2">Next Followup Date</th>
                                    <th class="border p-2">Record By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="followups.length === 0">
                                    <tr>
                                        <td colspan="5" class="text-center p-4 text-gray-500">No follow-ups found.
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="(f, index) in followups" :key="index">
                                    <tr class="border-b">
                                        <td class="p-2 border" x-text="f.created_at"></td>
                                        <td class="p-2 border" x-text="f.reason"></td>
                                        <td class="p-2 border" x-text="f.remark"></td>
                                        <td class="p-2 border" x-text="f.next_followup_date ?? '-'"></td>
                                        <td class="p-2 border" x-text="f.user_name ?? 'N/A'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
        function followupModal() {
            return {
                open: false,
                leadId: '',
                leadName: '',
                phoneNumber: '',
                phoneCode: '',
                fullNumber: '',
                selectedReason: '',
                followups: [],
                reasons: [
                    'Call Back Later', 'Call Me Tomorrow', 'Payment Tomorrow', 'Talk With My Partner',
                    'Work with other company', 'Not Interested', 'Interested', 'Wrong Information', 'Not Pickup',
                    'Other'
                ],

                openFollowModal(id, name) {
                    this.leadId = id;
                    this.leadName = name;
                    this.open = true;

                    // Fetch merged API
                    fetch(`/leads/${id}/details`)
                        .then(res => res.json())
                        .then(data => {

                            // Phone Numbers
                            this.phoneNumber = data.phone.phone_number;
                            this.phoneCode = data.phone.phone_code;
                            this.fullNumber = data.phone.full_number;

                            // Followups
                            this.followups = data.followups.map(f => ({
                                ...f,
                                created_at: f.created_at,
                                next_followup_date: f.next_followup_date,
                                user_name: f.user_name
                            }));
                        });
                },

                close() {
                    this.open = false;
                    this.followups = [];
                    this.phoneNumber = '';
                    this.fullNumber = '';
                }
            }
        }
    </script>
</x-app-layout>
