<?php

namespace App\Http\Controllers;

use App\Models\Kategorie;
use Illuminate\Http\Request;

class ModeratorController extends Controller
{
    /**
     * Show the moderator dashboard.
     */
    public function dashboard()
    {
        // Retrieve all categories
        $kategorie = Kategorie::all();

        return view('moderator.dashboard', compact('kategorie'));
    }

    /**
     * Update the approval status of a category.
     */
    public function updateCategoryStatus(Request $request, $id)
    {
        $request->validate([
            'schvaleno' => 'required|in:ANO,NE',
        ]);

        $kategorie = Kategorie::findOrFail($id);
        $kategorie->schvaleno = $request->schvaleno;
        $kategorie->save();

        return redirect()->route('moderator.dashboard')->with('success', 'Category approval status updated successfully.');
    }
}
