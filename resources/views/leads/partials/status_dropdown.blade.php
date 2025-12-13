@php
    $stageClass =
        [
            'Pending' => 'bg-lime-500 text-white',
            'Approved' => 'bg-green-500 text-white',
            'Quotation Sent' => 'bg-indigo-500 text-white',
            'Follow-up Taken' => 'bg-purple-500 text-white',
            'Converted' => 'bg-teal-500 text-white',
            'Lost' => 'bg-gray-500 text-white',
            'On Hold' => 'bg-amber-500 text-white',
            'Rejected' => 'bg-red-500 text-white',
            'In Progress' => 'bg-emerald-600 text-white',
        ][$lead->status] ?? 'bg-gray-300 text-black font-extrabold';

    $statuses = [
        'Pending',
        'Approved',
        'Quotation Sent',
        'In Progress',
        'Follow-up Taken',
        'Lost',
        'Converted',
        'On Hold',
        'Rejected',
    ];
@endphp

<div x-data="{ open: false, value: '{{ $lead->status }}' }" class="relative">
    <div x-show="!open" @click="open = true" class="cursor-pointer text-xs px-2 py-1 rounded {{ $stageClass }}">
        <span x-text="value || 'Select Status'"></span>
    </div>
    <select x-show="open" x-cloak
        @change="value = $event.target.value; open=false; updateStatus({{ $lead->id }}, value);"
        @click.outside="open=false" class="px-2 py-1 rounded text-xs border bg-white dark:bg-gray-800">
        <option value="">Select Status</option>
        @foreach ($statuses as $status)
            <option value="{{ $status }}" {{ $lead->status === $status ? 'selected' : '' }}>{{ $status }}
            </option>
        @endforeach
    </select>
</div>
