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
user List 

<div class="relative inline-block text-left">
  <div>
    <button
      type="button"
      class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      id="menu-button"
      aria-expanded="true"
      aria-haspopup="true"
      onclick="toggleDropdown()"
    >
      Options
      <!-- Heroicon name: solid/chevron-down -->
      <svg
        class="-mr-1 ml-2 h-5 w-5"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
          fill-rule="evenodd"
          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
          clip-rule="evenodd"
        />
      </svg>
    </button>
  </div>

  <div
    class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
    role="menu"
    aria-orientation="vertical"
    aria-labelledby="menu-button"
    tabindex="-1"
    id="dropdown-menu"
  >
    <div class="py-1" role="none">
        @foreach ($users as $user )
      <a
        href="#"
        class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100"
        role="menuitem"
        tabindex="-1"
        id="menu-item-0"
        value="{{ $user->id }}"
        >{{ $user->name }}</a
      >
      @endforeach
    </div>
  </div>
</div>

<script>
  function toggleDropdown() {
    const dropdown = document.getElementById('dropdown-menu');
    dropdown.classList.toggle('hidden');
  }
</script>


    

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



                                    <button @click="openInvoiceModal({{ $lead->id }}, '{{ $lead->name }}')"
                                        class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </button>



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
        <x-followup-modal :packgaes="$packages" />
        <x-share-modal :packgaes="$packages" />
        <x-invoice-modal :packgaes="$packages" />
    </div>
    <script>
        function invoiceModal() {
            return {
                open: false,
                leadId: "",
                leadName: "",
                packages: @json($packages),
                selectedPackage: "",
                packageData: null,

                openInvoiceModal(id, name) {
                    this.leadId = id;
                    this.leadName = name;
                    this.open = true;
                },


            }
        }
    </script>
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
