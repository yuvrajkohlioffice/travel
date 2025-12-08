<div class="text-xs text-gray-700">
    <div><strong>Assigned:</strong> {{ $lead->latestAssignedUser->user->name ?? 'N/A' }}</div>
    <div><strong>By:</strong> {{ $lead->latestAssignedUser->assignedBy->name ?? 'N/A' }}</div>
    <div><strong>Created:</strong> {{ $lead->createdBy->name ?? 'System' }}</div>
</div>
