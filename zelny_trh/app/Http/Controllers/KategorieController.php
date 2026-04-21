<?php

namespace App\Http\Controllers;

use App\Models\Kategorie;
use Illuminate\Http\Request;

class KategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategorie = Kategorie::where('schvaleno', 'ANO')->with('subcategories', 'parentCategory')->get();
        return view('kategorie.index', compact('kategorie'));
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = Kategorie::all();
        return view('kategorie.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nazev' => 'required|string|max:100',
            'popis' => 'nullable|string|max:100',
            'parent' => 'nullable|exists:Kategorie,id_kategorie',
            'foto' => 'nullable|image|max:2048',
        ]);


        $kategorie = new Kategorie($request->all());

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('images', 'public');
            $kategorie->foto = $path;
        }

        $kategorie->save();
        return redirect()->route('kategorie.index')->with('success', 'Kategorie created successfully.');
    }

    /**
     * Display the specified resource.
     */
    // KategorieController.php
    public function show($id)
    {
        $kategorie = Kategorie::with(['parentCategory', 'subcategories', 'nabidky'])->findOrFail($id);
        return view('kategorie.show', compact('kategorie'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $kategorie = Kategorie::findOrFail($id);
        $parentCategories = Kategorie::where('id_kategorie', '!=', $id)->get();
        return view('kategorie.edit', compact('kategorie', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nazev' => 'required|string|max:100',
            'popis' => 'nullable|string|max:100',
            'parent' => 'nullable|exists:Kategorie,id_kategorie',
            'foto' => 'nullable|image|max:2048',
            'schvaleno' => 'nullable|in:ANO,NE',
        ]);

        
        $kategorie = Kategorie::findOrFail($id);
        $kategorie->fill($request->all());

        
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('images', 'public');
            $kategorie->foto = $path;
        }

        $kategorie->save();
        return redirect()->route('kategorie.index')->with('success', 'Kategorie updated successfully.');
    }

    public function manage()
    {
        $kategorie = Kategorie::with('parentCategory', 'subcategories')->get();

        return view('kategorie.manage', compact('kategorie'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the Kategorie and delete it
        $kategorie = Kategorie::findOrFail($id);
        $kategorie->delete();
        return redirect()->route('kategorie.index')->with('success', 'Kategorie deleted successfully.');
    }
}
