<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['nama', 'jenis_konsol', 'tarif_per_jam', 'is_active'];
}