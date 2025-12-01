@props(['name'])

@if($name === 'home')
<svg {{ $attributes }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
<path d="M3 12l2-2 7-7 7 7M5 10v10h3m8 0h3V10"></path>
</svg>
@endif

@if($name === 'users')
<svg {{ $attributes }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
<path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2a4 4 0 100-8 4 4 0 000 8z"/>
</svg>
@endif
