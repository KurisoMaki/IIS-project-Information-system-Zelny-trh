<?php

namespace App\Http\Controllers;

use App\Models\Atribut;
use Illuminate\Http\Request;
use App\Models\HodnotaAtributu;

class AtributController extends Controller
{
    public function index()
    {
        // Fetch all attributes with their associated values
        $atributy = Atribut::with('hodnoty')->get();
        
        return view('atributy.index', compact('atributy'));
    }
    

    public function create()
    {
        return view('atributy.create');
    }
    
    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'nazev' => 'required|string|max:100',
            'hodnoty' => 'required|array', // Validate that hodnoty is an array
            'hodnoty.*' => 'required|string|max:100', // Each value should be a string
        ]);
    
        // Create the new attribute
        $atribut = Atribut::create([
            'nazev' => $request->nazev,
        ]);
    
        // Create the values (hodnoty) for the new attribute
        foreach ($request->hodnoty as $hodnota) {
            HodnotaAtributu::create([
                'id_atributu' => $atribut->id_atributu,
                'hodnota' => $hodnota,
            ]);
        }
    
        return redirect()->route('atributy.index')->with('success', 'Atribut byl úspěšně vytvořen.');
    }
    

    public function edit($id)
    {
        $atribut = Atribut::findOrFail($id);
        $hodnoty = HodnotaAtributu::where('id_atributu', $id)->get(); // Fetch all the values for the selected attribute
        return view('atributy.edit', compact('atribut', 'hodnoty'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'nazev' => 'required|string|max:100',
            'hodnoty' => 'array',
            'hodnoty.*' => 'required|string|max:100',
            'hodnoty_id' => 'array',
            'deleted_hodnoty' => 'array',  // Přidání validace pro smazané hodnoty
        ]);
    
        $atribut = Atribut::findOrFail($id);
        $atribut->nazev = $request->nazev;
        $atribut->save();
    
        $existingIds = [];
        
        // Aktualizace hodnot
        if ($request->has('hodnoty')) {
            foreach ($request->hodnoty as $index => $hodnota) {
                $idHodnoty = $request->hodnoty_id[$index] ?? null;
    
                if ($idHodnoty) {
                    // Aktualizace existující hodnoty
                    $hodnotaModel = HodnotaAtributu::find($idHodnoty);
                    if ($hodnotaModel) {
                        $hodnotaModel->hodnota = $hodnota;
                        $hodnotaModel->save();
                        $existingIds[] = $hodnotaModel->id_hodnoty;
                    }
                } else {
                    // Vytvoření nové hodnoty
                    $newHodnota = HodnotaAtributu::create([
                        'id_atributu' => $id,
                        'hodnota' => $hodnota,
                    ]);
                    $existingIds[] = $newHodnota->id_hodnoty;
                }
            }
        }
    
        // Odstranění hodnot, které nebyly v aktuálním požadavku
        HodnotaAtributu::where('id_atributu', $id)
            ->whereNotIn('id_hodnoty', $existingIds)
            ->delete();
    
        // Smazání hodnot, které byly označeny pro smazání
        if ($request->has('deleted_hodnoty')) {
            HodnotaAtributu::whereIn('id_hodnoty', $request->deleted_hodnoty)
                ->where('id_atributu', $id)
                ->delete();
        }
    
        return redirect()->route('atributy.index')->with('success', 'Atribut byl úspěšně upraven.');
    }
    
    
    
    public function show($id)
    {
        // Fetch the attribute with its related values
        $atribut = Atribut::with('hodnoty')->findOrFail($id);
        
        return view('atributy.show', compact('atribut'));
    }


    public function destroy($id)
    {
        $atribut = Atribut::findOrFail($id);
        $atribut->delete();

        return redirect()->route('atributy.index')->with('success', 'Atribut byl úspěšně smazán.');
    }

        // Get the values for a specific attribute
    public function getValues($id)
    {
        $atribut = Atribut::with('hodnoty')->findOrFail($id);
        
        // Return the values as JSON
        return response()->json([
            'values' => $atribut->hodnoty
        ]);
    }

    public function manage()
    {
        // Retrieve all attributes
        $atributy = Atribut::all();

        // Pass the attributes to the manage view
        return view('atributy.manage', compact('atributy'));
    }
}
