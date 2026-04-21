<?php

namespace App\Http\Controllers;

use App\Models\HodnotaAtributu;
use App\Models\Atribut;
use Illuminate\Http\Request;

class HodnotaAtributuController extends Controller
{
    public function index($atributId)
    {
        $atribut = Atribut::findOrFail($atributId);
        $hodnoty = HodnotaAtributu::where('id_atributu', $atributId)->get();
        return view('hodnoty.index', compact('atribut', 'hodnoty'));
    }

    public function create($atributId)
    {
        $atribut = Atribut::findOrFail($atributId);
        return view('hodnoty.create', compact('atribut'));
    }

    public function store(Request $request, $atributId)
    {
        $validated = $request->validate([
            'hodnota' => 'required|string|max:100',
        ]);

        $validated['id_atributu'] = $atributId;

        HodnotaAtributu::create($validated);

        return redirect()->route('hodnoty.index', $atributId)->with('success', 'Hodnota byla úspěšně přidána.');
    }

    public function edit($atributId, $id)
    {
        $atribut = Atribut::findOrFail($atributId);
        $hodnota = HodnotaAtributu::findOrFail($id);
        return view('hodnoty.edit', compact('atribut', 'hodnota'));
    }

    public function update(Request $request, $atributId, $id)
    {
        $validated = $request->validate([
            'hodnota' => 'required|string|max:100',
        ]);

        $hodnota = HodnotaAtributu::findOrFail($id);
        $hodnota->update($validated);

        return redirect()->route('hodnoty.index', $atributId)->with('success', 'Hodnota byla úspěšně upravena.');
    }

    public function destroy($atributId, $id)
    {
        $hodnota = HodnotaAtributu::findOrFail($id);
        $hodnota->delete();

        return redirect()->route('hodnoty.index', $atributId)->with('success', 'Hodnota byla úspěšně smazána.');
    }
}
