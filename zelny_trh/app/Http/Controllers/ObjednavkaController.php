<?php

namespace App\Http\Controllers;

use App\Models\Objednavka;
use App\Models\Uzivatel;
use App\Models\Nabidka;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ObjednavkaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $objednavky = Objednavka::with(['vlastnikUzivatel', 'nabidky'])->get();
            // Načítání objednávek se samozběrem
        $samozberObjednavky = Objednavka::whereHas('nabidky', function ($query) {
            $query->where('samozber', 'ANO');
        })->with(['nabidky' => function ($query) {
            $query->with('atributy'); // Zahrnuje atributy nabídek
        }])->get();
        return view('objednavky.index', compact('objednavky', 'samozberObjednavky'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (Auth::guest()) {
            return back()->with('modalError', 'Pro vytvoření objednávky se musíte přihlásit!');
        }
    
        $uzivatel = Auth::user()->prihlasovaci_jmeno;
        $nabidky = Nabidka::all();
        $vybranaNabidka = null;
    
        // Zkontroluj, zda je předán parametr 'nabidka'
        if ($request->has('nabidka')) {
            $vybranaNabidka = Nabidka::find($request->input('nabidka'));
        }
    
        return view('objednavky.create', compact('uzivatel', 'nabidky', 'vybranaNabidka'));
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'datum_vytvoreni' => now()->toDateString(),
            'stav' => $request->input('stav', 'vytvorena'), // Defaults to 'vytvorena'
            'celkova_cena' => $request->input('celkova_cena', 0) ?? 0,
        ]);
    
        // Validace základních polí objednávky
        $validatedData = $request->validate([
            'celkova_cena' => 'required|integer|min:0',
            'stav' => 'required|string',
            'datum_vytvoreni' => 'required|date',
            'druh_platby' => 'required|string',
        ]);
        
        $validatedData['vlastnik'] = Auth::user()->prihlasovaci_jmeno;
    
        // Rozlišení režimu (samosběr nebo standardní objednávka)
        if ($request->has('id_nabidky')) {
            // **Samosběr**: Přímé uložení objednávky
            $objednavka = Objednavka::create($validatedData);
    
            $objednavka->nabidky()->attach($request->input('id_nabidky'), [
                'objem' => 0, // Samosběr nemá objem
                'cena' => 0,  // Cena 0 pro samosběr
            ]);
    
            return redirect()->route('objednavky.index')->with('success', 'Objednávka byla úspěšně vytvořena.');
        } else {
            // **Standardní objednávka**: Validace vyplněných nabídek
            $nabidky = array_filter($request->input('nabidky', []), function ($nabidka) {
                return !empty($nabidka['objem']);
            });
    
            // Validace pouze vyplněných nabídek
            foreach ($nabidky as $index => $nabidkaData) {
                $request->validate([
                    "nabidky.$index.id_nabidky" => 'required|exists:Nabidka,id_nabidky',
                    "nabidky.$index.objem" => 'required|integer|min:1',
                ]);
            }
    
            if (empty($nabidky)) {
                return redirect()->back()->withErrors([
                    'error' => 'Musíte vyplnit alespoň jednu nabídku.',
                ])->withInput();
            }
    
            // Uložení objednávky
            $objednavka = Objednavka::create($validatedData);
    
            foreach ($nabidky as $nabidkaData) {
                $nabidka = Nabidka::findOrFail($nabidkaData['id_nabidky']);
    
                if ($nabidka->mnozstvi < $nabidkaData['objem']) {
                    return redirect()->back()->withErrors([
                        "error" => "Nabídka ID {$nabidka->id_nabidky} nemá dostatečné množství.",
                    ])->withInput();
                }
    
                $nabidka->mnozstvi -= $nabidkaData['objem'];
                $nabidka->save();
    
                $objednavka->nabidky()->attach($nabidkaData['id_nabidky'], [
                    'objem' => $nabidkaData['objem'],
                    'cena' => $nabidka->cena,
                ]);
            }
    
            return redirect()->route('objednavky.index')->with('success', 'Objednávka byla úspěšně vytvořena.');
        }
    }
    
    
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $objednavka = Objednavka::with(['vlastnikUzivatel', 'nabidky'])->findOrFail($id);
        return view('objednavky.show', compact('objednavka'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $objednavka = Objednavka::findOrFail($id);
        $nabidky = $objednavka->nabidky;
        
        return view('objednavky.edit', compact('objednavka', 'nabidky'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'celkova_cena' => 'required|integer|min:0',
            'stav' => 'required|string',
            'datum_vytvoreni' => 'required|date',
            'druh_platby' => 'required|string',

            'nabidky' => 'required|array',
            'nabidky.*.id_nabidky' => 'required|exists:Nabidka,id_nabidky',
            'nabidky.*.objem' => 'nullable|integer|min:1',
        ]);

        // Přidání vlastníka k validovaným datům
        // $validatedData['vlastnik'] = Auth::user()->prihlasovaci_jmeno;


        $objednavka = Objednavka::findOrFail($id);

        // Vrácení původních množství nabídek
        foreach ($objednavka->nabidky as $nabidka) {
            $nabidka->mnozstvi += $nabidka->pivot->objem;
            $nabidka->save();
        }

        // Aktualizace objednávky
        $objednavka->update($request->only(['celkova_cena', 'stav', 'datum_vytvoreni', 'druh_platby']));

        $pivotData = [];
        foreach ($request->input('nabidky') as $nabidkaData) {
            $nabidka = Nabidka::findOrFail($nabidkaData['id_nabidky']);

            if ($nabidka->mnozstvi < $nabidkaData['objem']) {
                return redirect()->back()->withErrors(['error' => "Nabídka ID {$nabidka->id_nabidky} nemá dostatečné množství."]);
            }

            // Snížení množství
            $nabidka->mnozstvi -= $nabidkaData['objem'];
            $nabidka->save();

            // Data pro pivot tabulku
            $pivotData[$nabidkaData['id_nabidky']] = [
                'objem' => $nabidkaData['objem'],
                'cena' => $nabidka->cena,
            ];
        }

        // Synchronizace pivot tabulky
        $objednavka->nabidky()->sync($pivotData);

        return redirect()->route('objednavky.index')->with('success', 'Objednávka byla úspěšně aktualizována.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $objednavka = Objednavka::findOrFail($id);
        $objednavka->delete();
        
        return redirect()->route('objednavky.index')->with('success', 'Objednávka deleted successfully.');
    }


    public function updateForFarmer(Request $request, string $id)
    {
        // Validate the request
        $request->validate([
            'stav' => 'required|string',
        ]);

        // Find the order
        $objednavka = Objednavka::findOrFail($id);

        // Check if the logged-in user owns any offers in this order
        $user = Auth::user();
        $isOwner = $objednavka->nabidky->contains(function ($nabidka) use ($user) {
            return $nabidka->vlastnik === $user->prihlasovaci_jmeno;
        });

        if (!$isOwner) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to update this order.');
        }

        // Update the order status
        $objednavka->update([
            'stav' => $request->stav,
        ]);

        return redirect()->route('dashboard')->with('success', 'Order status updated successfully.');
    }

    public function manage()
    {
        $objednavky = Objednavka::with(['vlastnikUzivatel', 'nabidky'])->get();
        return view('objednavky.manage', compact('objednavky'));
    }

}
