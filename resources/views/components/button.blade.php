@props(['active' => false])

<button {{ $attributes->merge(['class' => 'inline-flex items-center justify-center px-4 py-2 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 ' . (
    $active 
    ? 'bg-blue-600 text-white focus:ring-blue-500' 
    : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-blue-500'
)]) }}>
    {{ $slot }}
</button>
