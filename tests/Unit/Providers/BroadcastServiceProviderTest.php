<?php

use App\Providers\BroadcastServiceProvider;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

uses(TestCase::class);

test('registra a rota de autenticação dos canais de broadcast', function () {
    (new BroadcastServiceProvider(app()))->boot();

    $uris = collect(Route::getRoutes()->getRoutes())->map->uri();

    expect($uris)->toContain('broadcasting/auth');
});
