@props([
    'label' => '',
    'name' => '',
    'value' => '',
])

<div class="w-full">
    <label class="label">{{ $label }}</label>

    <input 
        type="text" 
        name="{{ $name }}" 
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'input-field']) }}
    />
</div>
