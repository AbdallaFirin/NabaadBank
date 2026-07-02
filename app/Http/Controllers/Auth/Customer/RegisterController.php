<?php

namespace App\Http\Controllers\Auth\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Customer/Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|max:150|unique:customers,email',
            'phone'         => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:-18 years',
            'gender'        => 'required|in:male,female,other',
            'address'       => 'required|string|max:255',
            'city'          => 'required|string|max:100',
            'occupation'    => 'nullable|string|max:100',
            'nationality'   => 'nullable|string|max:100',
            'password'      => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $customerNumber = 'CUST-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        Customer::create([
            'customer_number' => $customerNumber,
            'name'            => $data['name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'],
            'date_of_birth'   => $data['date_of_birth'],
            'gender'          => $data['gender'],
            'address'         => $data['address'],
            'city'            => $data['city'],
            'occupation'      => $data['occupation'] ?? null,
            'nationality'     => $data['nationality'] ?? null,
            'password'        => Hash::make($data['password']),
            'status'          => 'pending',
        ]);

        return redirect()->route('customer.login')
            ->with('success', 'Registration submitted. Your account is pending bank approval. We will notify you once approved.');
    }
}
