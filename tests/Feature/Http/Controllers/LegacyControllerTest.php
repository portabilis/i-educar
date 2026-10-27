<?php

use App\Http\Controllers\LegacyController;
use App\Models\LegacyRace;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class);

/**
 * O LegacyController monta $_GET e $_POST para o código legado.
 *
 * Num processo que atende mais de uma requisição — a suíte de testes, que roda
 * no SAPI de linha de comando — o que a tela anterior recebeu não pode chegar à
 * seguinte. O teste faz duas requisições no mesmo processo, que é o único jeito
 * de ver o vazamento.
 */
beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('abrir um formulário depois de gravar não grava de novo', function () {
    $this->post('/intranet/educar_raca_cad.php', [
        'tipoacao' => 'Novo',
        'nm_raca' => 'Raça Gama',
    ])->assertRedirect('educar_raca_lst.php');

    // Um GET não tem corpo. Se o $_POST da requisição anterior sobrevivesse, o
    // `tipoacao` continuaria valendo e a tela cadastraria a raça outra vez.
    $this->get('/intranet/educar_raca_cad.php')->assertOk();

    expect(LegacyRace::query()->where('nm_raca', 'Raça Gama')->count())->toBe(1);
});

test('arquivo do legado que não existe responde 404', function () {
    $this->get('/intranet/tela_que_nao_existe.php')->assertNotFound();
    $this->get('/modules/arquivo_que_nao_existe.php')->assertNotFound();
    $this->get('/module/ModuloInexistente/index')->assertNotFound();
});

test('a resposta xml das telas do legado sai com o tipo xml', function () {
    $this->get('/intranet/educar_funcao_xml.php?ins=0')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/xml; charset=utf-8');
});

test('um resolvedor registrado pode trocar o arquivo da tela', function () {
    $resolvers = new ReflectionProperty(LegacyController::class, 'resolvers');
    $before = $resolvers->getValue();

    LegacyController::resolver(fn ($uri) => str_ends_with($uri, 'tela_trocada.php') ? base_path('ieducar/intranet/educar_raca_lst.php') : null);

    try {
        $this->get('/intranet/tela_trocada.php')->assertOk()->assertSee('Raça');
    } finally {
        $resolvers->setValue(null, $before);
    }
});

test('com a exibição de erros ligada não mexe no nível de erros', function () {
    config(['legacy.display_errors' => true]);
    $level = error_reporting(E_ALL);

    try {
        (fn () => $this->configureErrorsAndExceptions())->call(app(LegacyController::class));

        expect(error_reporting())->toBe(E_ALL);
    } finally {
        error_reporting($level);
    }
});
