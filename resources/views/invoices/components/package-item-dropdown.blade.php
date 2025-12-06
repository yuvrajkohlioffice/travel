<div class="grid grid-cols-3 gap-4">
    
    <div>
        <label class="font-semibold">Package Type *</label>
        <select x-model="selectedRoomType" class="w-full p-3 border rounded-xl">
            <option value="standard_price">Standard</option>
            <option value="deluxe_price">Deluxe</option>
            <option value="luxury_price">Luxury</option>
            <option value="premium_price">Premium</option>
        </select>
    </div>
    <div>
        <label class="font-semibold">Adults *</label>
        <input type="number" min="0" x-model.number="adultCount" class="w-full p-3 border rounded-xl">
    </div>

    <div>
        <label class="font-semibold">Children *</label>
        <input type="number" min="0" x-model.number="childCount" class="w-full p-3 border rounded-xl">
    </div>
</div>
