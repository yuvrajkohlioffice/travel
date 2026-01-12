<x-guest-layout>
    <div class="min-h-screen bg-gray-100 flex flex-col justify-center items-center py-12 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="sm:mx-auto sm:w-full sm:max-w-3xl text-center mb-6">
            <h2 class="text-3xl font-extrabold text-gray-900">
                Welcome, {{ $lead->name }}
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Please review and complete your travel details below.
            </p>
        </div>

        <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200 shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white py-8 px-6 shadow-lg rounded-xl sm:px-10 border border-gray-100">
                
                {{-- 1. Package Info Box (Read Only) --}}
                <div class="bg-blue-50 border-l-4 border-blue-500 p-5 mb-8 rounded-r-md">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div>
                            <h3 class="text-lg font-bold text-blue-900">
                                Selected Package: {{ $package->name ?? 'Custom Package' }}
                            </h3>
                            <div class="mt-2 text-sm text-blue-800 space-y-1">
                                <p>
                                    <span class="font-semibold">Travelers:</span> 
                                    {{ $invoice->adult_count ?? $lead->adults ?? 0 }} Adults, 
                                    {{ $invoice->child_count ?? $lead->children ?? 0 }} Children
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 md:text-right">
                             <p class="text-sm font-semibold text-blue-900 bg-blue-100 px-3 py-1 rounded-full inline-block">
                                Travel Date: {{ $invoice->travel_start_date ? \Carbon\Carbon::parse($invoice->travel_start_date)->format('d M, Y') : 'Not scheduled' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- 2. The Form --}}
                <form action="{{ route('guest.update', $lead->id) }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="border-b border-gray-200 pb-2 mb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Personal Information</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-2">
                        
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" value="{{ old('name', $lead->name) }}" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="email" name="email" id="email" value="{{ old('email', $lead->email) }}" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                            </div>
                        </div>

                        {{-- Phone (Read Only - Locked) --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number (Login ID)</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="text" value="{{ $lead->phone }}" readonly
                                    class="block w-full rounded-md border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed shadow-sm focus:border-gray-300 focus:ring-0 sm:text-sm py-2.5">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Address --}}
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Address / Location <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="text" name="address" id="address" value="{{ old('address', $lead->address) }}" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                            </div>
                        </div>

                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes / Special Requests</label>
                        <div class="mt-1">
                            <textarea id="notes" name="notes" rows="4" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ $lead->notes }}</textarea>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-4">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            Save & Update My Details
                        </button>
                    </div>

                </form>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} Tourism Company. All rights reserved.</p>
            </div>
        </div>
    </div>
</x-guest-layout>