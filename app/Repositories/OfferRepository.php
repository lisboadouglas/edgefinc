<?php

namespace App\Repositories;

use App\Models\Offer;

class OfferRepository
{
    public function saveResults(string $cpf, array $offers): void
    {
        foreach ($offers as $offer) {
            Offer::updateOrCreate(
                [
                    'cpf' => $cpf,
                    'instituicao_id' => $offer['instituicao_id'],
                    'instituicao_nome' => $offer['instituicao_nome'],
                    'modalidade_cod' => $offer['modalidade_cod'],
                    'modalidade_nome' => $offer['modalidade_nome'],
                ],
                [
                    'valor_min' => $offer['valor_min'],
                    'valor_max' => $offer['valor_max'],
                    'taxa_juros' => $offer['taxa_juros'],
                    'custo_total' => $offer['custo_total']
                ]
            );
        }
    }
}
