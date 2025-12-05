<div>
    <label class="font-semibold">Select Vehicle / Item *</label>
    <select x-model="selectedItem" class="w-full p-3 rounded-xl border bg-white">
        <option value="">Select Item</option>
        <template x-for="item in packageItems" :key="item.id">
            <option :value="item.id" x-text="item.vehicle_name"></option>
        </template>
    </select>
</div>

<div>
    <label class="font-semibold">Package Type *</label>
    <select x-model="selectedRoomType" class="w-full p-3 rounded-xl border bg-white">
        <option value="standard_price">Standard</option>
        <option value="deluxe_price">Deluxe</option>
        <option value="luxury_price">Luxury</option>
        <option value="premium_price">Premium</option>
    </select>
</div>