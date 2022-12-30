<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'pessoa_id', 'nomeUsuario', 'senhaUsuario',
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
