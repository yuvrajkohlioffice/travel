<x-app-layout>
    <div class="ml-64"> <!-- ADD THIS to shift content right -->

        <x-slot name="header">
            <h2 class="text-xl font-semibold">Edit User</h2>
        </x-slot>

        <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ $user->name }}" class="w-full mt-1 p-2 border rounded"
                        required>
                </div>

                <div class="mb-4">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ $user->email }}"
                        class="w-full mt-1 p-2 border rounded" required>
                </div>

                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Update
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
