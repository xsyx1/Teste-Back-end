<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function scopePerson($query)
    {
        return $query->select(
            'usuarios.*',
            'people.nome',
            'people.cpf_cnpj'
        )
            ->join('pessoas', 'pessoas.id', '=', 'usuarios.person_id');
    }

}
