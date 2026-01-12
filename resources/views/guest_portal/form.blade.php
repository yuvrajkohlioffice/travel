<x-guest-layout>
    <script src="//unpkg.com/alpinejs" defer></script>

    <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        
        <div class="max-w-4xl mx-auto">
            
            {{-- Header --}}
            <div class="text-center mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">Traveler Details</h2>
                <p class="mt-2 text-gray-600">Please fill in the details for the primary contact and all accompanying travelers.</p>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                
                {{-- Form Start --}}
                <form action="{{ route('guest.update', $lead->id) }}" method="POST" 
                      x-data="guestForm()" 
                      x-init="initData()"
                      @submit="prepareSubmit">
                    
                    @csrf

                    <div class="p-6 md:p-8 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Primary Traveler</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name *</label>
                                <input type="text" name="primary_full_name" x-model="primaryName" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address *</label>
                                <input type="email" name="primary_email" x-model="primaryEmail" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number *</label>
                                <input type="text" name="primary_phone" x-model="primaryPhone" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Address *</label>
                                <input type="text" name="primary_address" x-model="primaryAddress" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>

                        </div>
                    </div>

                    <div class="p-6 md:p-8 bg-gray-50">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Additional Travelers</h3>
                            
                            <button type="button" @click="addTraveler()" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                + Add Traveler
                            </button>
                        </div>

                        <input type="hidden" name="additional_travelers" :value="jsonTravelers">

                        <template x-if="additionalTravelers.length === 0">
                            <div class="text-center py-6 text-gray-500 italic">
                                No additional travelers added. Click the button above to add family or friends.
                            </div>
                        </template>

                        <div class="space-y-4">
                            <template x-for="(t, index) in additionalTravelers" :key="index">
                                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Name</label>
                                            <input type="text" x-model="t.name" placeholder="Full Name" required
                                                class="mt-1 w-full p-2 border border-gray-300 rounded-md text-sm">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Relation</label>
                                            <select x-model="t.relation" class="mt-1 w-full p-2 border border-gray-300 rounded-md text-sm">
                                                <option value="">Select Relation</option>
                                                <option value="Spouse">Spouse</option>
                                                <option value="Child">Child</option>
                                                <option value="Parent">Parent</option>
                                                <option value="Friend">Friend</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <div class="flex-grow">
                                                <label class="block text-xs font-bold text-gray-500 uppercase">Age</label>
                                                <input type="number" x-model="t.age" min="0" placeholder="Age"
                                                    class="mt-1 w-full p-2 border border-gray-300 rounded-md text-sm">
                                            </div>
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="p-6 md:p-8 border-t border-gray-200">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Special Requests / Notes</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500">{{ $invoice->additional_details ?? $lead->notes }}</textarea>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 text-right sm:px-8 border-t border-gray-200">
                        <button type="submit" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full sm:w-auto">
                            Save & Update Details
                        </button>
                    </div>

                </form>
            </div>

            <div class="mt-6 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Tourism Company. All rights reserved.
            </div>
        </div>
    </div>

    <script>
        function guestForm() {
            return {
                // Initialize with PHP Data
                primaryName: @json($invoice->primary_full_name ?? $lead->name),
                primaryEmail: @json($invoice->primary_email ?? $lead->email),
                primaryPhone: @json($invoice->primary_phone ?? $lead->phone_number),
                primaryAddress: @json($invoice->primary_address ?? $lead->address),
                
                // Load existing additional travelers or empty array
                additionalTravelers: @json($invoice->additional_travelers ?? []),

                // Computed property for JSON string to send to backend
                get jsonTravelers() {
                    return JSON.stringify(this.additionalTravelers);
                },

                // Functions
                initData() {
                    // Ensure it is an array if null came from DB
                    if (!this.additionalTravelers) {
                        this.additionalTravelers = [];
                    }
                },

                addTraveler() {
                    this.additionalTravelers.push({
                        name: '',
                        relation: 'Adult',
                        age: ''
                    });
                },

                removeTraveler(index) {
                    this.additionalTravelers.splice(index, 1);
                },

                prepareSubmit() {
                    // Optional: Clean up data before submit if needed
                    // console.log('Submitting:', this.jsonTravelers);
                }
            }
        }
    </script>
</x-guest-layout>