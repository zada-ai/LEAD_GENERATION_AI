@props(['label', 'name', 'value' => null, 'type' => 'text', 'required' => false, 'placeholder' => null])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-colors @error($name) border-red-500 @enderror"
            placeholder="{{ $placeholder }}"
            rows="4"
            {{ $required ? 'required' : '' }}
        >{{ old($name, $value) }}</textarea>
    @elseif ($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-colors @error($name) border-red-500 @enderror"
            {{ $required ? 'required' : '' }}
        >
            <option value="">-- Select {{ $label }} --</option>
            {{ $slot }}
        </select>
    @else
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-colors @error($name) border-red-500 @enderror"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
        />
    @endif
    
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
