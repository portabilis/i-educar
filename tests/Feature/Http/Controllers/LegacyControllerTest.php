<?php

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
