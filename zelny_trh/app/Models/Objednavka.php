<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objednavka extends Model
{
    protected $table = 'Objednavka';
    protected $primaryKey = 'id_objednavky';
    public $timestamps = false;

    protected $fillable = [
        'celkova_cena',
        'stav',
        'datum_vytvoreni',
        'datum_platby',
        'datum_vyrizeni',
        'datum_prebrani',
        'druh_platby',
        'cislo_uctu',
        'vlastnik'
    ];

    // Relationship to Uzivatel
    public function vlastnikUzivatel()
    {
        return $this->belongsTo(Uzivatel::class, 'vlastnik', 'prihlasovaci_jmeno');
    }

    // Relationship to Nabidka through Relace_objednavka_nabidka
    public function nabidky()
    {
        return $this->belongsToMany(
            Nabidka::class,
            'Relace_objednavka_nabidka',
            'id_objednavky',
            'id_nabidky'
        )->withPivot('objem', 'cena');
    }
}
