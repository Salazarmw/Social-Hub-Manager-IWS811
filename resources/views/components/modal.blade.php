<div
    x-data="{ show: @js($attributes->get('show', false)) }"
    x-show="show"
    x-transition.opacity
    class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40"
    @click.self="show = false"
    style="display: none;"
>
    <div class="bg-white rounded-lg shadow-lg p-4 w-full max-w-xs sm:max-w-sm md:max-w-md">
        <button @click="show = false" class="top-0 right-0 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
        {{ $slot }}
    </div>
</div>
