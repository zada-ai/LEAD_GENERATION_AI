@props(['route', 'icon', 'label'])

@php
$isActive = request()->routeIs($route);
@endphp

<a 
    href="{{ route($route) }}"
    class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors {{ $isActive ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}"
>
    <span class="text-lg">{{ $icon }}</span>
    <span class="text-sm font-medium">{{ $label }}</span>
</a>
