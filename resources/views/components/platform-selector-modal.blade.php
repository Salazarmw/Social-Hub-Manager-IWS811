<div id="platform-selector-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" x-show="showPlatformModal">
    <div class="relative top-20 mx-auto p-5 border w-[28rem] shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Seleccionar plataformas</h3>
            <div class="mt-4 space-y-3">
                @php
                    $socialAccounts = $socialAccounts ?? collect([]);
                    $twitterAccount = $socialAccounts->firstWhere('provider', 'x');
                    $redditAccount = $socialAccounts->firstWhere('provider', 'reddit');
                @endphp

                <div class="rounded-lg hover:bg-blue-50 group">
                    <div class="p-3">
                        <div class="flex items-center mb-2">
                            <input type="checkbox" name="platforms[]" value="twitter" class="h-5 w-5 text-blue-600 rounded"
                                {{ !$twitterAccount || !$twitterAccount['hasValidToken'] ? 'disabled' : '' }}>
                            <div class="ml-3 flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <img src="{{ asset('images/x.svg') }}" alt="X logo" class="w-5 h-5">
                                </div>
                                <span class="text-gray-700 font-medium">Twitter</span>
                            </div>
                        </div>
                        
                        @if (!$twitterAccount || !$twitterAccount['hasValidToken'])
                            <div class="ml-8 flex items-center justify-between">
                                <div class="flex-grow">
                                    @if (!$twitterAccount)
                                        <span class="text-sm text-gray-500">No conectado</span>
                                    @elseif (!$twitterAccount['hasValidToken'])
                                        <span class="text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            {{ $twitterAccount['errorMessage'] }}
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('settings') }}" class="ml-4 px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded-md hover:bg-indigo-700 shadow-sm transition-colors">
                                    Configurar
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <label class="flex items-center p-3 rounded-lg hover:bg-orange-50 cursor-pointer group relative">
                    <input type="checkbox" name="platforms[]" value="reddit" class="h-5 w-5 text-orange-600 rounded"
                        {{ !$redditAccount || !$redditAccount['hasValidToken'] ? 'disabled' : '' }}>
                    <div class="ml-3 flex-grow flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <img src="{{ asset('images/reddit.svg') }}" alt="Reddit logo" class="w-5 h-5">
                            </div>
                            <span class="text-gray-700 font-medium">Reddit</span>
                        </div>
                        @if (!$redditAccount)
                            <span class="text-sm text-gray-500">No conectado</span>
                        @elseif (!$redditAccount['hasValidToken'])
                            <span class="text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                {{ $redditAccount['errorMessage'] }}
                            </span>
                        @endif
                    </div>
                    @if (!$redditAccount || !$redditAccount['hasValidToken'])
                        <a href="{{ route('settings') }}" class="hidden group-hover:block absolute right-3 px-2 py-1 text-xs bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200">
                            Configurar
                        </a>
                    @endif
                </label>
            </div>

            @if(!$socialAccounts->where('hasValidToken', true)->count())
                <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-md">
                    <p class="text-sm text-amber-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        No hay plataformas disponibles para publicar. Por favor, conecta y verifica tus cuentas en
                        <a href="{{ route('settings') }}" class="ml-1 font-medium underline hover:text-amber-800">Configuración</a>.
                    </p>
                </div>
            @endif

            <div class="mt-5 flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md" onclick="closePlatformModal()">
                    Cancelar
                </button>
                <button type="button" 
                    class="px-4 py-2 text-sm font-medium text-white rounded-md {{ $socialAccounts->where('hasValidToken', true)->count() ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-400 cursor-not-allowed' }}"
                    onclick="applyPlatformSelection()"
                    {{ !$socialAccounts->where('hasValidToken', true)->count() ? 'disabled' : '' }}>
                    Aplicar
                </button>
            </div>
        </div>
    </div>
</div>
