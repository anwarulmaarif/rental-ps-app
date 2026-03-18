<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email, $password;

    public function login()
    {
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            session()->regenerate();
            return redirect()->route('kasir');
        }

        $this->addError('email', 'Email atau password salah!');
    }

    public function render()
    {
        return view('livewire.login')->layout('layouts.app');
    }
}