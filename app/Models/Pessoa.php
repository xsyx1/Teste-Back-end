<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    protected $fillable = [
        'nome', 'cpf_cnpj',
    ];

    public function produtor(){
    	return $this->hasMany(Produtor::class, 'pessoa_id');
	}

    public function usuario(){
    	return $this->hasMany(Usuario::class, 'pessoa_id');
	}
}
