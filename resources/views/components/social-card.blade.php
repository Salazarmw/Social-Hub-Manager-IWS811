@props(['provider', 'providerKey', 'account'])

<div
    {{ $attributes->merge(['class' => 'bg-white border border-gray-200 rounded-xl shadow-sm p-4 flex items-center justify-between']) }}>

    <!-- left side -->
    <div class="flex items-center space-x-3">
        <img src="{{ asset("images/{$providerKey}.svg") }}" alt="{{ $provider }}" class="w-8 h-8 rounded">
        <div>
            <p class="font-semibold text-gray-800 capitalize">{{ $provider }}</p>
            <p class="text-xs text-gray-500">
                {{ $account ? 'Connected' : 'Not connected' }}
            </p>
        </div>
    </div>

    <!-- right button -->
    @if ($account)
        <div class="flex items-center space-x-2">
            <form method="POST" action="{{ route('oauth.revoke', ['provider' => $providerKey]) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-full bg-red-600 text-white hover:bg-red-700 transition">
                    Desconectar
                </button>
            </form>
        </div>
    @else
        <a href="{{ route('oauth.redirect', ['provider' => $providerKey]) }}"
            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-full bg-indigo-600 text-white hover:bg-indigo-700 transition">
            Conectar
        </a>
    @endif
</div>
