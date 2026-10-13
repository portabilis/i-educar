<?php

use App\Process;
use Database\Factories\LegacyDisciplineAbsenceFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineScoreFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('lista os componentes curriculares e filtra pelo nome', function () {
    $discipline = LegacyDisciplineFactory::new()->create(['nome' => 'Componente Alfa']);
    $link = 'ComponenteCurricular/view?id=' . $discipline->getKey();

    $this->get('/intranet/educar_componente_curricular_lst.php?nome=Componente Alfa&abreviatura=' . urlencode($discipline->abreviatura) . '&tipo_base=' . $discipline->tipo_base . '&area_conhecimento_id=' . $discipline->area_conhecimento_id)
        ->assertOk()
        ->assertSee('Componente Alfa');
});

test('mostra a lixeira ativa para componente sem vínculo', function () {
    $discipline = LegacyDisciplineFactory::new()->create(['name' => 'Componente Livre 1177']);

    $this->get('/intranet/educar_componente_curricular_lst.php?' . http_build_query(['nome' => 'Componente Livre 1177']))
        ->assertOk()
        ->assertSee('data-id="' . $discipline->getKey() . '"', false)
        ->assertSee('aria-label="Excluir Componente Livre 1177"', false);
});

test('mostra a lixeira bloqueada para componente vinculado a série', function () {
    $discipline = LegacyDisciplineFactory::new()->create(['name' => 'Componente Vinculado 1177']);
    LegacyDisciplineAcademicYearFactory::new()->create(['componente_curricular_id' => $discipline]);

    $this->get('/intranet/educar_componente_curricular_lst.php?' . http_build_query(['nome' => 'Componente Vinculado 1177']))
        ->assertOk()
        ->assertDontSee('data-id="' . $discipline->getKey() . '"', false)
        ->assertSee('data-toggle="tooltip" title="Este componente está vinculado a uma ou mais séries e não pode ser excluído para preservar o histórico."', false)
        ->assertSee('role="img" tabindex="0"', false);
});

test('mostra a lixeira bloqueada para componente com nota lançada', function () {
    $discipline = LegacyDisciplineFactory::new()->create(['name' => 'Componente Com Nota 1177']);
    LegacyDisciplineScoreFactory::new()->create(['componente_curricular_id' => $discipline]);

    $this->get('/intranet/educar_componente_curricular_lst.php?' . http_build_query(['nome' => 'Componente Com Nota 1177']))
        ->assertOk()
        ->assertDontSee('data-id="' . $discipline->getKey() . '"', false)
        ->assertSee('title="Este componente não pode ser excluído para preservar o histórico, pois possui: notas lançadas."', false);
});

test('lista todos os vínculos no tooltip', function () {
    $discipline = LegacyDisciplineFactory::new()->create(['name' => 'Componente Com Vínculos 1177']);
    LegacyDisciplineAcademicYearFactory::new()->create(['componente_curricular_id' => $discipline]);
    LegacyDisciplineAbsenceFactory::new()->create(['componente_curricular_id' => $discipline]);

    $this->get('/intranet/educar_componente_curricular_lst.php?' . http_build_query(['nome' => 'Componente Com Vínculos 1177']))
        ->assertOk()
        ->assertSee('title="Este componente não pode ser excluído para preservar o histórico, pois possui: vínculo com séries e faltas lançadas."', false)
        ->assertSee('aria-label="Este componente não pode ser excluído para preservar o histórico, pois possui: vínculo com séries e faltas lançadas."', false);
});

test('escapa o nome do componente na ação de excluir', function () {
    LegacyDisciplineFactory::new()->create(['name' => 'Arte "Visual" <b>1177</b>']);

    $this->get('/intranet/educar_componente_curricular_lst.php?' . http_build_query(['nome' => 'Arte']))
        ->assertOk()
        ->assertSee('data-nome="Arte &quot;Visual&quot; &lt;b&gt;1177&lt;/b&gt;"', false)
        ->assertSee('aria-label="Excluir Arte &quot;Visual&quot; &lt;b&gt;1177&lt;/b&gt;"', false);
});

test('mostra a coluna de ações para usuário com permissão de excluir', function () {
    LegacyGeneralConfigurationFactory::new()->create([
        'ref_cod_instituicao' => LegacyInstitutionFactory::new()->current()->getKey(),
        'active_on_ieducar' => 1,
    ]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(Process::DISCIPLINES)->create());

    $discipline = LegacyDisciplineFactory::new()->create(['name' => 'Componente Permitido 1177']);

    $this->get('/intranet/educar_componente_curricular_lst.php?' . http_build_query(['nome' => 'Componente Permitido 1177']))
        ->assertOk()
        ->assertSee('data-id="' . $discipline->getKey() . '"', false);
});

test('esconde a coluna de ações para usuário sem permissão de excluir', function () {
    LegacyGeneralConfigurationFactory::new()->create([
        'ref_cod_instituicao' => LegacyInstitutionFactory::new()->current()->getKey(),
        'active_on_ieducar' => 1,
    ]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(Process::DISCIPLINES, remove: false)->create());

    $discipline = LegacyDisciplineFactory::new()->create(['name' => 'Componente Sem Permissao 1177']);

    $this->get('/intranet/educar_componente_curricular_lst.php?' . http_build_query(['nome' => 'Componente Sem Permissao 1177']))
        ->assertOk()
        ->assertSee('Componente Sem Permissao 1177')
        ->assertDontSee('data-id="' . $discipline->getKey() . '"', false)
        ->assertDontSee('Ações');
});

test('lista vazia não quebra com a coluna de ações', function () {
    $this->get('/intranet/educar_componente_curricular_lst.php?' . http_build_query(['nome' => 'Inexistente 1177']))
        ->assertOk();
});
