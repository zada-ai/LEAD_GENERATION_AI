@props(['priority' => 'medium'])

@php
$colors = [
    'high' => 'bg-red-100 text-red-800',
    'medium' => 'bg-yellow-100 text-yellow-800',
    'low' => 'bg-blue-100 text-blue-800',
];
@endphp

<span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $colors[$priority] ?? $colors['medium'] }}">
    {{ $slot }}
</span>
