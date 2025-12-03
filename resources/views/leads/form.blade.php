<div class="grid grid-cols-2 gap-4">
    <!-- Name -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
        <input type="text" name="name"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            value="{{ old('name', $lead->name ?? '') }}" required>
    </div>
    <!-- Company Name -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Company Name</label>
        <input type="text" name="company_name"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            value="{{ old('company_name', $lead->company_name ?? '') }}">
    </div>
    <!-- Email -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
        <input type="email" name="email"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            value="{{ old('email', $lead->email ?? '') }}">
    </div>
    <!-- Country -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Country</label>
        <input type="text" name="country"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            value="{{ old('country', $lead->country ?? '') }}">
    </div>
    <!-- District -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">District</label>
        <input type="text" name="district"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            value="{{ old('district', $lead->district ?? '') }}">
    </div>

    <!-- Phone Code -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Phone Code</label>
        <input type="text" name="phone_code"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            value="{{ old('phone_code', $lead->phone_code ?? '') }}">
    </div>

    <!-- Phone Number -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Phone Number</label>
        <input type="text" name="phone_number"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            value="{{ old('phone_number', $lead->phone_number ?? '') }}">
    </div>

    <!-- City -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">City</label>
        <input type="text" name="city"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            value="{{ old('city', $lead->city ?? '') }}">
    </div>
    <!-- Client Category -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Client Category</label>
        <input type="text" name="client_category"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            value="{{ old('client_category', $lead->client_category ?? '') }}">
    </div>

    <!-- Lead Status -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Lead Status</label>
        <select name="lead_status"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">Select Status</option>
            <option value="Hot" {{ old('lead_status', $lead->lead_status ?? '') == 'Hot' ? 'selected' : '' }}>Hot
            </option>
            <option value="Warm" {{ old('lead_status', $lead->lead_status ?? '') == 'Warm' ? 'selected' : '' }}>Warm
            </option>
            <option value="Cold" {{ old('lead_status', $lead->lead_status ?? '') == 'Cold' ? 'selected' : '' }}>Cold
            </option>
        </select>
    </div>

    <!-- Lead Source -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Lead Source</label>
        <input type="text" name="lead_source"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            value="{{ old('lead_source', $lead->lead_source ?? '') }}">
    </div>

    <!-- Website -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Website</label>
        <input type="text" name="website"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            value="{{ old('website', $lead->website ?? '') }}">
    </div>

    <!-- Package -->
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Select Package (Optional)</label>
        <select name="package_id"
            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">-- No Package --</option>
            @foreach ($packages as $package)
                <option value="{{ $package->id }}"
                    {{ old('package_id', $lead->package_id ?? '') == $package->id ? 'selected' : '' }}>
                    {{ $package->package_name }}
                </option>
            @endforeach
        </select>
    </div>

</div>

<!-- Inquiry Text -->
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Inquiry Text</label>
    <textarea name="inquiry_text" rows="4"
        class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('inquiry_text', $lead->inquiry_text ?? '') }}</textarea>
</div>

<!-- Submit Button -->
