<a href="{{ route('leads.show', $lead->id) }}"
    class="p-1 border rounded text-gray-700">
    <i class="fa-solid fa-eye"></i>
</a>

<a href="{{ route('leads.assign.form', $lead->id) }}"
    class="p-1 border rounded text-gray-700">
    <i class="fa-solid fa-user-plus"></i>
</a>

<button onclick="deleteLead({{ $lead->id }})"
    class="p-1 border rounded text-red-600">
    <i class="fa-solid fa-trash"></i>
</button>
