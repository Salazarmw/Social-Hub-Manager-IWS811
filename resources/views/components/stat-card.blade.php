<div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
    <div class="text-sm text-gray-500">{{ __($title) }}</div>
    <div class="mt-2">
        @if (isset($count))
            <div class="text-2xl font-semibold text-gray-800">{{ $count }}</div>
        @else
            {{ $slot }}
        @endif
    </div>
</div>
