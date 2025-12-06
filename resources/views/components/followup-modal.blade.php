<!-- Follow-Up Modal -->
<div x-show="followOpen" x-transition.opacity
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">

     
    <div @click.outside="closeFollow"
         class="bg-white dark:bg-gray-800 w-full max-w-6xl mt-16 rounded-2xl shadow-xl p-6">

        <!-- Modal Header -->
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                Follow-Up: <span class="text-blue-600" x-text="leadName"></span>
                <span class="text-green-600 ml-2" x-text="phoneNumber ? '(' + phoneNumber + ')' : ''"></span>
            </h2>
            <button @click="closeFollow" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>
        </div>
     <div class="max-h-[75vh] overflow-y-auto p-6 space-y-4 scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-100 dark:scrollbar-track-gray-700">

        <div class="grid grid-cols-12 gap-6">

            <!-- Follow-Up Form -->
            <div class="col-span-5 border rounded-xl p-6 space-y-4 shadow-sm bg-gray-50">
                <form action="{{ route('followup.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="lead_id" x-model="leadId">

                    <!-- Followup Reasons -->
                    <div class="space-y-2">
                        <label class="font-semibold text-gray-700">Followup Reason</label>
                        <template x-for="reason in reasons" :key="reason">
                            <div class="flex items-center gap-2">
                                <input type="radio" :value="reason" name="reason" x-model="selectedReason" class="h-4 w-4" required>
                                <span x-text="reason" class="text-gray-700"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Remark -->
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Remark</label>
                        <textarea name="remark" rows="3" class="w-full p-3 rounded-lg border" placeholder="Write remark here..."></textarea>
                    </div>

                    <!-- Next Followup Date & Time -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold mb-1">Next Followup Date</label>
                            <input type="date" name="next_followup_date" class="w-full p-3 rounded-lg border" required>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Time</label>
                            <input type="time" name="next_followup_time" class="w-full p-3 rounded-lg border" required>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" @click="closeFollow" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Submit
                        </button>
                    </div>
                </form>
            </div>

            <!-- Follow-Ups Table -->
            <div class="col-span-7 overflow-x-auto border rounded-xl p-4 shadow-sm bg-white">
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2 text-left">Date</th>
                            <th class="border p-2 text-left">Reason</th>
                            <th class="border p-2 text-left">Remark</th>
                            <th class="border p-2 text-left">Next Followup Date</th>
                            <th class="border p-2 text-left">Record By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- No Followups -->
                        <template x-if="followups.length === 0">
                            <tr>
                                <td colspan="5" class="text-center p-4 text-gray-500">No follow-ups found.</td>
                            </tr>
                        </template>

                        <!-- Followups List -->
                        <template x-for="(f, index) in followups" :key="index">
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="p-2 border" x-text="f.created_at"></td>
                                <td class="p-2 border" x-text="f.reason"></td>
                                <td class="p-2 border" x-text="f.remark"></td>
                                <td class="p-2 border" x-text="f.next_followup_date ?? '-'"></td>
                                <td class="p-2 border" x-text="f.user_name ?? 'N/A'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

        </div>
     </div>
    </div>
</div>
