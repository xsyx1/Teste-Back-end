<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produtor extends Model
{
    protected $fillable = [
        'pessoa_id'
    ];

    //relacionamento com a tabela de pessoa
    public function pessoa()
	{
		return $this->belongsTo(Pessoa::class, 'pessoa_id');
	}

    public function scopePerson($query)
    {
        return $query->select(
            'produtors.*',
            'pessoas.nome',
            'pessoas.cpf_cnpj'
        )
            ->join('pessoas', 'pessoas.id', '=', 'produtors.pessoa_id');
    }
}
