<?php

namespace App\Providers;


use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticable;
use Illuminate\Contracts\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\Providers\Auth;

class StaticKeyProvider{

    public function retrieveById($identifier)
{
    return new class(config('services.static_jwt.key')) implements JWTSubject {
        private $token;

        public function __construct($token) {
            $this->token = $token;
        }

        public function getJWTIdentifier() {
            return $this->token;
        }

        public function getJWTCustomClaims() {
            return [];
        }
    };
}
}