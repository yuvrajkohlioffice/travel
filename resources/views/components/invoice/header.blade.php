@php
    // Extract related data safely
    $lead = $invoice->lead;
    $agent = $lead->createdBy ?? null;
    $company = $agent->company ?? null;
    
    // Fallback logo if missing (Optional)
    $logoUrl = $company->logo ?? null;
@endphp


<div class="w-full bg-blue-50 border-b border-blue-200" style="padding: 40px;">
    <table class="w-full" width="100%" cellpadding="0" cellspacing="0" style="border: none;">
        <tr>
            {{-- LEFT SIDE: Company / Agent Info --}}
            <td class="align-top" style="width: 60%; vertical-align: top;">
                <div class="inline-block align-middle mr-4">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" 
                                 alt="Logo" 
                                 class="rounded-lg shadow-sm" 
                                 style="height: 60px; width: auto; max-width: 150px; object-fit: contain;">
                        @else
                            {{-- Placeholder Icon if no logo --}}
                            <div class="h-16 w-16 bg-white rounded-lg border border-blue-100 flex items-center justify-center text-blue-300">
                                <span style="font-size: 24px; font-weight: bold;">C</span>
                            </div>
                        @endif
                    </div>
                <div class="flex items-center">
                    {{-- Logo Box --}}
                    

                    {{-- Company Text --}}
                    <div class="inline-block align-middle mt-2">
                        <h1 class="text-2xl font-bold text-gray-800 leading-tight">
                            {{ $company->company_name ?? 'Travel Agency' }}
                        </h1>
                        
                        <div class="text-sm text-gray-600 mt-2 space-y-1">
                            @if ($company->email ?? false)
                                <div class="mb-1">
                                    <span class="text-blue-500 font-bold"><i class="fas fa-envelope mr-2 text-blue-500"></i></span> {{ $company->email }}
                                </div>
                            @endif
                            
                            @if ($company->phone ?? false)
                                <div>
                                    <span class="text-blue-500 font-bold"><i class="fas fa-phone mr-2 text-blue-500"></i></span> {{ $company->phone }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </td>

            {{-- RIGHT SIDE: Invoice Details --}}
            <td class="align-top text-right" style="width: 40%; vertical-align: top; text-align: right;">
                <h2 class="text-5xl font-extrabold text-dark uppercase tracking-widest" style="line-height: 0.8; margin-bottom: 10px;">
                    INVOICE
                </h2>
                
                <div class="bg-white rounded-lg shadow-sm border border-blue-100 p-3 inline-block text-right text-sm">
                    <table style="text-align: right; width: 100%;">
                        <tr>
                            <td class="text-gray-500 pr-3 font-medium">Invoice No:</td>
                            <td class="font-bold text-gray-800">{{ $invoice->invoice_no }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-500 pr-3 font-medium">Date:</td>
                            <td class="font-bold text-gray-800">{{ $invoice->formatted_issue_date }}</td>
                        </tr>
                        <tr>
                            <td class="text-green-600 pr-3 font-bold">Travel Date:</td>
                            <td class="font-bold text-gray-800">{{ $invoice->formatted_travel_start_date }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</div>