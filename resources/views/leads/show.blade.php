<x-app-layout>
    <div class="ml-64 p-6 max-w-3xl">

        <h2 class="text-2xl font-bold mb-4">Lead Details</h2>

        <div class="bg-white p-4 rounded shadow">
            <p><strong>Name:</strong> {{ $lead->name }}</p>
            <p><strong>Email:</strong> {{ $lead->email }}</p>
            <p><strong>Phone:</strong> +{{ $lead->phone_code }} {{ $lead->phone_number }}</p>
            <p><strong>Package:</strong> {{ $lead->package->package_name ?? 'Custom Inquiry' }}</p>
            <p class="mt-2"><strong>Inquiry Text:</strong><br>{{ $lead->inquiry_text }}</p>
        </div>

    </div>
</x-app-layout>
