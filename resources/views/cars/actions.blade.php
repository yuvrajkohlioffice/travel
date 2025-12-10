<a href="{{ route('cars.edit', $car->id) }}"
   class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 inline-flex items-center gap-1">
   <i class="fas fa-edit"></i> Edit
</a>

<form action="{{ route('cars.destroy', $car->id) }}" method="POST"
      class="inline" onsubmit="return confirm('Delete this car?')">
    @csrf
    @method('DELETE')
    <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 inline-flex items-center gap-1">
        <i class="fas fa-trash-alt"></i> Delete
    </button>
</form>
