<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nabidka extends Model
{
    protected $table = 'Nabidka';
    protected $primaryKey = 'id_nabidky';
    public $timestamps = false;

    protected $fillable = [
        'nazev', 'popis', 'misto_puvodu', 'mnozstvi', 'cena', 'druh_ceny', 
        'trvanlivost', 'samozber', 'lokalita', 'cas_od', 'cas_do', 
        'schvaleno', 'id_kategorie', 'vlastnik'
    ];

    public function kategorie()
    {
        return $this->belongsTo(Kategorie::class, 'id_kategorie');
    }

    public function uzivatel()
    {
        return $this->belongsTo(Uzivatel::class, 'vlastnik', 'prihlasovaci_jmeno');
    }

    public function atributy()
    {
        return $this->belongsToMany(
            Atribut::class,
            'Relace_nabidka_atribut', // Explicit table name
            'id_nabidky',
            'id_atributu'
        )->withPivot('id_hodnoty');
    }
    
    public function hodnoceni()
    {
        return $this->hasMany(Hodnoceni::class, 'id_nabidky');
    }

    public function vlastnik()
    {
        return $this->belongsTo(Uzivatel::class, 'vlastnik', 'prihlasovaci_jmeno');
    }

    public function getSubcategories($parentId)
    {
        $subcategories = Kategorie::where('parent', $parentId)->get();
        return response()->json(['subcategories' => $subcategories]);
    }

    public function objednavky()
    {
        return $this->belongsToMany(Objednavka::class, 'Relace_objednavka_nabidka', 'id_nabidky', 'id_objednavky');
    }

}
