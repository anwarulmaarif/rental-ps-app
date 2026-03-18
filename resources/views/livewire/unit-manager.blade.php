<div class="p-4 md:p-6 max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-extrabold text-gray-800">⚙️ Daftar Unit TV</h2>
        <button wire:click="openModal" class="bg-blue-600 text-white px-4 py-2 rounded-xl font-bold text-sm shadow-md hover:bg-blue-700 active:scale-95 transition-all">
            + Tambah Unit
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-bold shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
        @foreach($units as $unit)
        <div class="bg-white p-5 rounded-2xl shadow-sm border {{ $unit->is_active ? 'border-gray-200' : 'border-red-200 opacity-70' }}">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-black text-xl text-gray-800">{{ $unit->nama }}</h3>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $unit->jenis_konsol }} • Rp {{ number_format($unit->tarif_per_jam, 0, ',', '.') }}/jam</p>
                </div>
                <button wire:click="toggleStatus({{ $unit->id }})" class="text-[10px] px-3 py-1 rounded-full font-bold {{ $unit->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                    {{ $unit->is_active ? 'AKTIF' : 'NONAKTIF' }}
                </button>
            </div>

            <div class="flex gap-2">
                <button wire:click="edit({{ $unit->id }})" class="flex-1 bg-gray-100 text-gray-700 font-bold py-2 rounded-xl text-xs hover:bg-gray-200 transition">
                    ✏️ UBAH
                </button>
                <button onclick="confirm('Yakin ingin menghapus unit ini secara permanen?') || event.stopImmediatePropagation()" wire:click="hapus({{ $unit->id }})" class="flex-1 bg-red-50 text-red-600 font-bold py-2 rounded-xl text-xs hover:bg-red-100 transition">
                    🗑️ HAPUS
                </button>
            </div>
        </div>
        @endforeach

        @if($units->isEmpty())
        <div class="col-span-full text-center py-10 bg-white rounded-2xl border border-gray-100 border-dashed">
            <p class="text-gray-400 font-bold">Belum ada unit terdaftar. Silakan tambah unit.</p>
        </div>
        @endif
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white p-6 rounded-3xl shadow-2xl w-full max-w-sm border border-gray-100">
            <h3 class="text-xl font-black text-gray-800 mb-6 text-center">
                {{ $isEdit ? '✏️ Ubah Unit' : '🎮 Tambah Unit Baru' }}
            </h3>
            
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">NAMA UNIT (Contoh: TV1)</label>
                    <input type="text" wire:model="nama" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                    @error('nama') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">JENIS KONSOL (Contoh: PS3 / PS4)</label>
                    <input type="text" wire:model="jenis_konsol" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                    @error('jenis_konsol') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">TARIF PER JAM (Rupiah)</label>
                    <input type="number" wire:model="tarif_per_jam" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                    @error('tarif_per_jam') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <button wire:click="simpan" class="w-full bg-blue-600 text-white font-black py-4 rounded-2xl hover:bg-blue-700 transition-all shadow-lg active:scale-95 text-lg">
                    SIMPAN
                </button>
                <button wire:click="$set('showModal', false)" class="w-full bg-gray-100 text-gray-500 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm">
                    BATAL
                </button>
            </div>
        </div>
    </div>
    @endif
</div>