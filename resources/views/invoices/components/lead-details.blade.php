@if ($lead)
<div class="bg-white rounded-xl p-4 shadow">
    <p class="font-semibold">Lead: {{ $lead->name }}</p>
    <p>Email: {{ $lead->email ?? '---' }}</p>
    <p>Phone: {{ $lead->phone_code . $lead->phone_number ?? '---' }}</p>
    <p>Address: {{ $lead->address ?? '---' }}</p>
</div>
@endif
