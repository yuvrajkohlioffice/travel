@php
    $lead = $invoice->lead;
    $agent = $lead->createdBy ?? null;
    $company = $agent->company ?? null;
    $logoUrl = $company->logo ?? null;
@endphp

<div class="w-full bg-blue-50 border-b border-blue-200 p-6 md:p-10">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start gap-6">
            
            {{-- LEFT SIDE: Company / Agent Info --}}
            <div class="w-full md:w-3/5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                {{-- Logo Box --}}
                <div class="flex-shrink-0">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" 
                             alt="Logo" 
                             class="rounded-lg shadow-sm" 
                             style="height: 60px; width: auto; max-width: 150px; object-fit: contain;">
                    @else
                        <div class="h-16 w-16 bg-white rounded-lg border border-blue-100 flex items-center justify-center text-blue-300">
                            <span class="text-2xl font-bold">C</span>
                        </div>
                    @endif
                </div>

                {{-- Company Text --}}
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 leading-tight">
                        {{ $company->company_name ?? 'Travel Agency' }}
                    </h1>
                    
                    <div class="text-sm text-gray-600 mt-2 space-y-1">
                        @if ($company->email ?? false)
                            <div class="flex items-center">
                                <i class="fas fa-envelope mr-2 text-blue-500 w-4"></i>
                                <span>{{ $company->email }}</span>
                            </div>
                        @endif
                        
                        @if ($company->phone ?? false)
                            <div class="flex items-center">
                                <i class="fas fa-phone mr-2 text-blue-500 w-4"></i>
                                <span>{{ $company->phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE: Invoice Details --}}
            <div class="w-full md:w-2/5 text-left md:text-right">
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 uppercase tracking-widest mb-3 md:mb-4">
                    INVOICE
                </h2>
                
                <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-4 inline-block w-full md:w-auto">
                    <table class="w-full border-separate border-spacing-y-1">
                        <tr>
                            <td class="text-gray-500 text-left md:text-right pr-3 font-medium text-xs uppercase">Invoice No:</td>
                            <td class="font-bold text-gray-800 text-right">{{ $invoice->invoice_no }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 text-left md:text-right pr-3 font-medium text-xs uppercase">Date:</td>
                            <td class="font-bold text-gray-800 text-right">{{ $invoice->formatted_issue_date }}</td>
                        </tr>
                        <tr>
                            <td class="text-green-600 text-left md:text-right pr-3 font-bold text-xs uppercase">Travel Date:</td>
                            <td class="font-bold text-gray-800 text-right">{{ $invoice->formatted_travel_start_date }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>