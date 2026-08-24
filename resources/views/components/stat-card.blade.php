@props(['title', 'value', 'icon', 'color' => 'blue'])

@php
$bgColors = [
    'blue' => 'bg-blue-50',
    'purple' => 'bg-purple-50',
    'green' => 'bg-green-50',
    'yellow' => 'bg-yellow-50',
    'emerald' => 'bg-emerald-50',
    'red' => 'bg-red-50',
];

$textColors = [
    'blue' => 'text-blue-600',
    'purple' => 'text-purple-600',
    'green' => 'text-green-600',
    'yellow' => 'text-yellow-600',
    'emerald' => 'text-emerald-600',
    'red' => 'text-red-600',
];
@endphp

<x-card>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $value }}</p>
        </div>
        <div class="text-4xl">{{ $icon }}</div>
    </div>
</x-card>
