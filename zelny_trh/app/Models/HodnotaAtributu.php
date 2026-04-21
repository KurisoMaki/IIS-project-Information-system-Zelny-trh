<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HodnotaAtributu extends Model
{
    protected $table = 'HodnotaAtributu';
    protected $primaryKey = 'id_hodnoty';
    public $timestamps = false;

    protected $fillable = ['id_atributu', 'hodnota'];

    public function atribut()
    {
        return $this->belongsTo(Atribut::class, 'id_atributu');
    }
}
