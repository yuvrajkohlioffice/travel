<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-lg">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Assign Lead: {{ $lead->name }}</h2>
                    <a href="{{ route('leads.index') }}"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back
                    </a>
                </div>
                <div class="p-6">
                    <!-- Already assigned users with delete option given  -->
                    @if ($assignedUsers->count() > 0)
                        <div class="mb-6">
                            <h3 class="font-semibold mb-2">Already Assigned Users:</h3>
                            <ul
                                class="list-disc pl-5 text-gray-700 dark:text-gray-300 space-y-1 max-h-40 overflow-y-auto">
                                @foreach ($assignedUsers as $assigned)
                                    <li class="flex items-center justify-between">
                                        <span>
                                            {{ $assigned->user->name }}
                                            (Assigned by: {{ $assigned->assignedBy->name ?? 'N/A' }},
                                            {{ $assigned->created_at->format('d M Y') }})
                                        </span>
                                        <form action="{{ route('leads.assign.delete', $assigned->id) }}" method="POST"
                                            class="ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Assign form with checkboxes and not Super Admin will show here or whom 
                        Role_id 
                        is 1 those also not shown here -->
                    <form action="{{ route('leads.assign.store', $lead->id) }}" method="POST" class="space-y-4"
                        autocomplete="off">
                        @csrf

                        <div>
                            <label class="block font-semibold mb-1">Select Users</label>

                            @if ($users->count() > 0)
                                <div class="border rounded p-2 max-h-60 overflow-y-auto bg-gray-50 dark:bg-gray-800">
                                    @foreach ($users as $user)
                                        <div class="flex items-center ">
                                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                                id="user_{{ $user->id }}"
                                                class="mr-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <label for="user_{{ $user->id }}"
                                                class="text-gray-800 dark:text-gray-200">
                                                {{ $user->name }} ({{ $user->email }})
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">
                                    No users available to assign. You are already assigned to a user or need to change
                                    the user
                                    role from Super Admin.
                                </p>
                            @endif
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Assign Lead
                        </button>
                    </form>
                </div>
            </div>
        </div>
</x-app-layout>
