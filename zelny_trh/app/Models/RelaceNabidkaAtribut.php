<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RelaceNabidkaAtribut extends Pivot
{
    protected $table = 'Relace_nabidka_atribut';
    public $timestamps = false;

    protected $fillable = ['id_atributu', 'id_nabidky', 'id_hodnoty'];

    public function atribut()
    {
        return $this->belongsTo(Atribut::class, 'id_atributu');
    }

    public function nabidka()
    {
        return $this->belongsTo(Nabidka::class, 'id_nabidky');
    }

    public function hodnota()
    {
        return $this->belongsTo(HodnotaAtributu::class, 'id_hodnoty');
    }
}

