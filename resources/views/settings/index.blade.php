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
            <section x-data="{ showModal: false }">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Seguridad – Autenticación en dos pasos</h3>
                <div class="bg-white border border-gray-100 rounded-xl p-6">
                    @if ($twoFactorEnabled)
                        <form method="POST" action="{{ route('2fa.disable') }}">
                            @csrf
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">2FA está <span class="text-green-600 font-bold">activo</span></span>
                                <button type="submit" class="ml-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Desactivar 2FA</button>
                            </div>
                        </form>
                    @else
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">2FA está <span class="text-gray-500 font-bold">inactivo</span></span>
                            <button @click="showModal = true" type="button" class="ml-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Activar 2FA</button>
                        </div>
                        <!-- Modal QR -->
                        <template x-if="showModal">
                            <x-modal>
                                <h4 class="text-lg font-semibold mb-4">Activar 2FA</h4>
                                <p class="text-sm text-gray-600 mb-2">Escanea el siguiente código con tu app de autenticación:</p>
                                <div class="border p-2 bg-white mb-4 flex justify-center">{!! $qrCode !!}</div>
                                <x-two-factor-form />
                            </x-modal>
                        </template>
                    @endif
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
