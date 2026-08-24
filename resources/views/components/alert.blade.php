@props(['type' => 'info'])

@php
$classes = [
    'info' => 'bg-blue-50 border border-blue-200 text-blue-800',
    'success' => 'bg-green-50 border border-green-200 text-green-800',
    'warning' => 'bg-yellow-50 border border-yellow-200 text-yellow-800',
    'error' => 'bg-red-50 border border-red-200 text-red-800',
];

$iconClasses = [
    'info' => 'text-blue-400',
    'success' => 'text-green-400',
    'warning' => 'text-yellow-400',
    'error' => 'text-red-400',
];
@endphp

<div class="rounded-lg p-4 {{ $classes[$type] ?? $classes['info'] }}">
    <div class="flex gap-3">
        <div class="flex-shrink-0 {{ $iconClasses[$type] ?? $iconClasses['info'] }} text-xl">
            @if ($type === 'success')
                ✓
            @elseif ($type === 'error')
                ✕
            @elseif ($type === 'warning')
                ⚠
            @else
                ℹ
            @endif
        </div>
        <div class="flex-1">
            {{ $slot }}
        </div>
    </div>
</div>
