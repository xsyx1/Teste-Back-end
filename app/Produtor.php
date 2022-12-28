<?php

namespace App;

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
}
