<?php

namespace App\Http\Controllers\Auth;

use App\Events\Customer\CustomerRegistered;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    // GCC countries available at launch (India and others to be enabled later)
    public const GCC_COUNTRIES = [
        'AE' => 'United Arab Emirates',
        'SA' => 'Saudi Arabia',
        'KW' => 'Kuwait',
        'BH' => 'Bahrain',
        'QA' => 'Qatar',
        'OM' => 'Oman',
    ];

    public function showRegistrationForm()
    {
        return view('auth.register', ['countries' => self::GCC_COUNTRIES]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'            => ['required', 'string', 'max:20'],
            'whatsapp_number'  => ['nullable', 'string', 'max:20'],
            'country'          => ['required', 'string', 'in:' . implode(',', array_keys(self::GCC_COUNTRIES))],
            'city'             => ['required', 'string', 'max:100'],
            'address'          => ['required', 'string', 'max:500'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'whatsapp_number' => $request->whatsapp_number ?: $request->phone,
            'country'         => $request->country,
            'city'            => $request->city,
            'address'         => $request->address,
            'password'        => Hash::make($request->password),
            'role'            => User::ROLE_CUSTOMER,
            'is_active'       => true,
        ]);

        event(new CustomerRegistered($user));

        Auth::login($user);

        return redirect('/');
    }
}
