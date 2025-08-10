@props(['title', 'badge' => null])

<div {{ $attributes->merge(['class' => 'bg-white shadow-sm sm:rounded-xl border border-gray-100 overflow-hidden']) }}>
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800">{{ __($title) }}</h3>
        @if($badge)
            <span class="inline-flex items-center gap-2 text-xs text-gray-500">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                {{ __($badge) }}
            </span>
        @endif
    </div>

    <div class="px-6 py-5">
        {{ $slot }}
    </div>
</div>