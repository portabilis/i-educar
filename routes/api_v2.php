<?php

use App\Http\Controllers\FaltasGeralController;
use App\Http\Middleware\ValidToken;

Route::group(['middleware' => ValidToken::class], function () {
    Route::post('/falta-geral', FaltasGeralController::class);
});
