<?php

namespace App\Http\Controllers;

use App\Models\Kategorie;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        $kategorie = Kategorie::all();
        return view('admin.dashboard', compact('kategorie'));
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

        return redirect()->route('admin.dashboard')->with('success', 'Category status updated successfully.');
    }
}
