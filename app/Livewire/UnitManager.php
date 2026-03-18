<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Unit;

class UnitManager extends Component
{
    public $nama, $jenis_konsol, $tarif_per_jam, $unit_id;
    public $showModal = false;
    public $isEdit = false;

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses Ditolak. Hanya Pemilik yang boleh membuka halaman ini.');
        }
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->reset(['nama', 'jenis_konsol', 'tarif_per_jam', 'unit_id']);
        $this->isEdit = false;
    }

    public function edit($id)
    {
        $unit = Unit::find($id);
        $this->unit_id = $unit->id;
        $this->nama = $unit->nama;
        $this->jenis_konsol = $unit->jenis_konsol;
        $this->tarif_per_jam = $unit->tarif_per_jam;
        
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function simpan()
    {
        $this->validate([
            'nama' => 'required',
            'jenis_konsol' => 'required',
            'tarif_per_jam' => 'required|numeric|min:0',
        ]);

        if ($this->isEdit) {
            Unit::find($this->unit_id)->update([
                'nama' => $this->nama,
                'jenis_konsol' => $this->jenis_konsol,
                'tarif_per_jam' => $this->tarif_per_jam,
            ]);
            session()->flash('message', 'Unit berhasil diperbarui!');
        } else {
            Unit::create([
                'nama' => $this->nama,
                'jenis_konsol' => $this->jenis_konsol,
                'tarif_per_jam' => $this->tarif_per_jam,
                'is_active' => true,
            ]);
            session()->flash('message', 'Unit baru berhasil ditambahkan!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function hapus($id)
    {
        Unit::find($id)->delete();
        session()->flash('message', 'Unit berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $unit = Unit::find($id);
        $unit->update(['is_active' => !$unit->is_active]);
    }

    public function render()
    {
        return view('livewire.unit-manager', [
            'units' => Unit::all()
        ])->layout('layouts.app');
    }
}