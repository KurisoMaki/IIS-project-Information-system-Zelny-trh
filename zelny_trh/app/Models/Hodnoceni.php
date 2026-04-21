<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hodnoceni extends Model
{
    protected $table = 'Hodnoceni';
    protected $primaryKey = 'id_hodnoceni';
    public $timestamps = false; // Since no `created_at` and `updated_at` columns exist

    protected $fillable = [
        'id_nabidky',
        'zakaznik',
        'hodnoceni',
        'komentar',
        'datum_hodnoceni',
    ];

    // Relationships
    public function nabidka()
    {
        return $this->belongsTo(Nabidka::class, 'id_nabidky');
    }

    public function zakaznik()
    {
        return $this->belongsTo(Uzivatel::class, 'zakaznik', 'prihlasovaci_jmeno');
    }
    
}
