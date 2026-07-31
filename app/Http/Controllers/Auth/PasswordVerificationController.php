<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordVerificationController extends Controller
{
    /**
     * STEP 1 — Tampilkan form input email.
     */
    public function showEmailForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * STEP 1 — Cek apakah email terdaftar, lalu lanjut ke captcha.
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan dalam sistem kami.',
            ])->onlyInput('email');
        }

        // Simpan email yang sudah diverifikasi ke session,
        // dan reset progres captcha sebelumnya (kalau ada).
        $request->session()->put('pwreset.email', $user->email);
        $request->session()->forget(['pwreset.captcha', 'pwreset.captcha_passed']);

        return redirect()->route('password.captcha');
    }

    /**
     * STEP 2 — Tampilkan soal captcha (dibuat sekali per sesi, kecuali salah).
     */
    public function showCaptcha(Request $request)
    {
        if (! $request->session()->has('pwreset.email')) {
            return redirect()->route('password.request');
        }

        if (! $request->session()->has('pwreset.captcha')) {
            $this->generateCaptcha($request);
        }

        $captcha = $request->session()->get('pwreset.captcha');

        return view('auth.verify-captcha', [
            'num_a' => $captcha['a'],
            'num_b' => $captcha['b'],
            'operation' => $captcha['operation'],
        ]);
    }

    /**
     * STEP 2 — Verifikasi jawaban captcha.
     */
    public function verifyCaptcha(Request $request)
    {
        if (! $request->session()->has('pwreset.email')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'captcha_answer' => ['required', 'numeric'],
        ]);

        $captcha = $request->session()->get('pwreset.captcha');

        if (! $captcha || (int) $request->captcha_answer !== (int) $captcha['answer']) {
            // Jawaban salah -> buat soal baru supaya tidak bisa ditebak berulang.
            $this->generateCaptcha($request);

            return redirect()->route('password.captcha')->withErrors([
                'captcha_answer' => 'Jawaban salah, coba soal berikut.',
            ]);
        }

        $request->session()->put('pwreset.captcha_passed', true);
        $request->session()->forget('pwreset.captcha');

        return redirect()->route('password.new');
    }

    /**
     * STEP 3 — Tampilkan form password baru (hanya bisa diakses setelah captcha lolos).
     */
    public function showNewPasswordForm(Request $request)
    {
        if (! $request->session()->get('pwreset.captcha_passed')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password-new');
    }

    /**
     * STEP 3 — Simpan password baru, lalu arahkan kembali ke halaman login.
     */
    public function updatePassword(Request $request)
    {
        if (! $request->session()->get('pwreset.captcha_passed')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $email = $request->session()->get('pwreset.email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Terjadi kesalahan, silakan ulangi proses dari awal.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        // Bersihkan seluruh state alur forgot password dari session.
        $request->session()->forget(['pwreset.email', 'pwreset.captcha', 'pwreset.captcha_passed']);

        return redirect()->route('login')
            ->with('status', 'Password berhasil diubah. Silakan login dengan password baru Anda.');
    }

    /**
     * Membuat soal captcha acak: penjumlahan, pengurangan, atau pembagian.
     * Hasil pembagian selalu dibuat bulat (tidak ada desimal).
     */
    private function generateCaptcha(Request $request): void
    {
        $operation = ['+', '-', '/'][array_rand(['+', '-', '/'])];

        switch ($operation) {
            case '+':
                $a = rand(1, 50);
                $b = rand(1, 50);
                $answer = $a + $b;
                break;

            case '-':
                $a = rand(10, 100);
                $b = rand(1, $a); // pastikan hasil tidak negatif
                $answer = $a - $b;
                break;

            case '/':
            default:
                $b = rand(2, 12);
                $answer = rand(1, 12);
                $a = $b * $answer; // pastikan hasil bulat
                break;
        }

        $request->session()->put('pwreset.captcha', [
            'a' => $a,
            'b' => $b,
            'operation' => $operation,
            'answer' => $answer,
        ]);
    }
}
