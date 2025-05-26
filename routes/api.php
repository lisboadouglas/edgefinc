<?php

use App\Http\Controllers\CreditController;
use App\Http\Middleware\VerifyStaticJWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/offers', [CreditController::class, 'checkOffers'])->middleware(VerifyStaticJWT::class);