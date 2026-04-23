<?php
// profile.php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads; // ✅ Fix #1: import trait
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Rental;             // ✅ Fix #4
use Carbon\Carbon;                 // ✅ Fix #4

class Profile extends Component
{
    use WithFileUploads;

    // ✅ Fix #2: hapus semua duplikat, tulis sekali saja
    public $file_excel;
    public $showModalAddUser = false;
    public $showModalImportCsv = false;
    public $new_user_name;
    public $new_user_email;
    public $new_user_password;
    public $new_user_role = 'kasir';

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function openModalAddUser()
    {
        $this->reset(['new_user_name', 'new_user_email', 'new_user_password']);
        $this->new_user_role = 'kasir'; // ✅ Fix #5: reset ke default manual
        $this->showModalAddUser = true;
    }

    public function openModalImportCsv()
    {
        $this->showModalImportCsv = true; // ✅ Fix #3: variabel yang benar
    }

    public function simpanUser()
    {
        $this->validate([
            'new_user_name'     => 'required|string|max:255',
            'new_user_email'    => 'required|email|unique:users,email',
            'new_user_password' => 'required|min:6',
            'new_user_role'     => 'required|in:admin,kasir',
        ], [
            'new_user_email.unique'    => 'Email/Username sudah terdaftar.',
            'new_user_password.min'    => 'Password minimal 6 karakter.',
        ]);

        \App\Models\User::create([
            'name'     => $this->new_user_name,
            'email'    => $this->new_user_email,
            'password' => Hash::make($this->new_user_password),
            'role'     => $this->new_user_role,
        ]);

        $this->showModalAddUser = false;
        session()->flash('message', 'User baru berhasil ditambahkan!');
    }

public function importData()
{
    $this->validate([
        'file_excel' => 'required|mimes:csv,txt|max:2048',
    ]);

    $path = $this->file_excel->getRealPath();
    $file = fopen($path, 'r');
    fgetcsv($file, 1000, ","); // Skip header

    $jumlahImport = 0;
    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
        if (count($data) < 6) continue;

        try {
            $tanggal = $data[0];
            $durasi  = (float) str_replace(',', '.', $data[3]);
            $jamMulai = $data[4];
            $tarif   = (int) $data[5]; // <--- SUDAH DIPERBAIKI (Indeks 5)
            
            $total_biaya = $durasi * $tarif;
            $waktuMulai = \Carbon\Carbon::parse($tanggal . ' ' . $jamMulai);

            \App\Models\Rental::create([
                'created_at'    => $waktuMulai, // Tanggal transaksi asli
                'nama_unit'     => $data[1],
                'nama_penyewa'  => $data[2],
                'durasi'        => $durasi,
                'tarif_per_jam' => $tarif,
                'total_biaya'   => $total_biaya,
                'is_lunas'      => true,
                'is_deleted'    => false,
                'jam_mulai'     => $waktuMulai,
                'jam_selesai'   => $waktuMulai->copy()->addMinutes($durasi * 60),
            ]);
            $jumlahImport++;
        } catch (\Exception $e) {
            continue;
        }
    }

    fclose($file);
    $this->reset('file_excel');
    $this->showModalImportCsv = false;
    session()->flash('message', "Berhasil mengimport $jumlahImport data transaksi!");
}

    public function render()
    {
        return view('livewire.profile')->layout('layouts.app');
    }
}