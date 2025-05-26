<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Exceptions\GosatApiException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GosatApiService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.gosat.url');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 15,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }
    public function checkCreditOffers(string $cpf): array
    {
        return Cache::remember('credit_offers_{$cpf}', 3600, function () use ($cpf) {
            return $this->makeRequest(
                'POST',
                '/api/v1/simulacao/credito',
                ['cpf' => $cpf]
            );
        });
    }

    public function simulateOffer(array $data): array
    {
        return $this->makeRequest(
            'POST',
            '/api/v1/simulacao/oferta',
            $data
        );
    }

    private function makeRequest(string $method, string $endpoint, array $data): array
    {
        if(Cache::get('gosat_api_outage')){
            throw new GosatApiException('Service in degraded mode', 503);
        }
        $startTime = microtime(true);
        try {
            $response = $this->client->request($method, $endpoint, [
                'json' => $data,
                'http_errors' => false,
            ]);

            Log::info('API Request', [
                'endpoint' => $endpoint,
                'payload' => $data,
                'response' => $response->getBody(),
                'response_time' => microtime(true) - $startTime
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);
            if ($statusCode !== 200) {
                throw new GosatApiException(
                    "API Error: {$body['message']}",
                    $statusCode
                );
            }
            return $body;
        } catch (\Exception $e) {
            Log::error('Falha na comunicação com a API da Gosat', [
                'error' => $e->getMessage(),
                'endpoint' => $endpoint,
                'payload' => $data
            ]);

            Cache::put('gosat_api_outage', true, now()->addMinutes(30));
            throw new GosatApiException(
                "Service unavailable: {$e->getMessage()}",
                503
            );
        }
    }
}
