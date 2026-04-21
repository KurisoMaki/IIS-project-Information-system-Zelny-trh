<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Uzivatel;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        // Get the currently authenticated user
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Validate the input data
        $request->validate([
            'username' => 'required|string|max:100|unique:Uzivatel,prihlasovaci_jmeno,' . $user->prihlasovaci_jmeno . ',prihlasovaci_jmeno',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:Uzivatel,email,' . $user->prihlasovaci_jmeno . ',prihlasovaci_jmeno',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'additional_info' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update the user's information
        $user->prihlasovaci_jmeno = $request->username;
        $user->jmeno = $request->name;
        $user->email = $request->email;
        $user->datum_narozeni = $request->birthday;
        $user->adresa = $request->address;
        $user->dalsi_osobni_udaje = $request->additional_info;

        // Only update the password if provided
        if ($request->filled('password')) {
            $user->heslo = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
