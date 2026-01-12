@php
    // Extract related data safely
    $lead = $invoice->lead;
    $agent = $lead->createdBy ?? null;
    $company = $agent->company ?? null;
@endphp

<div class="p-6 md:p-8 lg:p-10 border-b border-blue-100 bg-gradient-to-r from-blue-50 to-cyan-50">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        {{-- Company Info --}}
        <div class="flex items-center mb-8 md:mb-0">
            <div class="h-20 w-20 rounded-2xl bg-white flex items-center justify-center mr-5 shadow-lg">
                @if ($company && $company->logo)
                    <img src="{{ $company->logo }}" alt="Company Logo" class="w-full " />
                @else
                    <i class="fas fa-building text-3xl text-gray-300"></i>
                @endif
            </div>
            <div>
                <h1 class="text-4xl font-bold text-gray-900">{{ $company->company_name ?? 'Travel Agency' }}</h1>
                <div class="flex flex-wrap items-center mt-2">
                    @if ($company->email ?? false)
                        <p class="text-gray-600 mr-4">

                            <i class="fas fa-envelope mr-2 text-blue-500"></i> {{ $company->email }}
                        </p>
                    @endif
                    @if ($company->phone ?? false)
                        <p class="text-gray-600">
                            <i class="fas fa-phone mr-2 text-blue-500"></i> {{ $company->phone }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        {{-- Invoice Info --}}
        <div class="text-right">
            <h2 class="text-4xl font-bold text-gray-900 uppercase">Travel Invoice</h2>
            <div class="mt-4 space-y-2 bg-white p-4 rounded-xl shadow-sm inline-block">
                <p class="font-semibold"><span class="text-blue-600">Invoice #:</span> {{ $invoice->invoice_no }}</p>
                <p class="font-semibold"><span class="text-blue-600">Issue Date:</span>
                    {{ $invoice->formatted_issue_date }}</p>
                <p class="font-semibold"><span class="text-green-600">Travel Start:</span>
                    {{ $invoice->formatted_travel_start_date }}</p>
            </div>
        </div>
    </div>
</div>
