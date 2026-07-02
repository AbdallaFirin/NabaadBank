<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Admin/Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status'           => session('status'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        session(['last_activity_time' => time()]);

        $user = Auth::user();
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        AuditLog::record('login', 'auth', "Staff login: {$user->name} ({$user->staff_id})");

        // If 2FA is enabled, generate a code and redirect to the verification page.
        // The code is stored in the DB; it will be delivered by email once Phase 16
        // (notification system) is implemented.
        if ($user->two_factor_enabled) {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $user->update([
                'two_factor_code'       => $code,
                'two_factor_expires_at' => now()->addMinutes(10),
            ]);

            // TODO Phase 16: dispatch(new \App\Notifications\SendTwoFactorCode($user));

            return redirect()->route('two-factor.show');
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();
        if ($user) AuditLog::record('logout', 'auth', "Staff logout: {$user->name}");
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
