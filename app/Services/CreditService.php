<?php

namespace App\Services;

use App\Repositories\OfferRepository;
use App\Services\GosatApiService;
use Illuminate\Support\Facades\Log;

class CreditService
{
    public function __construct(
        private GosatApiService $apiService,
        private OfferRepository $offerRepository
    ) {}

    public function processCreditOffers(string $cpf): array
    {
        try {
            $cpf = preg_replace('/\D/', '', $cpf);
            $rawOffers = $this->apiService->checkCreditOffers($cpf);
            $simulatedOffers = $this->simulateAllOffers($cpf, $rawOffers);

            $bestOffers = $this->selectBestsOffers($simulatedOffers);
            $this->offerRepository->saveResults($cpf, $bestOffers);
            return $bestOffers;
        } catch (\Exception $e) {
            Log::error("Falha ao processar ofertas para {$cpf}: " . $e->getMessage());
            throw $e;
        }
    }

    private function simulateAllOffers(string $cpf, array $offers): array
    {
        return collect($offers['instituicoes'])
            ->flatMap(function ($institution) use ($cpf) {
                $institutionId = $institution['id'];
                $institutionName = $institution['nome'];

                return collect($institution['modalidades'])
                    ->map(function ($modality) use ($cpf, $institutionId, $institutionName) {
                        $simulation = $this->apiService->simulateOffer([
                            'cpf' => $cpf,
                            'instituicao_id' => $institutionId,
                            'codModalidade' => $modality['cod'],
                        ]);

                        return array_merge($simulation, [
                            'cpf' => $cpf,
                            'instituicao_id' => $institutionId,
                            'instituicao_nome' => $institutionName,
                            'modalidade_cod' => $modality['cod'],
                            'modalidade_nome' => $modality['nome'],
                        ]);
                    });
            })->toArray();
    }

    private function selectBestOffers(array $offers): array
    {
        return collect($offers)
            ->map(function ($offer) {
                $medianValue = ($offer['valorMin'] + $offer['valorMax']) / 2;
                $installments = $offer['QntParcelaMin'];
                return [
                    'cpf' => $offer['cpf'],
                    'instituicao_id' => $offer['instituicao_id'],
                    'instituicao_nome' => $offer['instituicao_nome'],
                    'modalidade_cod' => $offer['modalidade_cod'],
                    'modalidade_nome' => $offer['modalidade_nome'],
                    'custo_total' => $this->calculateTotalCost(
                        $offer['valorMin'],
                        $offer['valorMax'],
                        $offer['jurosMes'],
                        $installments
                    ),
                    'valor_min' => $offer['valorMin'],
                    'valor_max' => $offer['valorMax'],
                    'valor_medio' => $medianValue,
                    'taxa_juros' => $offer['jurosMes'],
                    'score' => $this->calculateAdvantageScore(
                        $offer['jurosMes'],
                        $medianValue,
                        $installments
                    ),
                    'quantidade_parcelas' => $installments,
                ];
            })
            ->sortBy([
                fn($a, $b) => $a['taxa_juros'] <=> $b['taxa_juros'],
                fn($a, $b) => $a['valor_medio'] <=> $b['valor_medio'],
                fn($a, $b) => $a['qntParcelas'] <=> $b['qntParcelas'],
            ])
            ->take(3)
            ->values()
            ->toArray();
    }

    private function selectBestsOffers(array $offers): array
    {
        return collect($offers)
            ->flatMap(function ($offer){
                return $this->gnrtOffers($offer);
            })
            ->sortBy([
                ['custo_total', 'asc'],
                ['taxa_juros', 'asc'],
                ['quantidade_parcelas', 'asc']
            ])
            ->unique(function($item){
                return $item['instituicao_id'].$item['modalidade_cod'];
            })
            ->take(3)
            ->map(function ($item){
                return [
                    'cpf' => $item['cpf'],
                    'instituicao_id' => $item['instituicao_id'],
                    'instituicao_nome' => $item['instituicao_nome'],
                    'modalidade_cod' => $item['modalidade_cod'],
                    'modalidade_nome' => $item['modalidade_nome'],
                    'valor_medio' => $item['valor_solicitado'],
                    'custo_total' => $item['custo_total'],
                    'valor_min' => $item['valor_min'],
                    'valor_max' => $item['valor_max'],
                    'taxa_juros' => $item['taxa_juros'],
                    'quantidade_parcelas' => $item['quantidade_parcelas']
                ];
            })
            ->values()
            ->toArray();
        
    }

    private function gnrtOffers(array $offer): array
    {
        $scenarios = [];

        $values = [
            'min' => $offer['valorMin'],
            'max' => $offer['valorMax']
        ];

        $installmentsOptions = [
            'min' => $offer['QntParcelaMin'],
            'max' => $offer['QntParcelaMax']
        ];

        foreach ($values as $valueType => $valueSelect) {
            foreach ($installmentsOptions as $installmentType => $parcs) {
                $scenarios[] = [
                    'cpf' => $offer['cpf'],
                    'instituicao_id' => $offer['instituicao_id'],
                    'instituicao_nome' => $offer['instituicao_nome'],
                    'modalidade_cod' => $offer['modalidade_cod'],
                    'modalidade_nome' => $offer['modalidade_nome'],
                    'custo_total' => $this->calculateValueSelect(
                        $valueSelect,
                        $offer['jurosMes'],
                        $parcs
                    ),
                    'valor_solicitado' => $valueSelect,
                    'taxa_juros' => $offer['jurosMes'],
                    'valor_min' => $offer['valorMin'],
                    'valor_max' => $offer['valorMax'],
                    'quantidade_parcelas' => $parcs
                ];
            }
        }
        return $scenarios;
    }

    private function calculateValueSelect($valueSelect, $tax, $parcs): float
    {
        return round($valueSelect * pow(1+$tax, $parcs), 2);
    }

    private function calculateTotalCost($min, $max, $rate, $installments): float
    {
        $median = ($min + $max) / 2;
        return round($median * pow(1 + $rate, $installments), 2);
    }

    private function calculateAdvantageScore($interestRate, $value, $installments): float
    {
        return ($value * $interestRate) / $installments;
    }
}
