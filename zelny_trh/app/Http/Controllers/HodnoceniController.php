<?php

namespace App\Http\Controllers;

use App\Models\Hodnoceni;
use App\Models\Nabidka;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HodnoceniController extends Controller
{
    // Display a listing of the reviews
    public function index(Request $request)
    {
        $id_nabidky = $request->get('id_nabidky');
    
        if ($id_nabidky) {
            // Načíst hodnocení pro konkrétní nabídku
            $hodnoceni = Hodnoceni::with(['nabidka', 'zakaznik'])
                ->where('id_nabidky', $id_nabidky)
                ->get();
        } else {
            // Načíst všechna hodnocení
            $hodnoceni = Hodnoceni::with(['nabidka', 'zakaznik'])->get();
        }
    
        return view('hodnoceni.index', compact('hodnoceni', 'id_nabidky'));
    }
    
    

    public function create(Request $request)
    {
        if (Auth::guest()) {
            // Redirect with error message for non-authenticated users
            return back()->with('modalError', 'Pro přidání hodnocení se musíte přihlásit!');
        }

        $selectedNabidka = $request->nabidka; // Get the selected Nabídka ID
        $zakaznik = Auth::user()->prihlasovaci_jmeno; // Get the logged-in user's username

        // Check if the user has purchased the product
        $hasPurchased = \App\Models\Objednavka::where('vlastnik', $zakaznik)
            ->whereHas('nabidky', function ($query) use ($selectedNabidka) {
                $query->where('Relace_objednavka_nabidka.id_nabidky', $selectedNabidka);
            })
            ->exists();

        if (!$hasPurchased) {
            // Redirect back if the user hasn't purchased the product
            return back()->with('modalError', 'Můžete ohodnotit pouze produkty, které jste zakoupili.');
        }

        $nabidka = \App\Models\Nabidka::find($selectedNabidka); // Fetch the selected Nabídka details

        return view('hodnoceni.create', compact('nabidka'));
    }

    // Store a newly created review in storage
    public function store(Request $request)
    {
        $request->validate([
            'id_nabidky' => 'required|exists:Nabidka,id_nabidky',
            'hodnoceni' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);
    
        $zakaznik = Auth::user()->prihlasovaci_jmeno;
    
        // Save the review
        \App\Models\Hodnoceni::create([
            'id_nabidky' => $request->id_nabidky,
            'zakaznik' => $zakaznik,
            'hodnoceni' => $request->hodnoceni,
            'komentar' => $request->komentar,
        ]);
    
        return redirect()->route('nabidky.index')->with('success', 'Hodnocení bylo úspěšně přidáno.');
    }
    
    
    // Display the specified review
    public function show($id)
    {
        $hodnoceni = Hodnoceni::with(['nabidka', 'zakaznik'])->findOrFail($id);
        
        return view('hodnoceni.show', compact('hodnoceni'));
    }

    // Show the form for editing the specified review
    public function edit($id)
    {
        $hodnoceni = Hodnoceni::findOrFail($id);
        $nabidky = Nabidka::all(); // Fetch all Nabidky for dropdown
        
        return view('hodnoceni.edit', compact('hodnoceni', 'nabidky'));
    }
    

    // Update the specified review in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_nabidky' => 'required|exists:Nabidka,id_nabidky',
            'zakaznik' => 'required|string|exists:Uzivatel,prihlasovaci_jmeno',
            'hodnoceni' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);

        $hodnoceni = Hodnoceni::findOrFail($id);
        $hodnoceni->update($request->all());

        return redirect()->route('hodnoceni.index')->with('success', 'Hodnocení bylo úspěšně aktualizováno.');
    }

    // Remove the specified review from storage
    public function destroy($id)
    {
        $hodnoceni = Hodnoceni::findOrFail($id);
        $hodnoceni->delete();

        return redirect()->route('hodnoceni.index')->with('success', 'Hodnocení bylo úspěšně smazáno.');
    }

    public function manage(Request $request)
    {
        $id_nabidky = $request->get('id_nabidky');
    
        if ($id_nabidky) {
            // Načíst hodnocení pro konkrétní nabídku
            $hodnoceni = Hodnoceni::with(['nabidka', 'zakaznik'])
                ->where('id_nabidky', $id_nabidky)
                ->get();
        } else {
            // Načíst všechna hodnocení
            $hodnoceni = Hodnoceni::with(['nabidka', 'zakaznik'])->get();
        }
    
        return view('hodnoceni.manage', compact('hodnoceni', 'id_nabidky'));
    }
}
