@props(['title','value','icon','color','subtitle','link'])

<a href="{{ $link ?? '#' }}" class="block">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $title }}</p>
        <div class="flex items-center justify-between mt-2">
            <div>
                <p class="text-2xl font-semibold text-gray-800 dark:text-gray-100">{{ $value }}</p>
                @if($subtitle)
                    <p class="text-xs text-gray-400 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="text-2xl {{ $color ?? 'text-indigo-600 dark:text-indigo-300' }}">
                <i class="{{ $icon }}" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</a>
