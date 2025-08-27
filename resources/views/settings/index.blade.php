<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configuración') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Social Networks To Connect -->
            <section>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Conectar Redes Sociales</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @php
                        $availableProviders = [
                            'x' => 'X (Twitter)',
                            'google' => 'Google',
                            'github' => 'GitHub',
                            'discord' => 'Discord',
                            'reddit' => 'Reddit',
                            'telegram' => 'Telegram',
                        ];
                    @endphp

                    @foreach ($availableProviders as $providerKey => $providerName)
                        @php
                            $account = null;
                            if ($accounts->has($providerKey)) {
                                $providerAccounts = $accounts->get($providerKey);
                                $account = is_a($providerAccounts, 'Illuminate\Database\Eloquent\Collection')
                                    ? $providerAccounts->first()
                                    : $providerAccounts;
                            }
                        @endphp
                        <x-social-card :provider="$providerName" :providerKey="$providerKey" :account="$account" />
                    @endforeach
                </div>
            </section>

            <!-- Telegram Widget (si está configurado) -->
            @if (config('services.telegram.bot_username'))
                <section id="telegram-widget" class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <h4 class="text-md font-semibold text-blue-800 mb-3">Conectar Telegram</h4>
                    <p class="text-sm text-blue-600 mb-4">Haz clic en el botón para conectar tu cuenta de Telegram:</p>

                    <!-- Widget de Telegram -->
                    <script async src="https://telegram.org/js/telegram-widget.js?22"
                        data-telegram-login="{{ config('services.telegram.bot_username') }}" data-size="large"
                        data-auth-url="{{ route('oauth.callback', 'telegram') }}" data-request-access="write"></script>

                    <p class="text-xs text-blue-500 mt-3">
                        <strong>Nota:</strong> Necesitas tener un bot de Telegram configurado para usar esta
                        funcionalidad.
                    </p>
                </section>
            @endif

            <!-- Connected Accounts Summary -->
            <section>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Cuentas Conectadas</h3>
                <div class="bg-white border border-gray-100 rounded-xl p-4">
                    @if ($accounts->count())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($accounts as $provider => $accountData)
                                @php
                                    $account = is_a($accountData, 'Illuminate\Database\Eloquent\Collection')
                                        ? $accountData->first()
                                        : $accountData;
                                @endphp
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        @if ($account->avatar)
                                            <img src="{{ $account->avatar }}"
                                                alt="{{ $account->nickname ?? ucfirst($provider) }}"
                                                class="w-10 h-10 rounded-full">
                                        @else
                                            <div
                                                class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                                {{ strtoupper(substr($provider, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-semibold text-gray-800">{{ ucfirst($provider) }}</span>
                                            <div class="text-sm text-gray-500">{{ $account->nickname ?? 'Sin nombre' }}
                                            </div>
                                            @if ($account->expires_in && $account->expires_in->isPast())
                                                <div class="text-xs text-red-500">Token expirado</div>
                                            @elseif($account->expires_in)
                                                <div class="text-xs text-green-500">Expira:
                                                    {{ $account->expires_in->diffForHumans() }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('oauth.revoke', $provider) }}" class="inline"
                                        data-requires2fa="true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('¿Estás seguro de desconectar {{ ucfirst($provider) }}?')"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Desconectar
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <p class="text-gray-500 text-sm mt-2">No hay cuentas conectadas aún.</p>
                            <p class="text-gray-400 text-xs mt-1">Conecta cuentas de redes sociales para empezar a
                                gestionar tu contenido.</p>
                        </div>
                    @endif
                </div>
            </section>

            <!-- 2FA -->
            <section x-data="{ showModal: false }">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Seguridad – Autenticación en dos pasos</h3>
                <div class="bg-white border border-gray-100 rounded-xl p-6">
                    @if ($twoFactorEnabled)
                        <form method="POST" action="{{ route('2fa.disable') }}" data-requires2fa="true">
                            @csrf
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">2FA está <span
                                        class="text-green-600 font-bold">activo</span></span>
                                <button type="submit"
                                    class="ml-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                    Desactivar 2FA
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">2FA está <span
                                    class="text-gray-500 font-bold">inactivo</span></span>
                            <button @click="showModal = true" type="button"
                                class="ml-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                                Activar 2FA
                            </button>
                        </div>

                        <!-- Modal QR -->
                        <div x-show="showModal" x-transition.opacity
                            class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
                            @click.self="showModal = false" style="display: none;">
                            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-lg font-semibold">Activar 2FA</h4>
                                    <button @click="showModal = false"
                                        class="text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">Escanea el siguiente código con tu app de
                                    autenticación:</p>
                                <div class="border p-4 bg-white mb-4 flex justify-center">{!! $qrCode !!}</div>
                                <x-two-factor-form />
                            </div>
                        </div>
                    @endif
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
