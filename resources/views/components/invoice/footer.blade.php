<div class="p-6 md:p-8 lg:p-10 bg-gradient-to-r from-blue-900 to-blue-800 text-white text-center">
    <p class="text-blue-300">This is a computer-generated invoice. No signature is required.</p>
    <p class="text-blue-300 text-sm mt-2">
        Invoice ID: {{ $invoice->invoice_no }} | Generated: {{ $invoice->created_at }}
    </p>
</div>
