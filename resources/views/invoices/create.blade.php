<x-app-layout>

<div class="ml-64 flex justify-center items-start min-h-screen p-6 bg-gray-100 dark:bg-gray-900">
    <div class="max-w-6xl w-full p-4 md:p-6 lg:p-8">

        <form 
    method="POST"
    action="{{ route('invoices.store') }}"
    x-data="invoiceGenerator({
        invoice: window.__invoice,
        lead: window.__lead,
        packageItems: window.__packageItems
    })"
    x-init="init()"
    class="space-y-6"
>

            @csrf

            <!-- Hidden fields -->
            <template x-for="(value, key) in hiddenFields" :key="key">
                <input type="hidden" :name="key" :value="value">
            </template>

            @include('invoices.components.lead-details')
            @include('invoices.components.package-dropdown')
            @include('invoices.components.package-item-dropdown')
            @include('invoices.components.traveler-inputs')
            @include('invoices.components.primary-traveler')
            @include('invoices.components.discount-date')
            @include('invoices.components.additional-details')
            @include('invoices.components.invoice-preview')

            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700">
                Generate Invoice
            </button>

        </form>

    </div>
</div>

<script src="{{ asset('js/invoice.js') }}"></script>
<script>
    window.__invoice = @json($invoice);
    window.__lead = @json($lead);
    window.__packageItems = @json($packageItems);
</script>

</x-app-layout>
