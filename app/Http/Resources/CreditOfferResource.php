<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'instituicaoFinanceira' => $this->resource['instituicao_nome'],
            'modalidadeCredito' => $this->resource['modalidade_nome'],
            'valorAPagar' => $this->resource['custo_total'],
            'valorSolicitado' => $this->resource['valor_medio'],
            'taxaJuros' => $this->resource['taxa_juros'],
            'qntParcelas' => $this->resource['quantidade_parcelas']
        ];
    }
}
