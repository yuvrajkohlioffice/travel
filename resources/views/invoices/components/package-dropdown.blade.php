<div>
    <label class="font-semibold">Select Package *</label>

    <select x-model="selectedPackage" @change="loadPackage"
        class="w-full p-3 rounded-xl border bg-white">
        <option value="">Select Package</option>

        @foreach ($packages as $p)
            <option value="{{ $p->id }}">{{ $p->package_name }}</option>
        @endforeach
    </select>
</div>
