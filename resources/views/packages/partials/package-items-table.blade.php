@foreach ($package->packageItems as $index => $item)
<tr class="border-b dark:border-gray-700">
    <td class="p-3">{{ $index + 1 }}</td>
    <td class="p-3">{{ $item->car->name ?? '—' }}</td>
    <td class="p-3">{{ $item->person_count }}</td>
    <td class="p-3">{{ $item->vehicle_name }}</td>
    <td class="p-3">{{ $item->room_count }}</td>
    <td class="p-3">₹{{ number_format($item->standard_price) }}</td>
    <td class="p-3">₹{{ number_format($item->deluxe_price) }}</td>
    <td class="p-3">₹{{ number_format($item->luxury_price) }}</td>
    <td class="p-3">₹{{ number_format($item->premium_price) }}</td>
    <td class="p-3 text-center">
        <button class="px-4 py-1 bg-indigo-600 text-white rounded-lg edit-item"
                                        data-id="{{ $item->id }}">Edit</button>
                                    <button class="px-4 py-1 bg-red-600 text-white rounded-lg delete-item"
                                        data-id="{{ $item->id }}">
                                        Delete
                                    </button>
    </td>
</tr>
@endforeach
