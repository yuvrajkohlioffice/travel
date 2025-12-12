<x-app-layout>
    <div class="ml-64 min-h-screen p-6 bg-gray-100 dark:bg-gray-900">

        <div class="w-full ">

            {{-- Header --}}
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                    Add Company
                </h1>

                <a href="{{ route('companies.index') }}"
                   class="flex items-center text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>

            {{-- Form Card --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 transition hover:shadow-lg">
                <form action="{{ route('companies.store') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="space-y-8">
                    @csrf

                    {{-- GRID START --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Company Name --}}
                        <div>
                            <label class="form-label">Company Name</label>
                            <input 
                                type="text" 
                                name="company_name"
                                class="custom-inputs"
                                placeholder="Enter company name"
                                required
                            >
                        </div>

                        {{-- Team Name --}}
                        <div>
                            <label class="form-label">Team Name</label>
                            <input 
                                type="text" 
                                name="team_name"
                                class="custom-inputs"
                                placeholder="Enter team name"
                            >
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="form-label">Email</label>
                            <input 
                                type="email" 
                                name="email"
                                class="custom-inputs"
                                placeholder="Enter company email"
                            >
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="form-label">Phone Number</label>
                            <input 
                                type="text" 
                                name="phone"
                                class="custom-inputs"
                                placeholder="Enter phone number"
                            >
                        </div>

                        {{-- Owner --}}
                        <div>
                            <label class="form-label">Select Owner</label>
                            <select name="owner_id" class="custom-inputs" required>
                                <option value="">Choose Owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- WhatsApp API Key --}}
                        <div>
                            <label class="form-label">WhatsApp API Key</label>
                            <input
                                type="text"
                                name="whatsapp_api_key"
                                class="custom-inputs"
                                placeholder="Enter API key"
                            >
                        </div>

                        {{-- Logo Upload --}}
                        <div class="md:col-span-2">
                            <label class="form-label">Company Logo (400 x 160) Size</label>
                            <input 
                                type="file" 
                                name="logo"
                                accept="image/*"
                                class="custom-inputs"
                                onchange="previewLogo(event)"
                            >
                            <img id="logoPreview" class="mt-3 w-32 h-32 rounded-xl object-cover hidden border" />
                        </div>

                        {{-- Scanner Image --}}
                        <div class="md:col-span-2">
                            <label class="form-label">QR Image (201 x 199) Size</label>
                            <input 
                                type="file" 
                                name="scanner_image"
                                accept="image/*"
                                class="custom-inputs"
                                onchange="previewScanner(event)"
                            >

                            <img id="scannerPreview" class="mt-3 w-32 h-32 rounded-xl object-cover hidden border" />

                            <label class="form-label mt-4">UPI ID</label>
                            <input
                                type="text"
                                name="scanner_upi_id"
                                class="custom-inputs"
                                placeholder="Enter UPI ID"
                            >
                        </div>

                    </div>
                    {{-- GRID END --}}

                    {{-- Bank Details --}}        
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        <div>
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_details[bank_name]" class="custom-inputs">
                        </div>

                        <div>
                            <label class="form-label">Account Name</label>
                            <input type="text" name="bank_details[account_name]" class="custom-inputs">
                        </div>

                        <div>
                            <label class="form-label">Account Number</label>
                            <input type="text" name="bank_details[account_number]" class="custom-inputs">
                        </div>

                        <div>
                            <label class="form-label">IFSC Code</label>
                            <input type="text" name="bank_details[ifsc]" class="custom-inputs">
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="pt-4">
                        <button 
                            type="submit"
                            class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-xl shadow-sm hover:shadow-md transition"
                        >
                            <i class="fas fa-save"></i>
                            Save Company
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    {{-- Preview JS --}}
    <script>
        function previewLogo(event) {
            let img = document.getElementById('logoPreview');
            img.src = URL.createObjectURL(event.target.files[0]);
            img.classList.remove('hidden');
        }

        function previewScanner(event) {
            let img = document.getElementById('scannerPreview');
            img.src = URL.createObjectURL(event.target.files[0]);
            img.classList.remove('hidden');
        }
    </script>


</x-app-layout>
