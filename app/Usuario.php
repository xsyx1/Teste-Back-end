<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $fillable = [
        'pessoa_id', 'senhaUsuario',
    ];

    //relacionamento com a tabela de pessoa
    public function pessoa()
	{
		return $this->belongsTo(Pessoa::class, 'pessoa_id');
	}
}
