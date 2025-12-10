<h3>Follow-up Reminder</h3>

<p>Lead: {{ $followup->lead->name ?? 'N/A' }} ({{ $followup->lead->phone_number ?? '' }})</p>
<p>Reason: {{ $followup->reason }}</p>
<p>Remark: {{ $followup->remark }}</p>
<p>Next Follow-up Date: {{ $followup->next_followup_date ? $followup->next_followup_date->format('d-M-Y') : 'N/A' }}</p>
<p>User: {{ $followup->user->name }}</p>
