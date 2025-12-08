<button
    @click="handleShare({{ $lead->id }}, '{{ $lead->name }}','{{ $lead->package->id ?? '' }}','{{ $lead->email }}')"
    class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm">
    <i class="fa-solid fa-share"></i>
</button>

<button
    @click="openInvoiceModal({{ $lead->id }}, '{{ $lead->name }}', '{{ $lead->people_count }}','{{ $lead->child_count }}','{{ $lead->package->id ?? '' }}','{{ $lead->email }}')"
    class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm ml-1">
    <i class="fa-solid fa-file-invoice"></i>
</button>
