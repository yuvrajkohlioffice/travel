<x-app-layout>
    <div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gradient-to-br from-gray-100 via-white to-gray-200">

        <div class="w-full max-w-3xl space-y-6">

            <!-- Header -->
            <div class="flex justify-between items-center bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-4 rounded-xl shadow-lg">
                <h2 class="text-2xl font-semibold">Edit Lead</h2>
                <a href="{{ route('leads.index') }}"
                    class="inline-flex items-center text-white font-medium hover:opacity-80 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back
                </a>
            </div>

            <!-- Form Card -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6">
                <form method="POST" action="{{ route('leads.update', $lead->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Include the reusable form fields -->
                    @include('leads.form')

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full mt-2 px-4 py-3 text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">
                        Update Lead
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
