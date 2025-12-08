<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gradient-to-br from-gray-100 via-white to-gray-200">

        <div class="w-full max-w-5xl space-y-6">

            <!-- Header -->
            <div class="flex justify-between items-center bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-xl shadow-lg">
                <h2 class="text-3xl font-bold">Lead Details: {{ $lead->name }}</h2>
                <a href="{{ route('leads.index') }}" class="px-5 py-2 bg-white text-blue-600 font-semibold rounded-lg shadow hover:bg-gray-100 transition">
                    Back
                </a>
            </div>

            <!-- Lead Info -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-800 dark:text-gray-200">
                <div class="space-y-2">
                    <p><strong>Company:</strong> {{ $lead->company_name }}</p>
                    <p><strong>Email:</strong> <a href="mailto:{{ $lead->email }}" class="text-blue-600 hover:underline">{{ $lead->email }}</a></p>
                    <p><strong>Phone:</strong> <span class="text-green-600 font-mono">+{{ $lead->phone_code }} {{ $lead->phone_number }}</span></p>
                    <p><strong>Country:</strong> {{ $lead->country }}</p>
                    <p><strong>City:</strong> {{ $lead->city }}</p>
                    <p><strong>District:</strong> {{ $lead->district }}</p>
                </div>
                <div class="space-y-2">
                    <p><strong>Client Category:</strong> {{ $lead->client_category }}</p>
                    <p><strong>Status:</strong>
                        @php
                            $statusColors = ['Hot' => 'bg-red-500', 'Warm' => 'bg-yellow-400', 'Cold' => 'bg-gray-400', 'Interested' => 'bg-green-500'];
                            $statusClass = $statusColors[$lead->lead_status] ?? 'bg-gray-300';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-white {{ $statusClass }}">{{ $lead->lead_status }}</span>
                    </p>
                    <p><strong>Source:</strong> {{ $lead->lead_source }}</p>
                    <p><strong>Website:</strong> <a href="{{ $lead->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $lead->website }}</a></p>
                    <p><strong>Inquiry For:</strong> {{ $lead->package->package_name ?? $lead->inquiry_text }}</p>
                    <p><strong>Created By:</strong> {{ $lead->createdBy->name ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Assigned Users -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Assigned Users</h3>
                <ul class="list-disc pl-5 space-y-1 text-gray-700 dark:text-gray-300">
                    @php $user = auth()->user(); @endphp
                    @foreach ($lead->assignedUsers as $assignment)
                        @if ($user->role_id == 1 || $assignment->assigned_by == $user->id || $assignment->user_id == $user->id)
                            <li class="flex justify-between items-center">
                                {{ $assignment->user->name ?? 'N/A' }}
                                <span class="text-sm text-gray-500">(Assigned By {{ $assignment->assignedBy->name ?? 'N/A' }})</span>
                            </li>
                        @endif
                    @endforeach
                    @if ($lead->assignedUsers->isEmpty())
                        <li class="text-gray-500">No assignments found.</li>
                    @endif
                </ul>
            </div>

            <!-- Follow-ups -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 overflow-x-auto">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Follow-ups</h3>
                <table class="w-full border-collapse border text-left">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="p-2 border">Date</th>
                            <th class="p-2 border">Reason</th>
                            <th class="p-2 border">Remark</th>
                            <th class="p-2 border">Next Followup</th>
                            <th class="p-2 border">Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lead->followups as $follow)
                            @if ($user->role_id == 1 || $follow->user_id == $user->id)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="p-2 border">{{ $follow->created_at->format('d-M-Y H:i') }}</td>
                                    <td class="p-2 border">{{ $follow->reason }}</td>
                                    <td class="p-2 border">{{ $follow->remark }}</td>
                                    <td class="p-2 border">{{ $follow->next_followup_date ? \Carbon\Carbon::parse($follow->next_followup_date)->format('d-M-Y') : '-' }}</td>
                                    <td class="p-2 border">{{ $follow->user->name ?? 'N/A' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        @if ($lead->followups->isEmpty())
                            <tr>
                                <td class="p-2 border text-gray-500 text-center" colspan="5">No follow-ups found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Followup Views (Admin Only) -->
            @if ($user->role_id == 1)
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 overflow-x-auto">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Followup Views</h3>
                <table class="w-full border-collapse border text-left">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="p-2 border">User</th>
                            <th class="p-2 border">Count</th>
                            <th class="p-2 border">View Timeline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $groupedViews = $lead->views->groupBy('user_id'); @endphp
                        @foreach ($groupedViews as $userId => $userViews)
                            @php $viewer = $userViews->first()->user; $sortedViews = $userViews->sortBy('viewed_at'); @endphp
                            @if ($user->role_id == 1 || $userId == $user->id)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="p-2 border">{{ $viewer->name }}</td>
                                    <td class="p-2 border">{{ $userViews->count() }}</td>
                                    <td class="p-2 border">
                                        <ul class="list-disc pl-5">
                                            @foreach ($sortedViews as $view)
                                                <li>{{ \Carbon\Carbon::parse($view->viewed_at)->format('d-M-Y H:i') }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @if ($lead->views->isEmpty())
                            <tr>
                                <td class="p-2 border text-gray-500 text-center" colspan="3">No views found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
