<x-app-layout>
    <div class="ml-64 p-6 bg-gray-100 dark:bg-gray-900 min-h-screen">

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">

          
<div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Manage Cars & Hotels — {{ $package->package_name }}</h2>
                <a href="{{ route('packages.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    ← Back
                </a>
            </div>
            <!-- Success message -->
            @if (session('success'))
                <div id="success-message"
                    class="mb-4 p-4 text-green-800 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Table -->
            <div class="p-6 overflow-x-auto">
                <x-data-table id="package-items-table" :headers="['#', 'Car', 'Person', 'Vehicle', 'Room', 'Std', 'Deluxe', 'Luxury', 'Premium', 'Actions']" title="Package Items List"
                    resourceName="Package Items">

                    @foreach ($package->packageItems as $index => $item)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->car->car_type ?? '—' }}</td>
                            <td class="text-center">{{ $item->person_count }}</td>
                            <td>{{ $item->vehicle_name }}</td>
                            <td class="text-center">{{ $item->room_count }}</td>
                            <td>₹{{ number_format($item->standard_price) }}</td>
                            <td>₹{{ number_format($item->deluxe_price) }}</td>
                            <td>₹{{ number_format($item->luxury_price) }}</td>
                            <td>₹{{ number_format($item->premium_price) }}</td>
                            <td class="text-center space-x-2 flex justify-center">
                                <!-- Edit -->
                                <button
                                    class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 edit-item flex items-center gap-1"
                                    data-id="{{ $item->id }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    Edit
                                </button>

                                <!-- Delete -->
                                <form action="{{ route('packages.item.delete', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                </x-data-table>
            </div>

            <!-- Add Item -->
            <button id="add-new" class="mb-4 px-4 py-2 bg-green-600 text-white rounded-lg">+ Add Item</button>

            <!-- Form -->
            <div id="form-container" class="hidden mt-6">
                <form id="package-form" method="POST" action="{{ route('packages.update-relations', $package->id) }}">
                    @csrf
                    <div id="item-form-content" class="space-y-6"></div>
                    <!-- Reload Page Button -->


                    <button type="submit" onclick="setTimeout(() => { window.location.reload(); }, 1000)"
                        class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg">Save Items</button>
                </form>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formContainer = document.getElementById('form-container');
            const itemFormContent = document.getElementById('item-form-content');
            const packageForm = document.getElementById('package-form');
            const tbody = document.getElementById('package-items-tbody');
            let currentIndex = 0;

            // Delete
            function bindDeleteButtons() {
                document.querySelectorAll('.delete-item').forEach(btn => {
                    btn.onclick = function() {
                        const itemId = this.dataset.id;
                        if (!confirm('Are you sure to delete?')) return;

                        fetch(`/packages/item/${itemId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content'),
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }).then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.closest('tr').remove();
                                    alert(data.message);
                                } else alert(data.message);
                            }).catch(err => console.error(err));
                    }
                });
            }

            // Edit
            function bindEditButtons() {
                document.querySelectorAll('.edit-item').forEach(btn => {
                    btn.onclick = function() {
                        const itemId = this.dataset.id;
                        formContainer.classList.remove('hidden');

                        packageForm.action = `/packages/item/${itemId}`;
                        packageForm.method = 'POST';

                        let methodInput = packageForm.querySelector('input[name="_method"]');
                        if (!methodInput) {
                            methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'PUT';
                            packageForm.appendChild(methodInput);
                        } else {
                            methodInput.value = 'PUT';
                        }

                        fetch(`/packages/partial-item-row?item_id=${itemId}`)
                            .then(res => res.text())
                            .then(html => {
                                itemFormContent.innerHTML = html;
                                itemFormContent.querySelector('div').setAttribute('data-item-id',
                                    itemId);
                            }).catch(err => console.error(err));
                    }
                });
            }

            // Single submit handler
            packageForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitButton = packageForm.querySelector('button[type="submit"]');
                submitButton.disabled = true; // prevent multiple clicks

                const formData = new FormData(packageForm);
                fetch(packageForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        submitButton.disabled = false;

                        if (data.success) {
                            // Update table
                            if (tbody) tbody.innerHTML = data.html;

                            // Hide form
                            itemFormContent.innerHTML = '';
                            formContainer.classList.add('hidden');

                            // Show success message
                            let msgDiv = document.getElementById('success-message');
                            if (!msgDiv) {
                                msgDiv = document.createElement('div');
                                msgDiv.id = 'success-message';
                                msgDiv.className =
                                    'mb-4 p-4 text-green-800 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200';
                                if (tbody) tbody.parentNode.parentNode.insertBefore(msgDiv, tbody
                                    .parentNode);
                            }
                            msgDiv.textContent = data.message;
                            setTimeout(() => msgDiv.remove(), 3000);
                        }
                    })
                    .catch(err => {
                        submitButton.disabled = false;
                        console.error(err);
                    });
            });

            // Add new
            document.getElementById('add-new').onclick = function() {
                formContainer.classList.remove('hidden');

                packageForm.action = "{{ route('packages.update-relations', $package->id) }}";
                packageForm.method = 'POST';
                const methodInput = packageForm.querySelector('input[name="_method"]');
                if (methodInput) methodInput.remove();

                const index = currentIndex++;
                fetch(`/packages/partial-item-row?index=${index}`)
                    .then(res => res.text())
                    .then(html => {
                        const wrapper = document.createElement('div');
                        wrapper.innerHTML = html;
                        itemFormContent.appendChild(wrapper.firstElementChild);
                    }).catch(err => console.error(err));
            };

            bindDeleteButtons();
            bindEditButtons();
        });
    </script>
</x-app-layout>
