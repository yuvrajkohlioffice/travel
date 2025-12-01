<x-app-layout>
    <div class="ml-64">

        <x-slot name="header">
            <h2 class="text-xl font-semibold">Add User</h2>
        </x-slot>

        <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow">

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="font-medium">Name</label>
                    <input type="text" name="name" class="w-full mt-1 p-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label class="font-medium">Email</label>
                    <input type="email" name="email" class="w-full mt-1 p-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label class="font-medium">Password</label>
                    <input type="password" name="password" class="w-full mt-1 p-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label class="font-medium">Role</label>
                    <select name="role_id" class="w-full mt-1 p-2 border rounded" required>
                        <option value="">Select Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Save
                </button>
            </form>

        </div>
    </div>
</x-app-layout>
