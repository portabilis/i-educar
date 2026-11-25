<?php

use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->rule = LegacyEvaluationRuleFactory::new()->create(['nome' => 'Regra Alfa']);
});

test('abre o formulário de uma nova regra de avaliação', function () {
    $this->get('/module/RegraAvaliacao/edit')->assertOk();
});

test('abre o formulário de edição da regra de avaliação', function () {
    $payload = LegacyFields::from($this->get('/module/RegraAvaliacao/edit?id=' . $this->rule->getKey())->assertOk()->getContent());

    expect($payload['nome'])->toBe('Regra Alfa');
});

test('recusa salvar regra de avaliação com quantidade de casas decimais fora do intervalo', function (string $decimalPlaces) {
    $original = $this->rule->fresh()->qtd_casas_decimais;
    $html = $this->get('/module/RegraAvaliacao/edit?id=' . $this->rule->getKey())->assertOk()->getContent();
    $payload = LegacyFields::from($html, [
        'qtdCasasDecimais' => $decimalPlaces,
        'pontos' => '10',
    ]);

    $this->post('/module/RegraAvaliacao/edit?id=' . $this->rule->getKey(), $payload)
        ->assertOk()
        ->assertSee('A quantidade máxima de casas decimais deve estar entre 0 e 4.');

    expect($this->rule->fresh()->qtd_casas_decimais)->toBe($original);
})->with([
    'negativo' => '-1',
    'acima do limite' => '5',
    'valor extremo' => '999',
    'fracionário' => '2.5',
]);

test('salva regra de avaliação com quantidade de casas decimais válida', function (int $decimalPlaces) {
    $html = $this->get('/module/RegraAvaliacao/edit?id=' . $this->rule->getKey())->assertOk()->getContent();
    $payload = LegacyFields::from($html, [
        'qtdCasasDecimais' => (string) $decimalPlaces,
        'pontos' => '10',
    ]);

    $this->post('/module/RegraAvaliacao/edit?id=' . $this->rule->getKey(), $payload)
        ->assertRedirect('/module/RegraAvaliacao/view?id=' . $this->rule->getKey());

    expect((int) $this->rule->fresh()->qtd_casas_decimais)->toBe($decimalPlaces);
})->with([0, 2, 4]);
