<div class="p-4 md:p-6 max-w-lg mx-auto mt-6 md:mt-10">
    <h2 class="text-2xl font-extrabold text-gray-800 mb-8 text-center">👤 Profil Akun</h2>

    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-lg border border-gray-100 text-center mb-8">
        <div class="w-24 h-24 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 text-5xl shadow-inner border-4 border-white ring-2 ring-gray-50">
            {{ auth()->user()->role === 'admin' ? '👑' : '👨‍💻' }}
        </div>
        
        <h3 class="text-2xl font-black text-gray-800">{{ auth()->user()->name }}</h3>
        <p class="text-gray-500 font-bold mb-6">{{ auth()->user()->email }}</p>

        <div class="inline-block bg-gray-50 border border-gray-100 px-5 py-2.5 rounded-2xl">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Hak Akses</span>
            <span class="text-sm font-black text-blue-600 uppercase">{{ auth()->user()->role }}</span>
        </div>
    </div>

    <button wire:click="logout" onclick="confirm('Yakin ingin keluar dari aplikasi?') || event.stopImmediatePropagation()" class="w-full bg-red-50 text-red-600 border border-red-100 font-black py-4 rounded-2xl hover:bg-red-100 transition-all shadow-sm active:scale-95 text-lg flex items-center justify-center gap-2">
        <span>🚪</span> LOGOUT
    </button>
</div>