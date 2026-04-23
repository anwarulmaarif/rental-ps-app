<div> 
    <div class="p-4 md:p-6 max-w-lg mx-auto mt-6 md:mt-10">
        <h2 class="text-2xl font-extrabold text-gray-800 mb-8 text-center">👤 Profil Akun</h2>

        @if (session()->has('message'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-bold shadow-sm animate-pulse text-center">
                {{ session('message') }}
            </div>
        @endif

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
            
            @if(auth()->user()->role === 'admin')
                <hr style="margin:20px 0">
                <button wire:click="openModalAddUser" class="m-3 bg-gray-800 text-white px-4 py-2 rounded-xl hover:bg-black transition shadow-sm font-bold text-sm" title="Tambah User">
                    👤+ Tambah User
                </button>
                <br>
                <button wire:click="openModalImportCsv" class="m-3 bg-gray-800 text-white px-4 py-2 rounded-xl hover:bg-black transition shadow-sm font-bold text-sm" title="Tambah User">
                    Import Data Lama
                </button>

            @endif
            
        </div>

        <button wire:click="logout" onclick="confirm('Yakin ingin keluar dari aplikasi?') || event.stopImmediatePropagation()" class="w-full bg-red-50 text-red-600 border border-red-100 font-black py-4 rounded-2xl hover:bg-red-100 transition-all shadow-sm active:scale-95 text-lg flex items-center justify-center gap-2">
            <span>🚪</span> LOGOUT
        </button>
    </div>

    @if($showModalAddUser)
    <div class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white p-6 rounded-3xl md:rounded-3xl rounded-b-none md:rounded-b-3xl shadow-2xl w-full max-w-sm border border-gray-100 animate-slide-up">
            <h3 class="text-xl font-black text-gray-800 mb-6 text-center">
                <span class="bg-gray-100 text-gray-700 px-4 py-1 rounded-full">Tambah User Baru</span>
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Nama Lengkap</label>
                    <input type="text" wire:model="new_user_name" placeholder="Nama Kasir..." class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                    @error('new_user_name') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Email / Username</label>
                    <input type="email" wire:model="new_user_email" placeholder="email@contoh.com" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                    @error('new_user_email') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Password</label>
                    <input type="password" wire:model="new_user_password" placeholder="******" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                    @error('new_user_password') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Role / Hak Akses</label>
                    <select wire:model="new_user_role" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                        <option value="kasir">Kasir (Akses Terbatas)</option>
                        <option value="admin">Admin (Akses Penuh)</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-2 mt-6">
                <button wire:click="simpanUser" class="w-full bg-gray-900 text-white font-bold py-4 rounded-2xl hover:bg-black transition-all shadow-lg active:scale-95 text-lg">
                    💾 SIMPAN USER
                </button>
                <button wire:click="$set('showModalAddUser', false)" class="w-full bg-gray-100 text-gray-500 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm">
                    BATAL
                </button>
            </div>
        </div>
    </div>
    @endif

@if($showModalImportCsv)
<div class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
    <div class="bg-white p-6 rounded-3xl md:rounded-3xl rounded-b-none md:rounded-b-3xl shadow-2xl w-full max-w-sm border border-gray-100 animate-slide-up">
        
        <h3 class="text-xl font-black text-gray-800 mb-4 text-center">
            <span class="bg-blue-100 text-blue-800 px-4 py-1 rounded-full text-sm">📥 Import CSV</span>
        </h3>

        <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 mb-6 overflow-x-auto">
            <p class="text-[10px] font-bold text-gray-500 mb-2 uppercase">Urutan 6 Kolom Excel:</p>
            <table class="text-[10px] w-full border-collapse bg-white shadow-sm">
                <tr class="bg-blue-50 font-bold text-blue-800 border-b border-blue-100">
                    <td class="border-r border-blue-100 p-1.5">A (Tgl)</td>
                    <td class="border-r border-blue-100 p-1.5">B (Unit)</td>
                    <td class="border-r border-blue-100 p-1.5">C (Nama)</td>
                    <td class="border-r border-blue-100 p-1.5">D (Jam)</td>
                    <td class="border-r border-blue-100 p-1.5">E (Mulai)</td>
                    <td class="p-1.5">F (Tarif)</td>
                </tr>
                <tr class="text-gray-500 font-semibold">
                    <td class="border-r border-gray-100 p-1.5">2026-03-29</td>
                    <td class="border-r border-gray-100 p-1.5">TV1</td>
                    <td class="border-r border-gray-100 p-1.5">Budi</td>
                    <td class="border-r border-gray-100 p-1.5">2</td>
                    <td class="border-r border-gray-100 p-1.5">14:30</td>
                    <td class="p-1.5">5000</td>
                </tr>
            </table>
            <p class="text-[9px] text-gray-400 mt-2 italic">* Jam Selesai, Total Biaya, dan Status (Lunas) akan dihitung otomatis oleh sistem.</p>
        </div>

        <form wire:submit.prevent="importData">
            <input type="file" wire:model="file_excel" class="block w-full text-xs text-gray-500
                file:mr-4 file:py-3 file:px-4
                file:rounded-xl file:border-0
                file:text-xs file:font-bold
                file:bg-blue-600 file:text-white
                hover:file:bg-blue-700 cursor-pointer bg-gray-50 border border-gray-200 rounded-2xl p-2 outline-none mb-2">
            
            @error('file_excel') <span class="text-red-500 text-[10px] block mb-4">{{ $message }}</span> @enderror

            <div class="flex flex-col gap-2 mt-6">
                <button type="submit" wire:loading.attr="disabled" class="w-full bg-blue-600 text-white font-black py-4 rounded-2xl hover:bg-blue-700 transition-all shadow-lg active:scale-95 text-base flex justify-center items-center">
                    <span wire:loading.remove>🚀 PROSES IMPORT</span>
                    <span wire:loading>⏳ MEMPROSES...</span>
                </button>
                <button type="button" wire:click="$set('showModalImportCsv', false)" class="w-full bg-gray-100 text-gray-500 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm">
                    BATAL
                </button>
            </div>
        </form>
    </div>
</div>
@endif
</div>