@props([
    'name',
    'label',
    'options' => [],
])

<div class="flex flex-col gap-1">
    <label class="text-sm font-medium text-gray-700">{{ $label }}</label>

    <select name="{{ $name }}"
        class="w-full border rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

        <option value="">Select {{ $label }}</option>

        @foreach ($options as $key => $value)
            @php
                // Handle both formats:
                // ['id' => 'Name'] 
                // ['Name1', 'Name2']
                $optionValue = is_numeric($key) ? $value : $key;
                $optionLabel = $value;
            @endphp

            <option value="{{ $optionValue }}"
                {{ request($name) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
</div>
