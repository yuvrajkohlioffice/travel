<div class="font-medium flex items-center gap-2">
    {{ $lead->name }}
    <span class="px-2 py-0.5 rounded text-white font-extrabold 
        {{ ['Hot'=>'bg-red-500','Warm'=>'bg-yellow-500','Cold'=>'bg-gray-400'][$lead->lead_status] ?? 'bg-gray-300' }}">
        {{ $lead->lead_status }}
    </span>

    <button onclick="openEditModal({{ $lead->id }})"
        class="text-gray-600 hover:text-black">
        <i class="fa-solid fa-pen-to-square"></i>
    </button>
</div>

<a href="mailto:{{ $lead->email }}" class="text-xs text-gray-700 hover:underline">
    {{ $lead->email }}
</a>

<div class="text-gray-600 text-sm font-mono">
    +{{ $lead->phone_code }} {{ $maskedPhone }}
</div>

<div class="text-gray-500 text-xs">
    {{ $lead->created_at->format('d-M-y') }}
</div>
