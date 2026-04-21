<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Nabidka;
use App\Models\Objednavka;


class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $nabidky = Nabidka::where('vlastnik', $user->prihlasovaci_jmeno)->get();
        $objednavky = Objednavka::where('vlastnik', $user->prihlasovaci_jmeno)->get();

        $objednavkyKZpracovani = Objednavka::whereHas('nabidky', function ($query) use ($user) {
            $query->where('vlastnik', $user->prihlasovaci_jmeno);
        })->get();

        $samozberObjednavky = Objednavka::whereHas('nabidky', function ($query) {
            $query->where('samozber', 'ANO');
        })->where('vlastnik', $user->prihlasovaci_jmeno)->get();
        
        return view('user.dashboard', compact('user', 'nabidky', 'objednavky', 'objednavkyKZpracovani', 'samozberObjednavky'));
    }

    // public function index()
    // {
    //     // Get the current user's offers
    //     $userOffers = Auth::user()->prihlasovaci_jmeno;

    //     // Retrieve orders related to the user's offers
    //     $objednavkyKZpracovani = 

    //     // Pass data to the dashboard view
    //     return view('dashboard', [
    //         'objednavkyKZpracovani' => $objednavkyKZpracovani,
    //     ]);
    // }
}
