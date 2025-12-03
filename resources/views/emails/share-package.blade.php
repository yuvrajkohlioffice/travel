<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Package Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-6">
        <h1 class="text-2xl font-bold text-blue-600 mb-4">Hello {{ $leadName }},</h1>

        <p class="text-gray-700 mb-4">We are excited to share the details of the package <strong class="text-blue-700">{{ $packageName }}</strong> with you.</p>

        @if(count($documents) > 0)
            <p class="text-gray-700 mb-2 font-semibold">Attached Documents:</p>
            <ul class="list-disc list-inside mb-4">
                @foreach($documents as $doc)
                    <li><a href="{{ $doc }}" target="_blank" class="text-blue-600 underline">{{ basename($doc) }}</a></li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500 mb-4">No documents attached for this package.</p>
        @endif

        <p class="text-gray-700">We hope this package suits your interest. Feel free to reach out for any queries.</p>

        <div class="mt-6">
            <a href="#" class="inline-block bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700 transition">Explore More Packages</a>
        </div>

        <p class="text-gray-400 text-sm mt-6">This is an automated email from Trekos. Please do not reply directly.</p>
    </div>
</body>
</html>
