<!DOCTYPE html>
<html>

<head>
    <title>Payment Receipt</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Payment Received</h2>
    <p>Dear {{ $payment->invoice->client_name ?? 'Client' }},</p>

    <p>We have received a payment of <strong>₹{{ number_format($payment->paid_amount, 2) }}</strong> for Invoice
        #{{ $payment->invoice->invoice_no }}.</p>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Transaction ID:</strong></td>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $payment->transaction_id ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Payment Date:</strong></td>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $payment->created_at->format('d M Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Payment Method:</strong></td>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                {{ ucwords($payment->paymentMethod->name ?? 'N/A') }}
            </td>
        </tr>
    </table>

    <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin-top: 20px;">
        <p style="margin: 0;"><strong>Remaining Balance:</strong> ₹{{ number_format($remaining, 2) }}</p>
        @if ($remaining > 0 && $payment->next_payment_date)
            <p style="margin: 5px 0 0 0; font-size: 0.9em; color: #666;">
                Next Payment Due: {{ \Carbon\Carbon::parse($payment->next_payment_date)->format('d M Y') }}
            </p>
        @endif
    </div>

    <p style="margin-top: 30px;">Thank you for your business.</p>
</body>

</html>
