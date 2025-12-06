<div class="p-6 md:p-8 lg:p-10 border-b border-blue-100 bg-gradient-to-r from-blue-50 to-cyan-50">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">

        {{-- Company Info --}}
        <div class="flex items-center mb-8 md:mb-0">
            <div class="h-20 w-20 rounded-2xl bg-white flex items-center justify-center mr-5 shadow-lg">
                <img src="{{ asset('logo.png') }}" alt="QR Code" class="w-full " />
            </div>
            <div>
                <h1 class="text-4xl font-bold text-gray-900">DevDham Yatra</h1>
                <div class="flex flex-wrap items-center mt-2">
                    <p class="text-gray-600 mr-4">
                        <i class="fas fa-envelope mr-2 text-blue-500"></i> contact@devdhamyatra.com
                    </p>
                    <p class="text-gray-600">
                        <i class="fas fa-phone mr-2 text-blue-500"></i> +91 7818-947399
                    </p>
                </div>
            </div>
        </div>

        {{-- Invoice Info --}}
        <div class="text-right">
            <h2 class="text-4xl font-bold text-gray-900 uppercase">Travel Invoice</h2>
            <div class="mt-4 space-y-2 bg-white p-4 rounded-xl shadow-sm inline-block">
                <p class="font-semibold"><span class="text-blue-600">Invoice #:</span> {{ $invoice->invoice_no }}</p>
                <p class="font-semibold"><span class="text-blue-600">Issue Date:</span> {{ $invoice->formatted_issue_date }}</p>
                <p class="font-semibold"><span class="text-green-600">Travel Start:</span> {{ $invoice->formatted_travel_start_date }}</p>
            </div>
        </div>

    </div>
</div>
