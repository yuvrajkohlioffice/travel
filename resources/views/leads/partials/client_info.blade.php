@php
    $phone = $lead->phone_number ?? '';
    $visibleDigits = 3;
    $maxMaskLength = 5;
    $maskLength = min(strlen($phone) - $visibleDigits, $maxMaskLength);
    $maskedPhone = str_repeat('*', $maskLength) . substr($phone, -$visibleDigits);

    $followup = $lead->latestFollowup;
    $followupText = '';
    if ($followup) {
        $today = now()->startOfDay();
        $nextDate = \Carbon\Carbon::parse($followup->next_followup_date)->startOfDay();
        if ($nextDate->isPast() && !$nextDate->isToday()) {
            $daysLate = $nextDate->diffInDays($today);
            $followupText = '<span class="text-white font-bold">Last followup expired: ' . $daysLate . ' days ago</span>';
        }
    }

    $statusClass = [
        'Hot' => 'bg-red-500',
        'Warm' => 'bg-yellow-400',
        'Cold' => 'bg-gray-400',
        'paid'=> 'bg-green-500',
        'Interested' => 'bg-green-500',
    ][$lead->lead_status] ?? 'bg-gray-300 text-black font-extrabold';

    $createdDate = \Carbon\Carbon::parse($lead->created_at)->startOfDay();
    $days = $createdDate->diffInDays(now()->startOfDay());
    $daysText = match($days) {
        0 => 'Today',
        1 => '1 day ago',
        default => $days . ' days ago',
    };
@endphp

<div class="font-xc flex items-center gap-2">
    {{ $lead->name }}
</div>

<div class="text-gray-600 text-sm font-mono mb-1">
    +{{ $lead->phone_code }} {{ $maskedPhone }} | 
    <span class="py-0.5 badge-custom rounded text-white font-bold {{ $statusClass }}">
        {{ $lead->lead_status ?? 'N/A' }}
    </span> | 
    <button @click="openEditModal({{ $lead->id }})" class="text-gray-600 hover:text-black">
        <i class="fa-solid fa-pen-to-square"></i>
    </button>
</div>

<div class="text-gray-500">
    <span class="text-xs text-black">Created At {{ $lead->created_at->format('d-M-y') }} </span> |
    <span class="bg-gray-500 text-black font-extrabold badge-custom">{{ $daysText }}</span>
</div>

@if($followupText)
    <div class="text-xs font-semibold mt-1 badge-custom bg-red-600">
        <i class="fa-solid fa-clock-rotate-left mr-1"></i> {!! $followupText !!}
    </div>
@endif
