<div class="grid grid-cols-2 gap-4">
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
<div>
        <label class="font-semibold">Select Vehicle / Item *</label>
        <select x-model="selectedItem" class="w-full p-3 border rounded-xl">
            <template x-for="item in packageItems" :key="item.id">
                <option :value="item.id" x-text="item.vehicle_name"></option>
            </template>
        </select>
    </div>
</div>