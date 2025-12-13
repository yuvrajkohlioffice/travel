@php
    $leadJson = htmlspecialchars(json_encode([
        'id' => $lead->id,
        'name' => $lead->name,
        'email' => $lead->email,
        'phone_code' => $lead->phone_code,
        'phone_number' => $lead->phone_number,
        'package_id' => $lead->package_id ?? null,
        'people_count' => $lead->people_count ?? 1,
        'child_count' => $lead->child_count ?? 0,
    ], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');

    $packageId = $lead->package->id ?? '';
    $invoice = $lead->invoice;
    $paymentButton = '';

    if($invoice) {
        $totalPaid = DB::table('payments')->where('invoice_id', $invoice->id)->sum('paid_amount');
        $remainingAmount = max(($invoice->final_price ?? 0) - $totalPaid, 0);

        if($remainingAmount > 0) {
            $invoiceJson = htmlspecialchars(json_encode([
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no ?? '',
                'amount' => $invoice->final_price,
                'remaining_amount' => $remainingAmount
            ], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');

            $paymentButton = <<<HTML
            <button
                @click='openPaymentModal({$invoiceJson})'
                class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700 ml-1"
            >
                <i class="fa-solid fa-money-bill-wave"></i> Add Payment
            </button>
            HTML;
        }
    }
@endphp

<button
    @click='handleShare({{ $leadJson }})'
    class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm"
>
    <i class="fa-solid fa-share"></i>
</button>

<button
    @click="openInvoiceModal({{ $lead->id }}, '{{ $lead->name }}', '{{ $lead->people_count }}', '{{ $lead->child_count }}', '{{ $packageId }}', '{{ $lead->email }}')"
    class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm ml-1"
>
    <i class="fa-solid fa-file-invoice"></i>
</button>

{!! $paymentButton !!}
