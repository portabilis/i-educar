<?php

use App\Models\LogUnification;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Modules\Unification\PersonLogUnification;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->url = '/intranet/educar_unifica_pessoa.php';
    $this->main = LegacyIndividualFactory::new()->create();
    $this->duplicate = LegacyIndividualFactory::new()->create();

    $this->people = fn (array ...$people) => ['tipoacao' => 'Novo', 'pessoas' => json_encode($people)];
    $this->logs = fn () => LogUnification::query()->where('type', PersonLogUnification::getType())->where('main_id', $this->main->getKey());
});

test('abre a tela com a tabela de pessoas e o envio por script', function () {
    $html = $this->get($this->url)
        ->assertOk()
        ->assertSee('Pessoa física a ser unificada')
        ->assertSee('carregaDadosPessoas()', false)
        ->getContent();

    expect(LegacyFields::from($html)['tipoacao'])->toBe('Novo');
});

test('unifica a pessoa duplicada na principal e registra o log', function () {
    $name = $this->duplicate->person->nome;

    $this->post($this->url, ($this->people)(
        ['idpes' => $this->main->getKey(), 'pessoa_principal' => true],
        ['idpes' => $this->duplicate->getKey(), 'pessoa_principal' => false],
    ))->assertOk()->assertSee('Pessoas unificadas com sucesso.');

    expect(($this->logs)()->sole())
        ->duplicates_id->toBe([$this->duplicate->getKey()])
        ->duplicates_name->toBe(json_encode([$name]))
        ->created_by->toBe($this->user->getKey())
        ->updated_by->toBe($this->user->getKey());

    $this->assertModelMissing($this->duplicate);
    $this->assertModelExists($this->main);
});

test('recusa dados incoerentes sem unificar', function (Closure $payload, string $message) {
    $this->post($this->url, ['tipoacao' => 'Novo', 'pessoas' => $payload->call($this)])
        ->assertOk()
        ->assertSee($message);

    expect(($this->logs)()->exists())->toBeFalse();

    $this->assertModelExists($this->duplicate);
})->with([
    'texto que não é JSON' => [fn () => '{pessoas', 'Informações inválidas para unificação'],
    'lista no lugar do texto' => [fn () => [['idpes' => $this->main->getKey()]], 'Informações inválidas para unificação'],
    'uma pessoa só' => [fn () => json_encode([['idpes' => $this->main->getKey(), 'pessoa_principal' => true]]), 'Informe no mínimo duas pessoas para unificação.'],
    'sem o código' => [fn () => json_encode([['pessoa_principal' => true], ['idpes' => $this->duplicate->getKey(), 'pessoa_principal' => false]]), 'Dados enviados inválidos, recarregue a tela e tente novamente!'],
    'sem a marca de principal' => [fn () => json_encode([['idpes' => $this->main->getKey()], ['idpes' => $this->duplicate->getKey(), 'pessoa_principal' => false]]), 'Dados enviados inválidos, recarregue a tela e tente novamente!'],
    'nenhuma principal' => [fn () => json_encode([['idpes' => $this->main->getKey(), 'pessoa_principal' => false], ['idpes' => $this->duplicate->getKey(), 'pessoa_principal' => false]]), 'Pessoa principal não informada'],
    'duas principais' => [fn () => json_encode([['idpes' => $this->main->getKey(), 'pessoa_principal' => true], ['idpes' => $this->duplicate->getKey(), 'pessoa_principal' => true]]), 'Não pode haver mais de uma pessoa principal'],
    'a mesma pessoa duas vezes' => [fn () => json_encode([['idpes' => $this->main->getKey(), 'pessoa_principal' => true], ['idpes' => $this->main->getKey(), 'pessoa_principal' => false]]), 'Erro ao tentar unificar Pessoas, foi inserido cadastro duplicados'],
]);

test('volta para o início sem a lista de pessoas', function () {
    $this->post($this->url, ['tipoacao' => 'Novo'])->assertRedirect('index.php');
});

test('volta para o início quando o usuário não pode unificar pessoas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(9998878, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->url)->assertRedirect('index.php');

    $this->post($this->url, ($this->people)(
        ['idpes' => $this->main->getKey(), 'pessoa_principal' => true],
        ['idpes' => $this->duplicate->getKey(), 'pessoa_principal' => false],
    ))->assertRedirect('index.php');

    $this->assertModelExists($this->duplicate);
});
