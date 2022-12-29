<?php

namespace App\Models;

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

    public function scopePerson($query)
    {
        return $query->select(
            'usuarios.*',
            'pessoas.nome',
            'pessoas.cpf_cnpj'
        )
            ->join('pessoas', 'pessoas.id', '=', 'usuarios.pessoa_id');
    }

}
