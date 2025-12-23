@php
    $current = $leadStatuses->firstWhere('name', $lead->status);
@endphp

<div x-data="{ open: false, value: '{{ $lead->status }}' }" class="relative w-full">

    {{-- Current Status --}}
    <div
        x-show="!open"
        @click="open = true"
        class="cursor-pointer text-xs px-2 py-1 rounded flex items-center gap-1
               {{ $current?->color ?? 'bg-gray-400 text-white' }}"
    >
        @if($current?->icon)
            <i class="fa {{ $current->icon }} text-[10px]"></i>
        @endif

        <span x-text="value || 'Select Status'"></span>
    </div>

    {{-- Dropdown --}}
    <select
        x-show="open"
        x-cloak
        @change="value = $event.target.value; open = false; updateStatus({{ $lead->id }}, value)"
        @click.outside="open = false"
        class="px-2 py-1 rounded text-xs border bg-white dark:bg-gray-800 w-full"
    >
        <option value="">Select Status</option>

        @foreach ($leadStatuses as $status)
            <option
                value="{{ $status->name }}"
                {{ $lead->status === $status->name ? 'selected' : '' }}
            >
                {{ $status->name }}
            </option>
        @endforeach
    </select>
</div>
