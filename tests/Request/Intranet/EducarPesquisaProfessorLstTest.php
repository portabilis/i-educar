<?php

use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->math = $this->scenario->discipline('Matemática');

    // Só entra na lista quem é professor, está alocado na escola e leciona um componente.
    $this->ana = EmployeeScenario::named('Ana Alves', $this->scenario->institution);
    $this->ana->allocation($this->scenario->school);
    $this->ana->discipline($this->ana->role('Professora', 'ana.alves'), $this->math, $this->scenario->course);

    $this->bia = EmployeeScenario::named('Bia Borges', $this->scenario->institution);
    $this->bia->role('Secretária', 'bia.borges', teacher: false);
    $this->bia->allocation($this->scenario->school);

    $this->search = fn (array $query = []) => $this->get('/intranet/educar_pesquisa_professor_lst.php?' . http_build_query($query + [
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => $this->scenario->school->getKey(),
    ]))->assertOk();
});

test('lista os professores alocados na escola com matrícula e instituição, filtrando pelo nome', function () {
    ($this->search)(['campo1' => 'ref_cod_servidor', 'campo2' => 'nome_servidor'])
        ->assertSee('Ana Alves')
        ->assertSee($this->user->institution->nm_instituicao)
        ->assertDontSee('Bia Borges')
        ->assertSee("addVal1('ref_cod_servidor','{$this->ana->employee->getKey()}','Ana Alves'); addVal1('nome_servidor'", false);

    ($this->search)(['nome_servidor' => 'bia'])->assertDontSee('Ana Alves');
});

test('monta o retorno para o campo que a tela de origem pediu, com ou sem tipo', function () {
    $id = $this->ana->employee->getKey();

    ($this->search)(['campo1' => 'ref_cod_servidor', 'campo2' => 'nome_servidor', 'tipo' => 1])
        ->assertSee("addVal1('ref_cod_servidor','{$id}', 'Ana Alves'); addVal1('nome_servidor','Ana Alves', '{$id}');", false);

    $this->flushSession();
    ($this->search)(['campo1' => 'ref_cod_servidor', 'tipo' => 1])
        ->assertSee("addVal1('ref_cod_servidor','{$id}','Ana Alves');  fecha();", false);

    $this->flushSession();
    ($this->search)(['campo2' => 'nome_servidor'])
        ->assertSee("addVal1('nome_servidor','{$id}','Ana Alves');  fecha();", false);

    $this->flushSession();
    ($this->search)(['campo1' => 'ref_cod_servidor'])
        ->assertSee("addVal1('ref_cod_servidor','{$id}','Ana Alves');  fecha();", false);
});

test('sem escola e instituição não lista ninguém', function () {
    $this->get('/intranet/educar_pesquisa_professor_lst.php')->assertOk()->assertDontSee('Ana Alves');
});
