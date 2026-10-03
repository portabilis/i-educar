<?php

use App\Http\Controllers\ExemptionListController;
use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->doAnoCorrente = LegacyDisciplineExemptionFactory::new()->create([
        'ref_cod_matricula' => LegacyRegistrationFactory::new()->create(['ano' => now()->year]),
    ]);
    $this->doAnoPassado = LegacyDisciplineExemptionFactory::new()->create([
        'ref_cod_matricula' => LegacyRegistrationFactory::new()->create(['ano' => now()->year - 1]),
    ]);
});

// A lista que o controller entrega à view, sem renderizar o layout
$idsNaLista = function (array $query) {
    $request = Request::create('/consulta-dispensas', 'GET', $query);
    $request->setUserResolver(fn () => $this->user);

    return app(ExemptionListController::class)->index($request)
        ->getData()['exemptions']->getCollection()->modelKeys();
};

test('sem ano na URL, lista o ano corrente que o campo mostra', function () use ($idsNaLista) {
    expect($idsNaLista->call($this, []))
        ->toContain($this->doAnoCorrente->getKey())
        ->not->toContain($this->doAnoPassado->getKey());
});

test('com o ano escolhido, lista esse ano', function () use ($idsNaLista) {
    expect($idsNaLista->call($this, ['ano' => now()->year - 1]))
        ->toContain($this->doAnoPassado->getKey())
        ->not->toContain($this->doAnoCorrente->getKey());
});

test('com o ano limpo, lista todos os anos', function () use ($idsNaLista) {
    expect($idsNaLista->call($this, ['ano' => '']))
        ->toContain($this->doAnoCorrente->getKey())
        ->toContain($this->doAnoPassado->getKey());
});

test('com o ano limpo, o link da proxima pagina continua com o ano limpo', function () {
    $request = Request::create('/consulta-dispensas', 'GET', ['ano' => null]);
    $request->setUserResolver(fn () => $this->user);

    $url = app(ExemptionListController::class)->index($request)->getData()['exemptions']->url(2);
    parse_str(parse_url($url, PHP_URL_QUERY), $query);

    expect($query)->toHaveKey('ano', '');
});
