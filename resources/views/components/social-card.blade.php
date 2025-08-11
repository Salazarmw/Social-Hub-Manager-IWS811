@props(['provider', 'account'])

<div
    {{ $attributes->merge(['class' => 'bg-white border border-gray-200 rounded-xl shadow-sm p-4 flex items-center justify-between']) }}>

    <!-- left side -->
    <div class="flex items-center space-x-3">
        <img src="{{ asset("images/{$provider}.svg") }}" alt="{{ $provider }}" class="w-8 h-8 rounded">
        <div>
            <p class="font-semibold text-gray-800 capitalize">{{ $provider }}</p>
            <p class="text-xs text-gray-500">
                {{ $account ? 'Connected' : 'Not connected' }}
            </p>
        </div>
    </div>

    <!-- right button -->
    @if ($account)
        <span
            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">
            ✔ Done
        </span>
    @else
        <a href="{{ route('oauth.redirect', $provider) }}"
            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-full bg-indigo-600 text-white hover:bg-indigo-700 transition">
            Connect
        </a>
    @endif
</div>
