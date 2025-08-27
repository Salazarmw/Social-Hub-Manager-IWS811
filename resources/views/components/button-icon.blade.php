@props(['text'])

<button type="button"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-3 py-2 text-sm rounded-md border border-gray-200 text-gray-700 hover:bg-gray-50']) }}>
    {{ $slot }}
    {{ __($text) }}
</button>
