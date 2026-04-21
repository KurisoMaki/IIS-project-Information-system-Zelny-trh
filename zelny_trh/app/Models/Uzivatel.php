<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Uzivatel extends Authenticatable
{
    use Notifiable;

    protected $table = 'Uzivatel';
    protected $primaryKey = 'prihlasovaci_jmeno';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'prihlasovaci_jmeno',
        'heslo',
        'jmeno',
        'urole',
        'email',
        'datum_narozeni',
        'adresa',
        'dalsi_osobni_udaje'
    ];

    protected $hidden = [
        'heslo',
    ];

    protected $casts = [
        'datum_narozeni' => 'date',
        'urole' => 'string',
    ];

    // Předefinování atributu hesla
    public function setPasswordAttribute($value)
    {
        $this->attributes['heslo'] = bcrypt($value);
    }

    public function hasRole($role)
    {
        return $this->urole === $role;
    }

    public function getAuthPassword()
    {
        return $this->heslo;
    }
    
    public function getAuthIdentifierName()
    {
        return 'email';
    }

    public function nabidky()
    {
        return $this->hasMany(Nabidka::class, 'vlastnik'); // Replace 'vlastnik_id' with the actual foreign key
    }


}
