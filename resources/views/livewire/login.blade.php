<div class="min-h-screen flex items-center justify-center bg-gray-50 p-4 -mt-10">
    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-sm border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-5xl mb-4">🎮</h1>
            <h2 class="text-2xl font-black text-gray-800">Login Sistem</h2>
            <p class="text-xs font-bold text-gray-400 mt-1 uppercase">Game Center Manager</p>
        </div>

        <form wire:submit.prevent="login" class="space-y-5">
            <div>
                <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Email</label>
                <input type="email" wire:model="email" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-700">
                @error('email') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Password</label>
                <input type="password" wire:model="password" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-700">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-black py-4 rounded-2xl hover:bg-blue-700 transition-all shadow-lg active:scale-95 text-lg mt-4">
                MASUK
            </button>
        </form>
    </div>
</div>