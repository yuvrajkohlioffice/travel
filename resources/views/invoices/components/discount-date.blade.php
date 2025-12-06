<div class="grid grid-cols-2 gap-4 mb-4">
    <!-- Discount -->
    <div>
        <label class="font-semibold">Discount (%)</label>
        <input type="number" min="0" max="100" x-model.number="discountPercent" 
               @input="recalculate()" class="w-full p-3 border rounded-xl">
    </div>

    <!-- Travel Start Date -->
    <div>
        <label class="font-semibold">Travel Start Date</label>
        <input type="date" x-model="travelStartDate" @input="recalculate()" 
               class="w-full p-3 border rounded-xl">
    </div>

    <!-- Tax -->
    <div>
        <label class="font-semibold">Tax (%)</label>
        <input type="number" min="0" max="28" x-model.number="taxPercent" 
               @input="recalculate()" class="w-full p-3 border rounded-xl">
    </div>

   <!-- Price per Person -->
<div>
    <label class="font-semibold">Price per Person</label>
   <input type="number" min="0" x-model.number="manualBasePrice" 
       @input="recalculate()" class="w-full p-3 border rounded-xl">

</div>

</div>