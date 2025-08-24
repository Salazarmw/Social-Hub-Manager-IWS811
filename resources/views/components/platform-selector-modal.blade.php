<div id="platform-selector-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" x-show="showPlatformModal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Seleccionar plataformas</h3>
            <div class="mt-4 space-y-3">
                <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 cursor-pointer">
                    <input type="checkbox" name="platforms[]" value="twitter" class="h-5 w-5 text-blue-600 rounded">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.643 4.937c-.835.37-1.732.62-2.675.733.962-.576 1.7-1.49 2.048-2.578-.9.534-1.897.922-2.958 1.13-.85-.904-2.06-1.47-3.4-1.47-2.572 0-4.658 2.086-4.658 4.66 0 .364.042.718.12 1.06-3.873-.195-7.304-2.05-9.602-4.868-.4.69-.63 1.49-.63 2.342 0 1.616.823 3.043 2.072 3.878-.764-.025-1.482-.234-2.11-.583v.06c0 2.257 1.605 4.14 3.737 4.568-.392.106-.803.162-1.227.162-.3 0-.593-.028-.877-.082.593 1.85 2.313 3.198 4.352 3.234-1.595 1.25-3.604 1.995-5.786 1.995-.376 0-.747-.022-1.112-.065 2.062 1.323 4.51 2.093 7.14 2.093 8.57 0 13.255-7.098 13.255-13.254 0-.2-.005-.402-.014-.602.91-.658 1.7-1.477 2.323-2.41z"></path>
                            </svg>
                        </div>
                        <span class="text-gray-700 font-medium">Twitter</span>
                    </div>
                </label>
                <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-orange-50 cursor-pointer">
                    <input type="checkbox" name="platforms[]" value="reddit" class="h-5 w-5 text-orange-600 rounded">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.957 0 1.734.746 1.734 1.666 0 .513-.256.964-.642 1.242.013.109.02.219.02.331 0 3.659-4.476 6.624-10.002 6.624-5.525 0-10-2.966-10-6.624 0-.112.007-.222.02-.331a1.61 1.61 0 0 1-.642-1.242c0-.92.777-1.666 1.734-1.666.478 0 .9.182 1.207.491 1.185-.851 2.826-1.41 4.636-1.488l.913-4.179a.84.84 0 0 1 .987-.654l2.898.615a1.2 1.2 0 0 1 1.137-.836z"></path>
                            </svg>
                        </div>
                        <span class="text-gray-700 font-medium">Reddit</span>
                    </div>
                </label>
            </div>
            <div class="mt-5 flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md" onclick="closePlatformModal()">
                    Cancelar
                </button>
                <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md" onclick="applyPlatformSelection()">
                    Aplicar
                </button>
            </div>
        </div>
    </div>
</div>
