<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class PasswordChangeController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('Auth/ChangePassword', [
            'forced' => (bool) $request->user()->must_change_password,
        ]);
    }

    public function update(Request $request)
    {
        $forced = (bool) $request->user()->must_change_password;

        $data = $request->validate([
            // Tự đổi (không bị ép) phải nhập mật khẩu hiện tại để bảo mật.
            'current_password' => [$forced ? 'nullable' : 'required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8), 'different:current_password'],
        ]);

        $request->user()->update([
            'password' => Hash::make($data['password']),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Đã đổi mật khẩu thành công!');
    }
}
