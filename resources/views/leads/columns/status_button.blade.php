<div x-data="{ open: false, value: '{{ $lead->status }}' }" class="relative">
    <div x-show="!open" @click="open = true"
        class="cursor-pointer text-xs px-2 py-1 rounded bg-gray-200">
        <span x-text="value"></span>
    </div>

    <select x-show="open" x-cloak
        @change="value=$event.target.value;open=false;updateStatus({{ $lead->id }},value)"
        @click.outside="open=false"
        class="px-2 py-1 rounded text-xs border bg-white">
        <option value="Pending">Pending</option>
        <option value="Approved">Approved</option>
        <option value="Quotation Sent">Quotation Sent</option>
        <option value="Follow-up Taken">Follow-up Taken</option>
        <option value="Lost">Lost</option>
        <option value="Converted">Converted</option>
        <option value="On Hold">On Hold</option>
        <option value="Rejected">Rejected</option>
    </select>
</div>
