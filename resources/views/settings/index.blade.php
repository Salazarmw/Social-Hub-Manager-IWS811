<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configuración') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Connected Social Accounts -->
            <section>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Cuentas conectadas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach (['X', 'facebook', 'linkedin', 'instagram', 'pinterest'] as $provider)
                        @php
                            $account = $accounts->get($provider, null);
                        @endphp
                        <x-social-card :provider="$provider" :account="$account" />
                    @endforeach
                </div>
            </section>

            <!-- 2FA -->
            <section>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Seguridad – Autenticación en dos pasos</h3>
                <div class="bg-white border border-gray-100 rounded-xl p-6">
                    <form method="POST" action="{{ $twoFactorEnabled ? route('2fa.disable') : route('2fa.enable') }}">
                        @csrf
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">Activar 2FA</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer"
                                    @if ($twoFactorEnabled) checked @endif onchange="this.form.submit()">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                                </div>
                            </label>
                        </div>
                        @if (!$twoFactorEnabled && $qrCode)
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-2">Escanea el siguiente código con tu app de
                                    autenticación:</p>
                                <div class="inline-block border p-2 bg-white">{!! $qrCode !!}</div>
                            </div>
                        @endif
                    </form>
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
