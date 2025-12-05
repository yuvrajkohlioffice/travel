<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="font-semibold">Discount (%)</label>
        <input type="number" min="0" max="100" x-model.number="discountPercent" class="w-full p-3 border rounded-xl">
    </div>
    <div>
        <label class="font-semibold">Travel Start Date</label>
        <input type="date" x-model="travelStartDate" class="w-full p-3 border rounded-xl">
    </div>

    <div>
        <label>Tax %</label>
        <input type="number" min="0" max="28" x-model="taxPercent" class="w-full p-3 rounded-xl border" />
    </div>
</div>

