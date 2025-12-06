<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="font-semibold">Full Name *</label>
        <input type="text" x-model="primaryName" class="w-full p-3 border rounded-xl">
    </div>
    <div>
        <label class="font-semibold">Email *</label>
        <input type="email" x-model="primaryEmail" class="w-full p-3 border rounded-xl">
    </div>
    <div>
        <label class="font-semibold">Phone *</label>
        <input type="text" x-model="primaryPhone" class="w-full p-3 border rounded-xl">
    </div>
    <div>
        <label class="font-semibold">Address *</label>
        <input type="text" x-model="primaryAddress" class="w-full p-3 border rounded-xl">
    </div>
</div>
<!-- ADDITIONAL TRAVELERS -->
<div class="mt-6 bg-white p-4 rounded-xl border">
    <div class="flex justify-between items-center mb-3">
        <h3 class="font-semibold text-lg">Additional Travelers</h3>
        <button type="button" :disabled="!canAddTraveler()" @click="addTraveler()"
            class="px-3 py-1 bg-blue-600 text-white rounded-lg">
            + Add Traveler
        </button>

    </div>

    <!-- TRAVELER LIST -->
    <template x-for="(t, index) in additionalTravelers" :key="index">
        <div class="p-3 border rounded-xl mb-3 bg-gray-50">
            <div class="grid grid-cols-3 gap-3">

                <!-- Name -->
                <div>
                    <label class="text-sm font-semibold">Name</label>
                    <input type="text" x-model="t.name" class="w-full p-2 border rounded-lg">
                </div>

                <!-- Relation -->
                <div>
                    <label class="text-sm font-semibold">Relation</label>
                    <select x-model="t.relation" class="w-full p-2 border rounded-lg">
                        <option value="">Select</option>
                        <option>Spouse</option>
                        <option>Child</option>
                        <option>Parent</option>
                        <option>Friend</option>
                        <option>Other</option>
                    </select>
                </div>

                <!-- Age -->
                <div>
                    <label class="text-sm font-semibold">Age</label>
                    <input type="number" min="0" x-model="t.age" class="w-full p-2 border rounded-lg">
                </div>

            </div>

            <!-- REMOVE BUTTON -->
            <button type="button" @click="removeTraveler(index)" class="mt-2 text-red-600 text-sm">
                Remove
            </button>
        </div>
    </template>

    <!-- TOTAL COUNT -->
    <p class="font-semibold mt-2">
        Total Travelers: <span x-text="totalTravelers"></span>
    </p>
</div>
