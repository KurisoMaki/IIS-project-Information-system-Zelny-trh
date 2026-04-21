<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atribut extends Model
{
    protected $table = 'Atribut';
    protected $primaryKey = 'id_atributu';
    public $timestamps = false;

    protected $fillable = ['nazev'];

    public function hodnoty()
    {
        return $this->hasMany(HodnotaAtributu::class, 'id_atributu', 'id_atributu');
    }
    

    public function nabidky()
    {
        return $this->belongsToMany(
            Nabidka::class,
            'Relace_nabidka_atribut', // Explicit table name
            'id_atributu',
            'id_nabidky'
        )->withPivot('id_hodnoty');
    }
}

