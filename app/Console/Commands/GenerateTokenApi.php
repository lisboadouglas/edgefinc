<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Tymon\JWTAuth\Facades\JWTAuth;

class GenerateTokenApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:token-api ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera um token JWT estático para autenticação na API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $user = new class implements \Tymon\JWTAuth\Contracts\JWTSubject {
                public function getJWTIdentifier()
                {
                    return "STATIC_API_KEY"; // ID do usuário fictício
                }

                public function getJWTCustomClaims(): array
                {
                    return [];
                }
            };

            $token = JWTAuth::fromUser($user);

            $this->info("Token JWT gerado com sucesso.");
            $this->line($token);
            $this->updateEnvFile($token);
            $this->info("\nToken atualizado no .env");
            $this->info("Certifique-se de reiniciar o servidor para aplicar as mudanças.");
        } catch (\Exception $e) {
            $this->error("Erro ao gerar token: " . $e->getMessage());
            return 1;
        }
    }

    protected function updateEnvFile($token)
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        
        $newContent = preg_replace(
            '/API_STATIC_KEY=.*/',
            "API_STATIC_KEY=$token",
            $envContent
        );
        
        if ($newContent === $envContent) {
            $newContent .= "\nAPI_STATIC_KEY=$token";
        }

        file_put_contents($envPath, $newContent);
    }
}
