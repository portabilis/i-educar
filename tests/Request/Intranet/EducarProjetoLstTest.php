<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyProjectFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->project = fn (string $name, string $note = 'Sem observação') => LegacyProjectFactory::new()->create(['nome' => $name, 'observacao' => $note]);
    $this->detail = fn ($project) => 'educar_projeto_det.php?cod_projeto=' . $project->getKey();
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_projeto_lst.php?' . http_build_query($query))->assertOk();
});

test('lista os projetos em ordem de nome, com a observação, o link do detalhe e o botão Novo', function () {
    $beta = ($this->project)('Registro Beta', 'Contraturno de música');
    $alfa = ($this->project)('Registro Alfa', 'Contraturno de esporte');

    ($this->list)(['nome' => 'Registro'])
        ->assertSeeInOrder(['Registro Alfa', 'Contraturno de esporte', 'Registro Beta', 'Contraturno de música'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee('go("educar_projeto_cad.php")', false);
});

test('filtra pelo nome, por trecho e sem diferenciar maiúsculas', function () {
    $alfa = ($this->project)('Registro Alfa');
    $beta = ($this->project)('Registro Beta');

    ($this->list)(['nome' => 'tro alf'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $number) {
        ($this->project)(sprintf('Registro %02d', $number));
    }

    ($this->list)(['nome' => 'Registro'])->assertSee('Registro 20')->assertDontSee('Registro 21');
    ($this->list)(['nome' => 'Registro', 'pagina_' => 2])->assertSee('Registro 21')->assertDontSee('Registro 20');
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $project = ($this->project)('Registro Alfa');

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(21250, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    ($this->list)(['nome' => 'Registro'])
        ->assertSee(($this->detail)($project), false)
        ->assertDontSee('go("educar_projeto_cad.php")', false);
});
