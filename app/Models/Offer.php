<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 'offers';
    protected $fillable = [
        'cpf',
        'instituicao_id',
        'instituicao_nome',
        'modalidade_cod',
        'modalidade_nome',
        'valor_min',
        'valor_medio',
        'valor_max',
        'taxa_juros',
        'custo_total',
        'quantidade_parcelas',
    ];
    protected $casts = [
        'valor_min' => 'decimal:2',
        'valor_max' => 'decimal:2',
        'valor_medio' => 'decimal:2',
        'taxa_juros' => 'decimal:2',
        'custo_total' => 'decimal:2',
    ];
}
