<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rental extends Model
{
    // Supaya Laravel tahu kolom mana saja yang boleh diisi manual
    protected $fillable = [
        'nama_unit', 'nama_penyewa', 'durasi', 
        'tarif_per_jam', 'total_biaya', 'jam_mulai', 
        'jam_selesai', 'is_deleted', 'tgl_hapus', 'is_lunas'
    ];

    // LOGIC: Mengubah 0,5 (koma) menjadi 0.5 (titik) sebelum masuk ke DB
    public function setDurasiAttribute($value)
    {
        $formatted = str_replace(',', '.', $value);
        $this->attributes['durasi'] = (float) $formatted;
    }

}