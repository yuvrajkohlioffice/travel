<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Package Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            background: #f3f4f6;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .card {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .banner {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        h1 {
            color: #2563eb;
            font-size: 22px;
            margin-bottom: 12px;
        }

        p {
            color: #374151;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .document-btn {
            display: inline-block;
            padding: 10px 14px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 28px;
            color: #9ca3af;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div
        style="max-width:600px;margin:auto;background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.08);font-family:Arial,sans-serif;">

        {{-- Show banner image if image --}}
        @if (!empty($documentUrls) && preg_match('/\.(jpg|jpeg|png|webp)$/i', $documentUrls[0]))
            <img src="{{ $documentUrls[0] }}" style="width:100%;border-radius:8px;margin-bottom:20px;" alt="Banner">
        @endif

        <h2>Hello {{ $leadName }},</h2>

        <p>{!! nl2br(e($bodyText)) !!}</p>

        {{-- List attachments (PDF/Docs) --}}
        @if (!empty($documentUrls) && !preg_match('/\.(jpg|jpeg|png|webp)$/i', $documentUrls[0]))
            <p><strong>Attached Documents:</strong></p>
            <ul>
                @foreach ($documentUrls as $url)
                    <li><a href="{{ $url }}" target="_blank">{{ basename($url) }}</a></li>
                @endforeach
            </ul>
        @endif

        <p style="font-size:13px;color:#999;margin-top:20px;">This is an automated message from Trekos. Please do not
            reply.</p>

    </div>


</body>

</html>
