<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-4xl">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        Lead Details: {{ $lead->name }}
                    </h2>
                    <a href="{{ route('leads.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        Back
                    </a>
                </div>

                <!-- Lead Info -->
                <div class="p-6 grid grid-cols-2 gap-6 text-gray-800 dark:text-gray-200">
                    <div>
                        <p><strong>Company:</strong> {{ $lead->company_name }}</p>
                        <p><strong>Email:</strong> {{ $lead->email }}</p>
                        <p><strong>Phone:</strong> +{{ $lead->phone_code }} {{ $lead->phone_number }}</p>
                        <p><strong>Country:</strong> {{ $lead->country }}</p>
                        <p><strong>City:</strong> {{ $lead->city }}</p>
                        <p><strong>District:</strong> {{ $lead->district }}</p>
                    </div>
                    <div>
                        <p><strong>Client Category:</strong> {{ $lead->client_category }}</p>
                        <p><strong>Status:</strong> {{ $lead->lead_status }}</p>
                        <p><strong>Source:</strong> {{ $lead->lead_source }}</p>
                        <p><strong>Website:</strong> {{ $lead->website }}</p>
                        <p><strong>Inquiry For:</strong> {{ $lead->package->package_name ?? $lead->inquiry_text }}</p>
                        <p><strong>Created By:</strong> {{ $lead->createdBy->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Assigned Users -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Assigned Users</h3>
                    <ul class="list-disc pl-5 space-y-1 text-gray-700 dark:text-gray-300">
                        @php $user = auth()->user(); @endphp
                        @foreach ($lead->assignedUsers as $assignment)
                            @if ($user->role_id == 1 || $assignment->assigned_by == $user->id || $assignment->user_id == $user->id)
                                <li>
                                    {{ $assignment->user->name ?? 'N/A' }}
                                    <span class="text-sm text-gray-500">(Assigned By
                                        {{ $assignment->assignedBy->name ?? 'N/A' }})</span>
                                </li>
                            @endif
                        @endforeach
                        @if ($lead->assignedUsers->isEmpty())
                            <li class="text-gray-500">No assignments found.</li>
                        @endif
                    </ul>
                </div>

                <!-- Follow-ups -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Follow-ups</h3>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700">
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
                                    <tr class="border-b">
                                        <td class="p-2 border">{{ $follow->created_at->format('d-M-Y H:i') }}</td>
                                        <td class="p-2 border">{{ $follow->reason }}</td>
                                        <td class="p-2 border">{{ $follow->remark }}</td>
                                        <td class="p-2 border">
                                            {{ $follow->next_followup_date ? \Carbon\Carbon::parse($follow->next_followup_date)->format('d-M-Y') : '-' }}
                                        </td>

                                        <td class="p-2 border">{{ $follow->user->name ?? 'N/A' }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            @if ($lead->followups->isEmpty())
                                <tr>
                                    <td class="p-2 border text-gray-500 text-center" colspan="5">No follow-ups found.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
           <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Phone Views</h3>
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-100 dark:bg-gray-700">
                <th class="p-2 border">User</th>
                <th class="p-2 border">Count</th>
                <th class="p-2 border">View Timeline</th>
            </tr>
        </thead>
        <tbody>
            @php
                $groupedViews = $lead->views->groupBy('user_id');
            @endphp

            @foreach ($groupedViews as $userId => $userViews)
                @php
                    $viewer = $userViews->first()->user;
                @endphp

                @if ($user->role_id == 1 || $userId == $user->id)
                    <tr class="border-b">
                        <td class="p-2 border">{{ $viewer->name }}</td>
                        <td class="p-2 border">{{ $userViews->count() }}</td>
                        <td class="p-2 border">
                            @php
                                // Sort views by time ascending
                                $sortedViews = $userViews->sortBy('viewed_at');
                            @endphp
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


            </div>
        </div>
    </div>
</x-app-layout>
