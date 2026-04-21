<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategorie extends Model
{
    protected $table = 'Kategorie';
    protected $primaryKey = 'id_kategorie';
    public $timestamps = false;

    protected $fillable = [
        'id_kategorie',
        'nazev',
        'popis',
        'foto',
        'schvaleno',
        'parent',
        'navrhl',
        'schvalil',
    ];

    // Relationship for subcategories
    public function subcategories()
    {
        return $this->hasMany(Kategorie::class, 'parent')->where('schvaleno', 'ANO');;
    }

    // Relationship for parent category
    public function parentCategory()
    {
        return $this->belongsTo(Kategorie::class, 'parent');
    }
    
    // User who proposed the category
    public function proposedBy()
    {
        return $this->belongsTo(Uzivatel::class, 'navrhl', 'prihlasovaci_jmeno');
    }

    // User who approved the category
    public function approvedBy()
    {
        return $this->belongsTo(Uzivatel::class, 'schvalil', 'prihlasovaci_jmeno');
    }

    // Offers in this category
    public function nabidky()
    {
        return $this->hasMany(Nabidka::class, 'id_kategorie', 'id_kategorie');
    }
}
