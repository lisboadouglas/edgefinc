<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckCreditOffersRequest;
use App\Http\Resources\CreditOfferResource;
use App\Services\CreditService;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function __construct(private CreditService $creditService){}
    
    public function checkOffers(CheckCreditOffersRequest $request)
    {
        try {
            $offers = $this->creditService->processCreditOffers(
                $request->validated()['cpf']
            );

            return CreditOfferResource::collection($offers);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Falha ao processar ofertas de crédito',
                'message' => $e->getMessage()
            ],500);
        }
    }
}
