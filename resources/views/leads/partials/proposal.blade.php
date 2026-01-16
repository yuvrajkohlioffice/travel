@php
    $leadData = [
        'id' => $lead->id,
        'name' => $lead->name,
        'email' => $lead->email,
        'phone_code' => $lead->phone_code,
        'phone_number' => $lead->phone_number,
        'package_id' => $lead->package_id ?? null,
        'people_count' => $lead->people_count ?? 1,
        'child_count' => $lead->child_count ?? 0,
    ];
    $leadJson = htmlspecialchars(
        json_encode(
            [
                'id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone_code' => $lead->phone_code,
                'phone_number' => $lead->phone_number,
                'package_id' => $lead->package_id ?? null,
                'people_count' => $lead->people_count ?? 1,
                'child_count' => $lead->child_count ?? 0,
            ],
            JSON_HEX_APOS | JSON_HEX_QUOT,
        ),
        ENT_QUOTES,
        'UTF-8',
    );

    $packageId = $lead->package->id ?? '';
    $invoice = $lead->invoice;
    $paymentButton = '';

    if ($invoice) {
        $totalPaid = DB::table('payments')->where('invoice_id', $invoice->id)->sum('paid_amount');
        $remainingAmount = max(($invoice->final_price ?? 0) - $totalPaid, 0);

        if ($remainingAmount > 0) {
            $invoiceJson = htmlspecialchars(
                json_encode(
                    [
                        'id' => $invoice->id,
                        'invoice_no' => $invoice->invoice_no ?? '',
                        'amount' => $invoice->final_price,
                        'remaining_amount' => $remainingAmount,
                    ],
                    JSON_HEX_APOS | JSON_HEX_QUOT,
                ),
                ENT_QUOTES,
                'UTF-8',
            );

            $paymentButton = <<<HTML
            <button
                @click='openPaymentModal({$invoiceJson})'
                class="group px-2 py-1.5 bg-green-600 text-white rounded-md text-xs font-medium hover:bg-green-700 shadow-sm transition-all flex items-center gap-1"
            >
                <i class="fa-solid fa-money-bill-wave"></i> Add Payment
            </button>
            HTML;
        }
    }
@endphp
<div class="flex items-center justify-start gap-2 whitespace-nowrap">
<button class="group p-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-gray-600 dark:text-gray-300 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-gray-700 transition-colors shadow-sm"
    data-id="{{ $leadData['id'] }}" data-name="{{ $leadData['name'] }}" data-email="{{ $leadData['email'] }}"
    data-phone-code="{{ $leadData['phone_code'] }}" data-phone-number="{{ $leadData['phone_number'] }}"
    data-package-id="{{ $leadData['package_id'] }}" data-people-count="{{ $leadData['people_count'] }}"
    data-child-count="{{ $leadData['child_count'] }}" @click="handleShare($event)">
    <i class="fa-solid fa-share"></i>
</button>

<button
    @click="openInvoiceModal({{ $lead->id }}, '{{ $lead->name }}', '{{ $lead->people_count }}', '{{ $lead->child_count }}', '{{ $packageId }}', '{{ $lead->email }}')"
   class="group p-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors shadow-sm">
    <i class="fa-solid fa-file-invoice"></i>
</button>

{!! $paymentButton !!}
</div>