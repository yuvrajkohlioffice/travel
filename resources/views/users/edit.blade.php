<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-lg">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                        {{ isset($user) ? 'Edit User' : 'Add User' }}
                    </h2>
                    <a href="{{ route('users.index') }}"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" 
                          method="POST" class="space-y-5" autocomplete="off">
                        @csrf
                        @if(isset($user)) @method('PUT') @endif

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                required>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                required>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Password</label>
                            <input type="password" name="password" placeholder="{{ isset($user) ? 'Leave blank to keep current password' : '' }}"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                {{ isset($user) ? '' : 'required' }}>
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Role</label>
                            <select name="role_id" required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ (old('role_id', $user->role_id ?? '') == $role->id) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Company -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Company</label>
                            <select name="company_id"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" {{ (old('company_id', $user->company_id ?? '') == $company->id) ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- WhatsApp API Key -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">WhatsApp API Key</label>
                            <input type="text" name="whatsapp_api_key" value="{{ old('whatsapp_api_key', $user->whatsapp_api_key ?? '') }}"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                            <select name="status" required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-800 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="1" {{ (old('status', $user->status ?? '') == 1) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ (old('status', $user->status ?? '') == 0) ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Submit -->
                        <div>
                            <button type="submit"
                                class="w-full px-4 py-3 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">
                                {{ isset($user) ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
