@php
    // Find the current status object from the collection to get its initial color
    $currentStatusObj = $leadStatuses->firstWhere('name', $lead->status);
    $initialColor = $currentStatusObj?->color ?? 'bg-gray-400 text-white';
@endphp

<div 
    x-data="{ 
        open: false, 
        currentStatus: '{{ $lead->status }}',
        currentColor: '{{ $initialColor }}',
        
        changeStatus(name, color) {
            this.currentStatus = name;
            this.currentColor = color;
            this.open = false;
            
            // Call your global AJAX function
            updateStatus({{ $lead->id }}, name); 
        }
    }" 
    class="relative"
    @click.outside="open = false"
>
    {{-- 1. THE TRIGGER (BADGE) --}}
    <button 
        @click="open = !open"
        class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm flex items-center gap-2 transition-all duration-200 hover:opacity-90 hover:scale-105 active:scale-95 w-full justify-center whitespace-nowrap"
        :class="currentColor"
    >
        <span x-text="currentStatus"></span>
        <i class="fa-solid fa-chevron-down text-[10px] opacity-70"></i>
    </button>

    {{-- 2. THE CUSTOM DROPDOWN MENU --}}
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute left-0 top-full mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden"
        style="display: none;"
    >
        <div class="py-1">
            @foreach ($leadStatuses as $status)
                <button 
                    @click="changeStatus('{{ $status->name }}', '{{ $status->color }}')"
                    class="w-full text-left px-4 py-2.5 text-xs font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center gap-3 group"
                >
                    {{-- Color Dot Indicator --}}
                    <span class="w-2.5 h-2.5 rounded-full {{ $status->color }} ring-1 ring-inset ring-black/10"></span>
                    
                    {{-- Status Name --}}
                    <span class="text-gray-700 dark:text-gray-200 group-hover:text-gray-900 dark:group-hover:text-white">
                        {{ $status->name }}
                    </span>

                    {{-- Checkmark if active (Visual Logic) --}}
                    <span x-show="currentStatus === '{{ $status->name }}'" class="ml-auto text-blue-600">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </button>
            @endforeach
        </div>
    </div>
</div>