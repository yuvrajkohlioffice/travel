<x-app-layout>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 p-6">
        <div class="max-w-4xl mx-auto">
            
            {{-- Header --}}
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-4 mb-4 md:mb-0">
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                        Edit Company
                    </h1>
                </div>

                <a href="{{ route('companies.index') }}" 
                   class="flex items-center text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>

            {{-- Form Card --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 transition hover:shadow-lg">
                <form action="{{ route('companies.update', $company->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Company Name --}}
                    <div>
                        <label for="company_name"
                               class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                            Company Name
                        </label>
                        <input 
                            type="text"
                            name="company_name"
                            id="company_name"
                            value="{{ old('company_name', $company->company_name) }}"
                            required
                            class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 
                                   bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-sm 
                                   focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                        >
                    </div>

                    {{-- Owner --}}
                    <div>
                        <label for="owner_id"
                               class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                            Owner
                        </label>
                        <select 
                            name="owner_id"
                            id="owner_id"
                            required
                            class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 
                                   bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-sm 
                                   focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                        >
                            <option value="">Select Owner</option>

                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ $company->owner_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Team Name --}}
                    <div>
                        <label for="team_name"
                               class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                            Team Name
                        </label>
                        <input 
                            type="text"
                            name="team_name"
                            id="team_name"
                            value="{{ old('team_name', $company->team_name) }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 
                                   bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-sm 
                                   focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                        >
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <button 
                            type="submit"
                            class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 
                                   text-white font-semibold px-6 py-2 rounded-xl shadow-sm hover:shadow-md transition"
                        >
                            <i class="fas fa-save"></i>
                            Update
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
