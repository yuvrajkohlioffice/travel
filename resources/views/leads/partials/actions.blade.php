<a href="{{ route('leads.show', $lead->id) }}" 
   class="px-3 py-1 rounded text-gray-700 hover:bg-gray-200 transition text-sm ml-1">
   <i class="fa-solid fa-eye"></i>
</a>

<a href="{{ route('leads.assign.form', $lead->id) }}" 
   class="px-3 py-1 rounded text-gray-700 hover:bg-gray-200 transition text-sm ml-1">
   <i class="fa-solid fa-user-plus"></i>
</a>

<form action="{{ route('leads.destroy', $lead->id) }}" method="POST" 
      onsubmit="return confirm('Delete this lead?')" class="inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="px-3 py-1 rounded text-gray-700 hover:bg-gray-200 transition text-sm ml-1">
        <i class="fa-solid fa-trash"></i>
    </button>
</form>
