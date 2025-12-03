<x-app-layout>
    <div x-data="followupModal()" x-cloak class="min-h-screen bg-gradient-to-br from-gray-100 via-white to-gray-200">

        <div class="ml-64 flex justify-center items-start min-h-screen p-6">
            <div class="w-full max-w-7xl space-y-6">

                <!-- Header -->
                <div
                    class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-xl shadow-lg">

                    <!-- Title -->
                    <h2 class="text-3xl font-bold flex items-center gap-3">
                        <i class="fa-solid fa-people-group"></i> Leads
                    </h2>

                    <!-- Actions -->
                    <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">

                        <!-- Import Template -->
                        <a href="/Example-Import-Leads.xlsx"
                            class="px-4 py-2 bg-white text-blue-600 font-semibold rounded-lg shadow hover:bg-gray-100 transition">
                            Import Template
                        </a>

                        <!-- Import File Form -->
                        <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data"
                            class="flex items-center gap-2">
                            @csrf
                            <input type="file" name="file" accept=".xlsx,.csv" required
                                class="text-sm text-white file:bg-white file:border-0 file:rounded-lg file:px-3 file:py-1 file:font-semibold file:hover:bg-white cursor-pointer" />
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                                Import Leads
                            </button>
                        </form>

                        <!-- Add Lead Button -->
                        <a href="{{ route('leads.create') }}"
                            class="px-5 py-2 bg-white text-blue-600 font-semibold rounded-lg shadow hover:bg-gray-100 transition">
                            + Add Lead
                        </a>

                    </div>
                </div>


                <!-- Success Message -->
                @if (session('success'))
                    <div class="p-4 bg-green-500 text-white rounded-lg shadow">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Leads Table -->
                <div class="bg-white rounded-xl shadow overflow-x-auto p-4">
                    <x-data-table id="Leads-table" :headers="[
                        'ID',
                        'Client Info',
                        'Country',
                        'Reminder',
                        'Inquiry For',
                        'Proposal',
                        'Status',
                        'Action',
                    ]" :excel="true" :print="true" title="Leads"
                        resourceName="Leads">
                        @foreach ($leads as $lead)
                            <tr class="border-b hover:bg-gray-50 transition-colors">

                                <!-- Serial -->
                                <td class="p-3 text-center font-medium">{{ $loop->iteration }}</td>

                                <!-- Client Info -->
                                <td class="p-3 space-y-1">
                                    <div class="font-semibold text-gray-800">{{ $lead->name }}</div>
                                    <a href="mailto:{{ $lead->email }}"
                                        class="text-blue-600 hover:underline">{{ $lead->email }}</a>

                                    @php
                                        $masked =
                                            str_repeat('*', strlen($lead->phone_number) - 4) .
                                            substr($lead->phone_number, -4);
                                        $created = \Carbon\Carbon::parse($lead->created_at);
                                        $daysFloor = floor($created->diffInRealDays());
                                    @endphp

                                    <div class="text-green-600 font-mono">+{{ $lead->phone_code }} {{ $masked }}
                                    </div>
                                    <div class="text-gray-500 text-sm">{{ $lead->created_at->format('d-M-y') }} • <span
                                            class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded">{{ $daysFloor }}
                                            days old</span></div>
                                </td>

                                <!-- Country -->
                                <td class="p-3 text-sm text-gray-700 space-y-1">
                                    <div>{{ $lead->country }}</div>
                                    <div>{{ $lead->district }}</div>
                                    <div>{{ $lead->city }}</div>
                                </td>

                                <!-- Reminder -->
                                <td class="p-3 text-center">
                                    <button @click="openFollowModal({{ $lead->id }}, '{{ $lead->name }}')"
                                        class="bg-green-500 text-white px-4 py-1 rounded-lg hover:bg-green-600 shadow-sm transition duration-300">
                                        <i class="fa-solid fa-phone-volume mr-1"></i> Followup
                                    </button>
                                </td>

                                <!-- Package / Inquiry -->
                                <td class="p-3 text-gray-800"
                                    title="{{ $lead->package->package_name ?? $lead->inquiry_text }}">
                                    {{ $lead->package->package_name ?? \Illuminate\Support\Str::limit($lead->inquiry_text, 20) }}
                                </td>
                                <td class="p-3 text-center flex gap-2 justify-center">
                                    <!-- Send Details -->
                                    <button
                                        @click="handleShare({{ $lead->id }}, '{{ $lead->name }}', '{{ $lead->package->id ?? '' }}')"
                                        class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition">
                                        <i class="fa-solid fa-share"></i>
                                    </button>



                                    <!-- Generate Invoice (only for Hot/Interested leads) -->
                                    @if (in_array($lead->lead_status, ['Hot', 'Interested']))
                                        <button @click="openInvoiceModal({{ $lead->id }}, '{{ $lead->name }}')"
                                            class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </button>
                                    @endif

                                    <!-- Other Action -->
                                    <button @click="openOtherModal({{ $lead->id }}, '{{ $lead->name }}')"
                                        class="bg-gray-400 text-white px-2 py-1 rounded hover:bg-gray-500">
                                        Other
                                    </button>
                                </td>

                                <!-- Status -->
                                <td class="p-3 text-center">
                                    @php
                                        $statusColors = [
                                            'Hot' => 'bg-red-500',
                                            'Warm' => 'bg-yellow-400',
                                            'Cold' => 'bg-gray-400',
                                            'Interested' => 'bg-green-500',
                                        ];
                                        $statusClass = $statusColors[$lead->lead_status] ?? 'bg-gray-300';
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-full text-white {{ $statusClass }}">{{ $lead->lead_status }}</span>
                                </td>

                                <!-- Actions -->
                                <td class="p-3  flex items-center gap-5">
                                    <a href="{{ route('leads.show', $lead->id) }}" class="btn-view p-1">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <!-- Assign -->
                                    <a href="{{ route('leads.assign.form', $lead->id) }}" class="btn-assign p-1">
                                        <i class="fa-solid fa-user-plus"></i>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('leads.edit', $lead->id) }}" class="btn-edit p-1">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('leads.destroy', $lead->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this lead?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete p-1">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </x-data-table>
                </div>

            </div>
        </div>

        <!-- Follow-up Modal -->
        <div x-show="open" x-transition.opacity
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-start justify-center overflow-auto p-4">
            <div @click.outside="close"
                class="bg-white dark:bg-gray-800 w-full max-w-6xl mt-16 rounded-2xl shadow-xl p-6">

                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        Follow-Up: <span class="text-blue-600" x-text="leadName"></span>
                        <span class="text-green-600 ml-2" x-text="phoneNumber ? '(' + phoneNumber + ')' : ''"></span>
                    </h2>
                    <button @click="close" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-xmark text-2xl"></i>
                    </button>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <!-- Form -->
                    <div class="col-span-4 border rounded-xl p-6 space-y-4 shadow-sm bg-gray-50">
                        <form action="{{ route('followup.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="lead_id" x-model="leadId">

                            <div class="space-y-2">
                                <label class="font-semibold text-gray-700">Followup Reason</label>
                                <template x-for="reason in reasons" :key="reason">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" :value="reason" name="reason"
                                            x-model="selectedReason" class="h-4 w-4">
                                        <span x-text="reason" class="text-gray-700"></span>
                                    </div>
                                </template>
                            </div>

                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Remark</label>
                                <textarea name="remark" rows="3" class="w-full p-3 rounded-lg border" placeholder="Write remark here..."></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-semibold mb-1">Next Followup Date</label>
                                    <input type="date" name="next_followup_date"
                                        class="w-full p-3 rounded-lg border">
                                </div>
                                <div>
                                    <label class="block font-semibold mb-1">Time</label>
                                    <input type="time" name="next_followup_time"
                                        class="w-full p-3 rounded-lg border">
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-4">
                                <button type="button" @click="close"
                                    class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">Cancel</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Submit</button>
                            </div>
                        </form>
                    </div>

                    <!-- Followups Table -->
                    <div class="col-span-8 overflow-x-auto border rounded-xl p-4 shadow-sm bg-white">
                        <table class="table-auto w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border p-2 text-left">Date</th>
                                    <th class="border p-2 text-left">Reason</th>
                                    <th class="border p-2 text-left">Remark</th>
                                    <th class="border p-2 text-left">Next Followup Date</th>
                                    <th class="border p-2 text-left">Record By</th>
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
                                    <tr class="border-b hover:bg-gray-50 transition">
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
        <!-- Share Details Modal -->
        <!-- Share Modal -->
        <div x-show="shareOpen" x-transition.opacity
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div x-transition class="bg-white w-full max-w-md rounded-2xl p-6 shadow-xl space-y-4">
                <!-- Header -->
                <div class="flex justify-between items-center border-b pb-3">
                    <h2 class="text-xl font-bold text-gray-800">
                        Share Lead – <span x-text="shareLeadName"></span>
                    </h2>
                    <button @click="closeShare" class="text-gray-500 hover:text-black text-xl">
                        &times;
                    </button>
                </div>

                <div class="space-y-4">

                    <!-- Package Dropdown -->
                    <div x-show="showDropdown" x-transition>
                        <label class="block font-semibold text-gray-700 mb-1">
                            Select Package
                        </label>

                        <select x-model="selectedPackage"
                            class="w-full p-3 rounded-lg border bg-gray-50 focus:ring focus:ring-blue-300 transition">
                            <template x-for="pkg in allPackages" :key="pkg.id">
                                <option :value="pkg.id" x-text="pkg.package_name"></option>
                            </template>
                        </select>
                    </div>
                    <!-- Show Selected Package (instead of dropdown) -->
                    <div x-show="showSelectedPackage" x-transition
                        class="p-3 bg-green-50 border border-green-300 rounded-lg text-green-800 font-medium">
                        Selected Package:
                        <span class="font-semibold" x-text="selectedPackageName"></span>
                    </div>

                    <!-- Share Buttons -->
                    <div class="grid grid-cols-2 gap-4 pt-2">

                        <!-- Email -->
                        <button @click="sendEmail()"
                            class="flex items-center justify-center gap-2 px-4 py-3
                    bg-blue-600 text-white rounded-xl shadow
                    hover:bg-blue-700 hover:shadow-xl transition">
                            <i class="fa-solid fa-envelope"></i> Email
                        </button>

                        <!-- WhatsApp -->
                        <button @click="sendWhatsApp()"
                            class="flex items-center justify-center gap-2 px-4 py-3
                    bg-green-600 text-white rounded-xl shadow
                    hover:bg-green-700 hover:shadow-xl transition">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp
                        </button>

                    </div>

                </div>
            </div>
        </div>


    </div>

    <script>
        function followupModal() {
            return {

                /* ---------------- FOLLOW-UP MODAL ---------------- */
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
                    'Work with other company', 'Not Interested', 'Interested', 'Wrong Information',
                    'Not Pickup', 'Other'
                ],

                openFollowModal(id, name) {
                    this.leadId = id;
                    this.leadName = name;
                    this.open = true;

                    fetch(`/leads/${id}/details`)
                        .then(res => res.json())
                        .then(data => {
                            this.phoneNumber = data.phone.phone_number;
                            this.phoneCode = data.phone.phone_code;
                            this.fullNumber = data.phone.full_number;

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
                },

                /* ---------------- SHARE MODAL ---------------- */
                shareOpen: false,
                shareLeadId: '',
                shareLeadName: '',
                selectedPackage: '',
                showDropdown: false,
                showSelectedPackage: false, // NEW
                selectedPackageName: '', // NEW
                allPackages: @json($packages),

                handleShare(id, name, packageId = null) {
                    this.shareLeadId = id;
                    this.shareLeadName = name;

                    // Lead already has package
                    if (packageId) {
                        this.selectedPackage = packageId;
                        this.showDropdown = false;
                        this.showSelectedPackage = true;

                        // Find package name
                        const pkg = this.allPackages.find(p => p.id == packageId);
                        this.selectedPackageName = pkg ? pkg.package_name : 'Unknown Package';

                        this.shareOpen = true;
                        return;
                    }

                    // Lead has NO package
                    this.showSelectedPackage = false;
                    this.showDropdown = true;
                    this.shareOpen = true;

                    // Auto-select first package
                    if (this.allPackages.length > 0) {
                        this.selectedPackage = this.allPackages[0].id;
                    }
                },

                closeShare() {
                    this.shareOpen = false;
                },




            }
        }
    </script>
</x-app-layout>
