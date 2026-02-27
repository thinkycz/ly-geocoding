<?php

namespace App\Http\Controllers;

use App\Support\AdminUnlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUnlockController extends Controller
{
    public function create()
    {
        return view('admin.unlock');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $hash = (string) config('admin.password_hash');
        $plain = (string) config('admin.password_plain');

        if ($hash === '' && $plain === '') {
            return back()->withErrors([
                'password' => 'Admin password is not configured. Set ADMIN_PASSWORD or ADMIN_PASSWORD_HASH in .env.',
            ]);
        }

        $input = (string) $request->input('password');

        $matches = $hash !== ''
            ? Hash::check($input, $hash)
            : hash_equals($plain, $input);

        if (!$matches) {
            return back()
                ->withErrors(['password' => 'Invalid password.'])
                ->onlyInput('password');
        }

        $request->session()->regenerate();
        AdminUnlock::unlock();

        return redirect()->intended(route('companies.index'));
    }

    public function destroy(Request $request)
    {
        AdminUnlock::lock();
        $request->session()->regenerate();

        return redirect()->route('companies.index');
    }
}
