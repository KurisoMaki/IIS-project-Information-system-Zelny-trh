<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nabidka;
use App\Models\Atribut;
use App\Models\Kategorie;
use App\Models\RelaceNabidkaAtribut;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;

class NabidkaController extends Controller
{
    // Display a listing of the resource
    public function index(Request $request)
    {
        // Načtení dat pro zobrazení ve filtrech
        $kategorie = Kategorie::all(); // Kategorie pro zobrazení ve filtru
        $atributy = Atribut::with('hodnoty')->get(); // Atributy s hodnotami
    
        // Základní dotaz na nabídky
        $nabidkyQuery = Nabidka::with(['kategorie', 'atributy.hodnoty', 'vlastnik', 'hodnoceni']);
    
        // Filtrování podle kategorie (včetně podkategorií)
        if ($request->filled('kategorie')) {
            $selectedCategoryId = $request->input('kategorie');
    
            // Získání všech podkategorií vybrané kategorie
            $allCategoryIds = $this->getCategoryAndSubcategoryIds($selectedCategoryId);
    
            // Filtrování nabídek podle ID kategorií
            $nabidkyQuery->whereIn('id_kategorie', $allCategoryIds);
        }
    
        // Filtrování podle cenového rozmezí
        if ($request->filled('cena_od')) {
            $nabidkyQuery->where('cena', '>=', $request->input('cena_od'));
        }
        if ($request->filled('cena_do')) {
            $nabidkyQuery->where('cena', '<=', $request->input('cena_do'));
        }

        // Filtrování podle hodnot atributů
        if ($request->filled('hodnoty')) {
            $selectedValues = $request->input('hodnoty');

            $nabidkyQuery->whereHas('atributy', function ($query) use ($selectedValues) {
                $query->whereIn('Relace_nabidka_atribut.id_hodnoty', $selectedValues);
            });
        }
    
        // Filtrování pouze pro samosběr
        if ($request->filled('samozber')) {
            $nabidkyQuery->where('samozber', 'ANO');
        }
    
        // Řazení podle ceny
        if ($request->filled('razeni')) {
            $nabidkyQuery->orderBy('cena', $request->input('razeni'));
        }
    
        // Vyhledávání podle názvu
        if ($request->filled('nazev')) {
            $nabidkyQuery->where('nazev', 'like', '%' . $request->input('nazev') . '%');
        }
    
        // Načtení filtrovaných nabídek
        $nabidky = $nabidkyQuery->get();
    
        // Zobrazení view s filtrovanými daty
        return view('nabidky.index', compact('nabidky', 'kategorie', 'atributy'));
    }
    
    
    // Rekurzivní funkce pro získání ID všech podkategorií
    private function getCategoryAndSubcategoryIds($categoryId)
    {
        $category = Kategorie::with('subcategories')->find($categoryId);
    
        if (!$category) {
            return [$categoryId];
        }
    
        $ids = [$categoryId];
        foreach ($category->subcategories as $subcategory) {
            $ids = array_merge($ids, $this->getCategoryAndSubcategoryIds($subcategory->id_kategorie));
        }
    
        return $ids;
    }
    

    // Show the form for creating a new resource
    public function create(Request $request)
    {
        $typ = $request->query('typ', 'normalni');
        $parentCategories = Kategorie::all();
        $atributy = Atribut::with('hodnoty')->get();
        return view('nabidky.create', compact('typ', 'parentCategories', 'atributy'));
    }
    

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nazev' => 'required|string|max:255',
            'popis' => 'nullable|string',
            'misto_puvodu' => 'nullable|string',
            'mnozstvi' => 'integer|min:0',
            'cena' => 'required|numeric|min:0',
            'druh_ceny' => 'required|in:HMOTNOST,KUSY',
            'trvanlivost' => 'nullable|date',
            'samozber' => 'nullable|in:ANO,NE',
            'lokalita' => 'nullable|string',
            'cas_od' => 'nullable|date',
            'cas_do' => 'nullable|date',
            // 'id_kategorie' => 'exists:Kategorie,id_kategorie', // Single category ID validation
            'id_kategorie' => 'nullable|exists:Kategorie,id_kategorie', // Validace kategorie
        ]);
    
        // Add the current user as the owner
        $validated['vlastnik'] = Auth::user()->prihlasovaci_jmeno;
    
        // Create a new Nabidka
        $nabidka = Nabidka::create($validated);
    
        // Handle attributes
        if ($request->has('atributy')) {
            foreach ($request->input('atributy') as $idAtributu => $idHodnoty) {
                if ($idHodnoty) {
                    RelaceNabidkaAtribut::create([
                        'id_nabidky' => $nabidka->id_nabidky,
                        'id_atributu' => $idAtributu,
                        'id_hodnoty' => $idHodnoty,
                    ]);
                }
            }
        }
    
        return redirect()->route('nabidky.index')->with('success', 'Nabídka byla úspěšně vytvořena.');
    }
    
    
    
    // Display the specified resource
    public function show($id)
    {
        $nabidka = Nabidka::with(['kategorie', 'atributy.hodnoty'])->findOrFail($id);
        return view('nabidky.show', compact('nabidka'));
    }
    

    // Show the form for editing the specified resource
    // public function edit($id)
    // {
    //     $nabidka = Nabidka::with('atributy')->findOrFail($id); // Fetch nabídka with its attributes
    //     $atributy = Atribut::with('hodnoty')->get(); // Fetch all attributes with their values
    //     $rootCategories = Kategorie::whereNull('parent')->with('subcategories')->get();

    //     return view('nabidky.edit', compact('nabidka', 'atributy', 'rootCategories'));
    // }
    public function edit($id) 
    {
        $nabidka = Nabidka::with(['kategorie', 'atributy'])->findOrFail($id);
    
        // Build the full hierarchy path for the selected category
        $categoriesChain = [];
        $currentCategory = $nabidka->kategorie;
        while ($currentCategory) {
            $categoriesChain[] = $currentCategory;
            $currentCategory = $currentCategory->parentCategory; // Assumes 'parentCategory' relationship exists
        }
        $categoriesChain = array_reverse($categoriesChain); // Reverse to start from the root
    
        // Fetch all categories for the dropdown
        $allCategories = Kategorie::all();
    
        // Fetch all attributes with their possible values
        $atributy = Atribut::with('hodnoty')->get();
    
        return view('nabidky.edit', [
            'nabidka' => $nabidka,
            'categoriesChain' => $categoriesChain,
            'allCategories' => $allCategories, // Pass all categories for the dropdown
            'atributy' => $atributy,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming data, ensuring required fields like `id_kategorie` are properly set
        // and other fields adhere to the necessary constraints, including relationships.
        $validated = $request->validate([
            'nazev' => 'sometimes|string|max:255',
            'popis' => 'sometimes|nullable|string',
            'mnozstvi' => 'sometimes|integer|min:0',
            'cena' => 'sometimes|numeric|min:0',
            'druh_ceny' => 'sometimes|in:HMOTNOST,KUSY',
            'id_kategorie' => 'sometimes|integer|exists:Kategorie,id_kategorie',
            'atributy' => 'nullable|array',
        ]);
    
        // Retrieve the `Nabidka` instance by its ID, ensuring it exists and throwing an error if not.
        $nabidka = Nabidka::findOrFail($id);
    
        // Update the main fields of the model, including the category field, if present in the request;
        // the `only` method ensures no extraneous data is passed to the model.
        $nabidka->update($request->only([
            'nazev', 'popis', 'mnozstvi', 'cena', 'druh_ceny', 'id_kategorie'
        ]));
    
        // If the request contains attribute data, iterate over the attributes to build synchronization data;
        // this ensures that only valid and provided attributes are added or updated in the pivot table.
        if ($request->has('atributy')) {
            $atributy = $request->input('atributy');
            $syncData = [];
    
            foreach ($atributy as $idAtributu => $idHodnoty) {
                if ($idHodnoty) {
                    // Include the attribute value pair in the synchronization data
                    $syncData[$idAtributu] = ['id_hodnoty' => $idHodnoty];
                }
            }
    
            // Sync the pivot table for attributes, removing any that are no longer associated
            // with the current `Nabidka` while ensuring the current ones are properly linked.
            $nabidka->atributy()->sync($syncData);
        }
    
        return redirect()->route('dashboard')->with('success', 'Nabídka byla úspěšně aktualizována.');
        // return redirect()->route('nabidky.index')->with('success', 'Nabídka byla úspěšně aktualizována.');
    } 

    // Remove the specified resource from storage
    public function destroy($id)
    {
        $nabidka = Nabidka::findOrFail($id);
        $nabidka->delete();
    
        return redirect()->route('nabidky.index')->with('success', 'Nabídka deleted successfully.');
    }


    public function pridatObjednavku(Request $request, $id_nabidky)
    {
        // Validace vstupních dat
        $validatedData = $request->validate([
            'id_objednavky' => 'required|exists:Objednavka,id_objednavky',
            'objem' => 'required|integer|min:1',
            'cena' => 'required|numeric|min:0',
        ]);

        // Najdi nabídku
        $nabidka = Nabidka::findOrFail($id_nabidky);

        // Přidání nové objednávky do pivot tabulky
        $nabidka->objednavky()->attach($validatedData['id_objednavky'], [
            'objem' => $validatedData['objem'],
            'cena' => $validatedData['cena'],
        ]);

        return redirect()->route('nabidky.show', $id_nabidky)
            ->with('success', 'Objednávka byla úspěšně přidána k nabídce.');
    }

    public function manage()
    {
        // Retrieve all offers with their owner details (if needed)
        $nabidky = Nabidka::with('vlastnik')->get();

        // Pass the offers to the manage view
        return view('nabidky.manage', compact('nabidky'));
    }

}
