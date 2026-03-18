<div class="p-4 md:p-6 max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800">🎮 Dashboard</h2>
        <div class="text-xs md:text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded-full shadow-sm">
            Pendapatan: <p class="text-base md:text-lg font-black mt-0.5">
                Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
            </p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-bold shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-10">
        @foreach($units as $unit)
            @php $sewa = $active_rentals[$unit->nama] ?? null; @endphp
            
            @if($sewa)
                <div class="bg-red-500 shadow-red-200 text-white p-5 rounded-2xl shadow-xl transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <h3 class="font-bold text-xl">{{ $unit->nama }}</h3>
                        <span class="text-[10px] bg-white bg-opacity-20 px-2 py-0.5 rounded-full uppercase">PS3</span>
                    </div>

                    <div class="mt-4" x-data="{ 
                        selesai: '{{ $sewa->jam_selesai }}',
                        sisa: '',
                        updateSisa() {
                            let waktuSelesai = new Date(this.selesai).getTime();
                            let sekarang = new Date().getTime();
                            let diff = Math.floor((waktuSelesai - sekarang) / 1000);

                            if (diff <= 0) { 
                                this.sisa = 'WAKTU HABIS'; 
                                return; 
                            }

                            let h = Math.floor(diff / 3600).toString().padStart(2, '0');
                            let m = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
                            let s = (diff % 60).toString().padStart(2, '0');
                            this.sisa = `${h}:${m}:${s}`;
                        }
                    }" 
                    x-init="updateSisa(); setInterval(() => updateSisa(), 1000);"
                    x-on:waktu-diperbarui.window="if('{{ $sewa->id }}' == $event.detail.id || true) { selesai = $event.detail.jamBaru; updateSisa(); }"
                    >
                        <p class="text-[12px] opacity-90 uppercase">Penyewa: <span class="font-bold">{{ $sewa->nama_penyewa }}</span></p>
                        <p class="text-[12px] opacity-80">Mulai: {{ \Carbon\Carbon::parse($sewa->jam_mulai)->format('d M H:i') }}</p>

                        <div class="grid grid-cols-2 gap-2 mt-3">
                            <div class="py-2 bg-black bg-opacity-20 rounded-lg text-center">
                                <p class="text-[9px] uppercase opacity-70">Selesai</p>
                                <p class="font-mono text-xl font-bold">{{ \Carbon\Carbon::parse($sewa->jam_selesai)->format('H:i') }}</p>
                            </div>
                            <div class="py-2 bg-black bg-opacity-40 rounded-lg text-center border border-white border-opacity-20">
                                <p class="text-[9px] uppercase opacity-70">Sisa Waktu</p>
                                <p class="font-mono text-xl font-bold" x-text="sisa.replace(/:/g, ' : ')"></p> 
                            </div>
                        </div>

                        <p class="text-center text-xs mt-2 font-bold bg-white bg-opacity-20 py-1 rounded">
                            Tagihan: Rp {{ number_format($sewa->total_biaya, 0, ',', '.') }}
                        </p>

                        @if(!$sewa->is_lunas)
                            <div class="mt-2 text-center bg-yellow-400 text-yellow-900 text-[10px] font-black py-1.5 rounded-lg shadow-sm animate-pulse">
                                ⚠️ BELUM BAYAR
                            </div>
                        @endif
                        
                        <div class="flex gap-2 mt-3">
                            <button wire:click="openModalStop({{ $sewa->id }})" class="flex-1 bg-white text-red-600 font-bold py-2 rounded-xl text-xs hover:bg-gray-100 transition shadow-md active:scale-95">
                                STOP
                            </button>
                            <button wire:click="openModalTambah({{ $sewa->id }})" class="flex-1 bg-yellow-400 text-yellow-900 font-bold py-2 rounded-xl text-xs hover:bg-yellow-300 transition shadow-md active:scale-95">
                                ➕ TAMBAH
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div wire:click="openModalSewa('{{ $unit->nama }}')" class="bg-emerald-500 shadow-emerald-200 text-white p-5 rounded-2xl shadow-xl transition-all duration-300 cursor-pointer hover:bg-emerald-600 active:scale-95 flex flex-col h-full">
                    <div class="flex justify-between items-start">
                        <h3 class="font-bold text-xl">{{ $unit->nama }}</h3>
                        <span class="text-[10px] bg-white bg-opacity-20 px-2 py-0.5 rounded-full uppercase">{{ $unit->jenis_konsol }}</span>
                    </div>
                    <div class="mt-8 mb-4 text-center flex-grow flex flex-col justify-center">
                        <div class="text-4xl mb-2">✅</div>
                        <p class="text-sm font-bold opacity-90">TAP UNTUK SEWA</p>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if($showModalSewa)
    <div class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white p-6 rounded-3xl md:rounded-3xl rounded-b-none md:rounded-b-3xl shadow-2xl w-full max-w-sm border border-gray-100 animate-slide-up">
            <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center justify-center">
                <span class="bg-emerald-100 text-emerald-600 px-4 py-1 rounded-full">Sewa {{ $selected_unit }}</span>
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">NAMA PENYEWA</label>
                    <input type="text" wire:model="nama_penyewa" placeholder="Input Nama..." class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                    @error('nama_penyewa') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">DURASI (JAM)</label>
                    <input type="text" wire:model="durasi" placeholder="Misal: 1 atau 1.5" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                    @error('durasi') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between mt-4 p-4 bg-gray-50 border border-gray-200 rounded-2xl">
                <div>
                    <label class="text-xs font-bold text-gray-700 block">BAYAR DI AKHIR?</label>
                    <span class="text-[10px] text-gray-500">Aktifkan untuk langganan yang bayar setelah main</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="is_bayar_nanti" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="flex flex-col gap-5 mt-6">
                <button wire:click="mulaiSewa" class="w-full bg-blue-600 text-white font-bold py-4 rounded-2xl hover:bg-blue-700 transition-all shadow-lg active:scale-95 text-lg">
                    🚀 MULAI SEWA
                </button>
                <button wire:click="$set('showModalSewa', false)" class="w-full bg-gray-100 text-gray-500 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm">
                    BATAL
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($showModalTambah)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white p-6 rounded-3xl shadow-2xl w-full max-w-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4 text-center">⏳ Tambah Durasi</h3>
            <div class="mb-4">
                <input type="text" wire:model="tambahan_durasi" placeholder="Misal: 0.5 atau 2" 
                       class="w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-yellow-400 outline-none text-xl font-bold text-center">
                @error('tambahan_durasi') <span class="text-red-500 text-[10px] mt-1 block text-center">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col gap-5">
                <button wire:click="simpanTambahanWaktu" class="w-full bg-yellow-400 text-yellow-900 font-black py-4 rounded-2xl hover:bg-yellow-500 transition-all shadow-lg active:scale-95">
                    KONFIRMASI
                </button>
                <button wire:click="$set('showModalTambah', false)" class="w-full bg-gray-100 text-gray-500 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm">
                    BATAL
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($showModalStop)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white p-6 rounded-3xl shadow-2xl w-full max-w-sm border border-gray-100 text-center">
            <div class="text-5xl mb-4">⚠️</div>
            <h3 class="text-xl font-black text-gray-800 mb-2">Hentikan Sewa?</h3>
            <p class="text-sm text-gray-500 mb-6">Apakah kamu yakin ingin menghentikan waktu sewa ini sekarang? Aksi ini tidak bisa dibatalkan.</p>
            
            <div class="flex flex-col gap-5">
                <button wire:click="konfirmasiStop" class="w-full bg-red-600 text-white font-black py-4 rounded-2xl hover:bg-red-700 transition-all shadow-lg active:scale-95">
                    YA, STOP SEKARANG
                </button>
                <button wire:click="$set('showModalStop', false)" class="w-full bg-gray-100 text-gray-500 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm">
                    KEMBALI
                </button>
            </div>
        </div>
    </div>
    @endif

    <style>
        /* Animasi agar modal sewa muncul dari bawah di HP */
        @keyframes slideUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-slide-up {
            animation: slideUp 0.3s ease-out forwards;
        }
    </style>

    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-xl border border-gray-100 mb-10">
        <h4 class="text-xl font-bold mb-6 text-gray-700 flex items-center">
            <span class="mr-2">📋</span> Riwayat Sewa Hari Ini
        </h4>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="p-4 font-bold">Unit</th>
                        <th class="p-4 font-bold">Nama</th>
                        <th class="p-4 font-bold">Durasi</th>
                        <th class="p-4 font-bold">Mulai</th>
                        <th class="p-4 font-bold">Berakhir</th>
                        <th class="p-4 font-bold text-right">Biaya</th>
                        <th class="p-4 font-bold text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700">
                    @foreach($riwayat_hari_ini as $riwayat)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="p-4 font-black text-gray-900">{{ $riwayat->nama_unit }}</td>
                        <td class="p-4 font-semibold">{{ $riwayat->nama_penyewa }}
                            @if($riwayat->is_deleted)
                                <span class="ml-2 text-[9px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full uppercase">Dihentikan</span>
                            @endif
                        </td>
                        <td class="p-4">{{ $riwayat->durasi }} Jam</td>
                        <td class="p-4">{{ \Carbon\Carbon::parse($riwayat->jam_mulai)->format('H:i') }}</td>
                        <td class="p-4">{{ \Carbon\Carbon::parse($riwayat->jam_selesai)->format('H:i') }}</td>
                        <td class="p-4 font-bold text-right text-emerald-600">Rp {{ number_format($riwayat->total_biaya, 0, ',', '.') }}</td>
                        <td class="p-4 text-right">
                            @if($riwayat->is_lunas)
                                <span class="text-[10px] bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full font-bold uppercase tracking-wider">LUNAS</span>
                            @else
                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-[10px] bg-red-100 text-red-600 px-3 py-1 rounded-full font-bold uppercase tracking-wider">BELUM BAYAR</span>
                                    <button wire:click="tandaiLunas({{ $riwayat->id }})" class="bg-blue-600 text-white px-2 py-1 rounded-lg text-[10px] font-black shadow-sm hover:bg-blue-700 transition active:scale-95">
                                        💸 TANDAI LUNAS
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($riwayat_hari_ini->isEmpty())
                <p class="text-center text-gray-400 py-6 text-sm">Belum ada penyewaan hari ini.</p>
            @endif
        </div>
    </div>
    
</div>