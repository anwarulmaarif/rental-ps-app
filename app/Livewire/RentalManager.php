<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Rental;
use App\Models\Unit; // Pastikan Model Unit dipanggil
use Carbon\Carbon;

class RentalManager extends Component
{
    public $nama_penyewa, $selected_unit, $durasi;
    public $is_bayar_nanti = false; // Slider form Bayar Nanti
    
    // Variabel kontrol Modal
    public $showModalSewa = false;
    public $showModalTambah = false;
    public $showModalStop = false;
    
    // Variabel penampung ID/Data
    public $selectedRentalId;
    public $selectedRentalIdToStop;
    public $tambahan_durasi;

    // --- FUNGSI MULAI SEWA ---
    public function openModalSewa($unit)
    {
        $this->selected_unit = $unit;
        $this->nama_penyewa = ''; 
        $this->durasi = '';       
        $this->is_bayar_nanti = false; // Default slider mati
        $this->showModalSewa = true;
    }

    public function mulaiSewa()
    {
        // Ubah koma jadi titik
        $this->durasi = str_replace(',', '.', $this->durasi);

        $this->validate([
            'selected_unit' => 'required',
            'nama_penyewa' => 'required',
            'durasi' => 'required|numeric|gt:0',
        ], [
            'durasi.numeric' => 'Durasi harus berupa angka.',
            'durasi.gt' => 'Durasi tidak boleh 0 atau minus.'
        ]);

        $durasi_jam = (float) $this->durasi;
        
        // AMBIL TARIF DARI DATABASE BERDASARKAN TV YANG DIPILIH
        $unitTerpilih = Unit::where('nama', $this->selected_unit)->first();
        $total_bayar = $durasi_jam * $unitTerpilih->tarif_per_jam;

        Rental::create([
            'nama_unit' => $this->selected_unit,
            'nama_penyewa' => $this->nama_penyewa,
            'durasi' => $durasi_jam, 
            'tarif_per_jam' => $unitTerpilih->tarif_per_jam, 
            'jam_mulai' => now(),
            'jam_selesai' => now()->addMinutes($durasi_jam * 60),
            'total_biaya' => $total_bayar,
            'is_deleted' => false,
            // Jika bayar nanti dicentang (true), maka is_lunas = false
            'is_lunas' => !$this->is_bayar_nanti, 
        ]);

        $this->reset(['selected_unit', 'nama_penyewa', 'durasi']);
        $this->showModalSewa = false; 
        
        session()->flash('message', 'Sewa dimulai! Biaya: Rp ' . number_format($total_bayar, 0, ',', '.'));
    }

    // --- FUNGSI TAMBAH WAKTU ---
    public function openModalTambah($id)
    {
        $this->selectedRentalId = $id;
        $this->tambahan_durasi = null; 
        $this->showModalTambah = true;
    }

    public function simpanTambahanWaktu()
    {
        $this->tambahan_durasi = str_replace(',', '.', $this->tambahan_durasi);

        $this->validate([
            'tambahan_durasi' => 'required|numeric|gt:0',
        ], [
            'tambahan_durasi.numeric' => 'Durasi tambahan harus berupa angka.',
            'tambahan_durasi.gt' => 'Durasi tambahan harus lebih dari 0.'
        ]);

        $durasi_jam = (float) $this->tambahan_durasi;
        
        $this->tambahWaktu($this->selectedRentalId, $durasi_jam);

        $this->dispatch('waktu-diperbarui', jamBaru: Rental::find($this->selectedRentalId)->jam_selesai);

        $this->showModalTambah = false;
        $this->reset(['selectedRentalId', 'tambahan_durasi']);
        session()->flash('message', 'Durasi berhasil ditambah!');
    }

    public function tambahWaktu($id, $tambahanJam)
    {
        if ($tambahanJam <= 0) return;

        $rental = Rental::find($id);
        if ($rental) {
            $durasiBaru = $rental->durasi + $tambahanJam;
            
            // Ambil tarif dinamis
            $unitTerpilih = Unit::where('nama', $rental->nama_unit)->first();
            $tarif = $unitTerpilih ? $unitTerpilih->tarif_per_jam : $rental->tarif_per_jam;

            $biayaTambahan = $tambahanJam * $tarif;

            $rental->update([
                'durasi' => $durasiBaru,
                'jam_selesai' => Carbon::parse($rental->jam_selesai)->addMinutes($tambahanJam * 60),
                'total_biaya' => $rental->total_biaya + $biayaTambahan,
            ]);
        }
    }

    // --- FUNGSI STOP SEWA ---
    public function openModalStop($id)
    {
        $this->selectedRentalIdToStop = $id;
        $this->showModalStop = true;
    }

    public function konfirmasiStop()
    {
        $rental = Rental::find($this->selectedRentalIdToStop);
        if ($rental) {
            $rental->update([
                'is_deleted' => true,
                'tgl_hapus' => now()
            ]);
        }
        
        $this->showModalStop = false;
        $this->reset('selectedRentalIdToStop');
        session()->flash('message', 'Unit sudah dikosongkan.');
    }

    // --- FUNGSI TANDAI LUNAS ---
    public function tandaiLunas($id)
    {
        $rental = Rental::find($id);
        if ($rental) {
            $rental->update(['is_lunas' => true]);
            session()->flash('message', 'Pembayaran berhasil ditandai LUNAS!');
        }
    }

    public function render()
    {
        // Hitung pendapatan hanya dari sewa yang statusnya LUNAS
        $pendapatanHariIni = Rental::whereDate('created_at', Carbon::today())
                                    ->where('is_lunas', true)
                                    ->sum('total_biaya');
                                    
        $riwayatHariIni = Rental::whereDate('created_at', Carbon::today())
                                ->orderBy('id', 'desc')
                                ->get();
                                    
        return view('livewire.rental-manager', [
            // Ambil unit yang Aktif saja dari Database
            'units' => Unit::where('is_active', true)->get(), 
            
            'active_rentals' => Rental::where('is_deleted', false)
                                     ->where('jam_selesai', '>', now())
                                     ->get()
                                     ->keyBy('nama_unit'),
                                     
            'total_pendapatan' => $pendapatanHariIni,
            'riwayat_hari_ini' => $riwayatHariIni
        ])->layout('layouts.app');
    }
}