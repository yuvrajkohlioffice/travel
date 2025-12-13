<div>
    @if($lead->lastFollowup)
        <div class="text-xs text-gray-600 mt-2">
            <strong>Last:</strong> {{ $lead->lastFollowup->reason }}<br>
            <strong>By:</strong> {{ $lead->lastFollowup->user->name ?? 'N/A' }}
        </div>
    @endif

    <button 
        @click="openFollowModal({{ $lead->id }}, '{{ $lead->name }}')" 
        class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm"
    >
        Followup
    </button>
</div>
