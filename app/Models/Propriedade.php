<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Propriedade extends Model
{
    protected $fillable = [
        'nomePropriedade', 'cadastroRural',
    ];
}
