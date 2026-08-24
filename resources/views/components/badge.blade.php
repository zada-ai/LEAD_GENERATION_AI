@props(['status' => 'new', 'size' => 'md'])

@php
$colors = [
    'new' => 'bg-blue-100 text-blue-800',
    'contacted' => 'bg-purple-100 text-purple-800',
    'interested' => 'bg-green-100 text-green-800',
    'follow_up' => 'bg-yellow-100 text-yellow-800',
    'converted' => 'bg-emerald-100 text-emerald-800',
    'not_interested' => 'bg-red-100 text-red-800',
    'do_not_contact' => 'bg-gray-100 text-gray-800',
    // Conversation statuses
    'open' => 'bg-blue-100 text-blue-800',
    'waiting' => 'bg-yellow-100 text-yellow-800',
    'replied' => 'bg-green-100 text-green-800',
    'closed' => 'bg-gray-100 text-gray-800',
];

$sizes = [
    'sm' => 'px-2 py-1 text-xs',
    'md' => 'px-3 py-1 text-sm',
    'lg' => 'px-4 py-2 text-base',
];
@endphp

<span class="inline-flex items-center rounded-full font-medium {{ $colors[$status] ?? $colors['new'] }} {{ $sizes[$size] ?? $sizes['md'] }}">
    {{ $slot }}
</span>
