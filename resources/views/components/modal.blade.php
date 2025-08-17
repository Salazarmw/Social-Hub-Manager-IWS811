<div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40" @click.self="showModal = false">
    <div class="bg-white rounded-lg shadow-lg p-4 w-full max-w-xs sm:max-w-sm md:max-w-md">
        <button @click="showModal = false" class="top-0 right-0 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
        {{ $slot }}
    </div>
</div>
