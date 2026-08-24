@props(['icon', 'label'])

<div class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-500 cursor-not-allowed opacity-50">
    <span class="text-lg">{{ $icon }}</span>
    <span class="text-sm font-medium">{{ $label }}</span>
</div>
