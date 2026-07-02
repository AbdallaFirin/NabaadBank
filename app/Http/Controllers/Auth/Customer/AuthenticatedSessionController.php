<?php

namespace App\Http\Controllers\Auth\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Customer/Auth/Login', [
            'status' => session('status'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::lower($request->string('email')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts('customer-login|'.$throttleKey, 5)) {
            $seconds = RateLimiter::availableIn('customer-login|'.$throttleKey);
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
            ]);
        }

        if (!Auth::guard('customer')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit('customer-login|'.$throttleKey);
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        $customer = Auth::guard('customer')->user();
        if (!$customer->isActive()) {
            Auth::guard('customer')->logout();
            throw ValidationException::withMessages([
                'email' => 'Your account is not active. Please contact the bank.',
            ]);
        }

        RateLimiter::clear('customer-login|'.$throttleKey);
        $request->session()->regenerate();
        session(['last_activity_time' => time()]);

        $customer->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return redirect()->route('customer.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }
}
