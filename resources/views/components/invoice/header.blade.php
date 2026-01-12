@php
    // Extract related data safely
    $lead = $invoice->lead;
    $agent = $lead->createdBy ?? null;
    $company = $agent->company ?? null;
@endphp

<div class="p-6 md:p-8 lg:p-10 border-b border-blue-100 bg-gradient-to-r from-blue-50 to-cyan-50 print:bg-none">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">

        {{-- Company Info --}}
        <div class="flex items-center mb-8 md:mb-0">
            <div class="h-20 w-20 rounded-2xl bg-white flex items-center justify-center mr-5 shadow-lg overflow-hidden">
                @if($company && $company->logo)
                    <img src="{{ $company->logo }}" alt="Company Logo" class="h-full w-full object-contain p-2" />
                @else
                    <i class="fas fa-building text-3xl text-gray-300"></i>
                @endif
            </div>
            
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
                    {{ $company->company_name ?? 'Travel Agency' }}
                </h1>
                
                <div class="flex flex-col md:flex-row md:items-center mt-2 gap-2 md:gap-4">
                    @if($company->email ?? false)
                        <p class="text-gray-600 text-sm md:text-base">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i> {{ $company->email }}
                        </p>
                    @endif
                    
                    @if($company->phone ?? false)
                        <p class="text-gray-600 text-sm md:text-base">
                            <i class="fas fa-phone mr-2 text-blue-500"></i> {{ $company->phone }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Invoice Info --}}
        <div class="text-left md:text-right mt-4 md:mt-0 w-full md:w-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 uppercase">Travel Invoice</h2>
            
            <div class="mt-4 bg-white p-4 rounded-xl shadow-sm inline-block text-left w-full md:w-64">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-blue-600 font-semibold">Invoice #:</span>
                    <span class="font-bold text-gray-800">{{ $invoice->invoice_no }}</span>
                </div>
                
                <div class="flex justify-between items-center mb-2">
                    <span class="text-blue-600 font-semibold">Issue Date:</span>
                    <span class="font-medium text-gray-600">
                        {{ \Carbon\Carbon::parse($invoice->issued_date)->format('d M, Y') }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center border-t pt-2 mt-1">
                    <span class="text-green-600 font-semibold">Travel Start:</span>
                    <span class="font-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($invoice->travel_start_date)->format('d M, Y') }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>