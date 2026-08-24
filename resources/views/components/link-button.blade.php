@props(['type' => 'primary'])

@if ($type === 'primary')
    <a {{ $attributes->merge(['class' => 'inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2']) }}>
        {{ $slot }}
    </a>
@elseif ($type === 'secondary')
    <a {{ $attributes->merge(['class' => 'inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2']) }}>
        {{ $slot }}
    </a>
@elseif ($type === 'danger')
    <a {{ $attributes->merge(['class' => 'inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2']) }}>
        {{ $slot }}
    </a>
@else
    <a {{ $attributes->merge(['class' => 'inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-300 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2']) }}>
        {{ $slot }}
    </a>
@endif
