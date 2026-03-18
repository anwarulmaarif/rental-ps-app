<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\RentalManager;
use App\Livewire\ReportManager;
use App\Livewire\UnitManager;
use App\Livewire\Login;

// Halaman Login (Hanya untuk yang belum login)
Route::get('/login', Login::class)->name('login')->middleware('guest');

// Kelompokkan semua route yang butuh Login
Route::middleware('auth')->group(function () {
    Route::get('/', RentalManager::class)->name('kasir');
    Route::get('/laporan', ReportManager::class)->name('laporan');
    Route::get('/pengaturan', UnitManager::class)->name('pengaturan');
    
    // Route khusus untuk Logout
    Route::get('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});