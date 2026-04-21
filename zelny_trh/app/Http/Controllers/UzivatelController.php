<?php

namespace App\Http\Controllers;

use App\Models\Uzivatel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UzivatelController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {   
        // Check if the user is a guest or a general user (not admin)
        if (Auth::guest() || Auth::user()->urole !== 'Admin') {
            // Farmers are users who have created at least one offer
            $farmers = Uzivatel::whereHas('nabidky')->get(); // Ensure the 'nabidky' relationship is defined in the Uzivatel model
    
            return view('uzivatele.index', compact('farmers'));
        } 
        // Check if the user is an admin
        else if (Auth::user()->urole === 'Admin') {
            // Get all users
            $users = Uzivatel::all();
    
            // Show the list of all users
            return view('uzivatele.index', compact('users'));
        }
    }
    
    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('uzivatele.create');
    }

    /**
     * Store a newly created user in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'prihlasovaci_jmeno' => 'required|unique:Uzivatel,prihlasovaci_jmeno|max:100',
            'heslo' => 'required|min:8',
            'jmeno' => 'required|max:100',
            'urole' => 'required|in:Admin,Moderator,Farmar,Zakaznik',
            'email' => 'required|email|unique:Uzivatel,email|max:100',
            'datum_narozeni' => 'nullable|date',
            'adresa' => 'nullable|max:100',
            'dalsi_osobni_udaje' => 'nullable|max:100'
        ]);
    
        // Pokud validace projde, vytvoří uživatele
        Uzivatel::create([
            'prihlasovaci_jmeno' => $request->prihlasovaci_jmeno,
            'heslo' => Hash::make($request->heslo),
            'jmeno' => $request->jmeno,
            'urole' => $request->urole,
            'email' => $request->email,
            'datum_narozeni' => $request->datum_narozeni,
            'adresa' => $request->adresa,
            'dalsi_osobni_udaje' => $request->dalsi_osobni_udaje,
        ]);
    
        return redirect()->route('uzivatele.index')->with('success', 'Uživatel byl úspěšně vytvořen.');
    }
    

    /**
     * Display the specified user.
     */
    public function show($prihlasovaci_jmeno)
    {   
        //$uzivatel = Uzivatel::where('prihlasovaci_jmeno', $prihlasovaci_jmeno)->firstOrFail();
        $uzivatel = Uzivatel::with('nabidky')->findOrFail($prihlasovaci_jmeno);

        return view('uzivatele.show', compact('uzivatel'));
    }
    

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = Uzivatel::findOrFail($id);
        return view('uzivatele.edit', compact('user'));
    }

    /**
     * Update the specified user in the database.
     */
    public function update(Request $request, $id)
    {
        $user = Uzivatel::findOrFail($id);

        $request->validate([
            'heslo' => 'nullable|min:8',
            'jmeno' => 'required|max:100',
            'urole' => 'required|in:Admin,Moderator,Farmar,Zakaznik',
            'email' => 'required|email|max:100|unique:Uzivatel,email,' . $user->prihlasovaci_jmeno . ',prihlasovaci_jmeno',
            'datum_narozeni' => 'nullable|date',
            'adresa' => 'nullable|max:100',
            'dalsi_osobni_udaje' => 'nullable|max:100'
        ]);

        $user->update([
            'heslo' => $request->heslo ? Hash::make($request->heslo) : $user->heslo,
            'jmeno' => $request->jmeno,
            'urole' => $request->urole,
            'email' => $request->email,
            'datum_narozeni' => $request->datum_narozeni,
            'adresa' => $request->adresa,
            'dalsi_osobni_udaje' => $request->dalsi_osobni_udaje,
        ]);

        return redirect()->route('uzivatele.index')->with('success', 'Uživatel byl aktualizován.');
    }

    public function manage()
    {
        // Retrieve all users
        $users = Uzivatel::all();

        // Pass the users to the manage view
        return view('uzivatele.manage', compact('users'));
    }

    /**
     * Remove the specified user from the database.
     */
    public function destroy($id)
    {
        $user = Uzivatel::findOrFail($id);
        $user->delete();

        return redirect()->route('uzivatele.index')->with('success', 'Uživatel byl smazán.');
    }
}
