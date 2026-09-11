<?php

use App\Http\Controllers\FaltasGeralController;
use App\Http\Controllers\LaudoUploadController;
use App\Http\Middleware\ValidToken;

Route::group(['middleware' => ValidToken::class], function () {
    Route::post('/falta-geral', FaltasGeralController::class);
    Route::post('/aluno-laudo-upload', LaudoUploadController::class);
});
