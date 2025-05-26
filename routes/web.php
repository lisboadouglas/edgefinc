<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    echo "No web routes allowed.";
    exit;
});
