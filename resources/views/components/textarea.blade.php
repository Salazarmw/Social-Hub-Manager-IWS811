@props(['placeholder', 'max' => 280])

<div class="flex-1">
    <label for="composer" class="sr-only">{{ __('Escribe tu publicación') }}</label>
    <textarea
        id="composer"
        rows="5"
        placeholder="{{ __($placeholder) }}"
        maxlength="{{ $max }}"
        {{ $attributes->merge(['class' => 'w-full resize-y rounded-lg border border-gray-200 focus:border-indigo-400 focus:ring focus:ring-indigo-100 text-gray-800 placeholder:text-gray-400']) }}
    ></textarea>

    <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
        <span>{{ __('Sugerencia: puedes añadir enlaces o hashtags.') }}</span>
        <span><span class="js-counter">0</span>/{{ $max }}</span>
    </div>
</div>