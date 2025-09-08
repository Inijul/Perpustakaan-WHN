<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Jika sudah login, redirect ke dashboard
        if (session('admin_logged_in')) {
            return redirect('/dashboard');
        }
        
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        // Query langsung ke database admin
        $admin = DB::table('admin')
            ->where('email', $request->email)
            ->first();

        if ($admin && $admin->password === $request->password) {
            // Login berhasil - simpan data admin di session
            session([
                'admin_logged_in' => true,
                'admin_nip' => $admin->nip,
                'admin_email' => $admin->email
            ]);

            Log::info('Admin logged in successfully', [
                'email' => $request->email,
                'nip' => $admin->nip,

            ]);



            return redirect('/dashboard');
        }

        Log::warning('Failed login attempt', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        // Hapus session admin
        session()->forget(['admin_logged_in', 'admin_nip', 'admin_email']);
        
        return redirect('/login');
    }


}
