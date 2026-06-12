<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CustomerAccountController extends Controller
{
    /**
     * Display the customer profile page.
     */
    public function profile()
    {
        $user = auth()->user();
        return view('frontliner.account.profile', compact('user'));
    }

    /**
     * Update the customer profile info.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Username tidak boleh menggunakan format email.');
                    }
                },
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
        ]);

        $user->name = $request->input('name');
        $user->username = Str::lower($request->input('username'));
        $user->email = $request->input('email');
        $user->save();

        return redirect()->route('customer.profile')
            ->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Display the customer settings (security) page.
     */
    public function settings()
    {
        return view('frontliner.account.settings');
    }

    /**
     * Update the customer password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'old_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'old_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!Hash::check($request->input('old_password'), $user->password)) {
            return back()->withErrors(['old_password' => 'Password lama yang Anda masukkan salah.']);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('customer.settings')
            ->with('success', 'Password Anda berhasil diperbarui.');
    }

    /**
     * Display the customer payment history page.
     */
    public function payments()
    {
        $payments = PaymentHistory::whereHas('rental', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->with(['rental.car'])
        ->latest()
        ->get();

        return view('frontliner.account.payments', compact('payments'));
    }
}
