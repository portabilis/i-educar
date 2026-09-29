<?php

use App\Http\Controllers\FaltasComponenteController;
use App\Http\Controllers\FaltasGeralController;
use App\Http\Controllers\LaudoUploadController;
use App\Http\Controllers\NotasController;
use App\Http\Controllers\PareceresPorEtapaComponenteController;
use App\Http\Controllers\PareceresPorEtapaGeralController;
use App\Http\Controllers\RecuperacoesController;
use App\Http\Middleware\ValidToken;

Route::group(['middleware' => ValidToken::class], function () {
    Route::post('/falta-geral', FaltasGeralController::class);
    Route::post('/falta-componente', FaltasComponenteController::class);
    Route::post('/aluno-laudo-upload', LaudoUploadController::class);
    Route::post('/notas', NotasController::class);
    Route::post('/recuperacoes', RecuperacoesController::class);
    Route::post('/pareceres-por-etapa-e-componente', PareceresPorEtapaComponenteController::class);
    Route::post('/pareceres-por-etapa-geral', PareceresPorEtapaGeralController::class);
});
