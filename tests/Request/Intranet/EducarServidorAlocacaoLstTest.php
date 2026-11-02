<?php

use App\Models\LegacyBondType;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);

    $this->keys = "ref_cod_servidor={$this->ana->employee->getKey()}&ref_cod_instituicao={$this->institution}";
    $this->detail = fn ($allocation) => 'educar_servidor_alocacao_det.php?cod_servidor_alocacao=' . $allocation->getKey();
    $this->list = fn (array $query = []) => $this->get("/intranet/educar_servidor_alocacao_lst.php?{$this->keys}&" . http_build_query($query))->assertOk();
});

test('lista as alocações do servidor por ano, com a função, o período, a carga, as datas e o vínculo', function () {
    $role = $this->ana->role('Professor', '1001');
    $bond = LegacyBondType::query()->create(['nm_vinculo' => 'Efetivo', 'abreviatura' => 'EF']);

    $second = $this->ana->allocation($this->school->school, ['ano' => 2031, 'periodo' => 3, 'carga_horaria' => '10:30:00']);
    $first = $this->ana->allocation($this->school->school, [
        'ano' => 2030,
        'periodo' => 2,
        'carga_horaria' => '20:00:00',
        'ref_cod_servidor_funcao' => $role->getKey(),
        'ref_cod_funcionario_vinculo' => $bond->getKey(),
        'data_admissao' => '2030-02-01',
        'data_saida' => '2030-12-15',
    ]);
    $other = EmployeeScenario::named('Bia Borges', $this->institution)->allocation($this->school->school);

    ($this->list)()
        ->assertSee('Ana Alves')
        ->assertSeeInOrder(['Escola Aurora', 'Professor', '2030', 'Vespertino', '20:00', '01/02/2030', '15/12/2030', 'Efetivo', 'Escola Aurora', '2031', 'Noturno', '10:30'])
        ->assertSee(($this->detail)($first), false)
        ->assertSee(($this->detail)($second), false)
        ->assertDontSee(($this->detail)($other), false)
        ->assertSee("educar_servidor_alocacao_cad.php?{$this->keys}", false)
        ->assertSee("educar_servidor_det.php?cod_servidor={$this->ana->employee->getKey()}&ref_cod_instituicao={$this->institution}", false);
});

test('filtra pela escola e pelo ano, e não lista alocação inativa', function () {
    $otherSchool = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->institution]);

    $wanted = $this->ana->allocation($this->school->school, ['ano' => 2030]);
    $otherYear = $this->ana->allocation($this->school->school, ['ano' => 2031]);
    $elsewhere = $this->ana->allocation($otherSchool, ['ano' => 2030]);
    $inactive = $this->ana->allocation($this->school->school, ['ano' => 2030, 'ativo' => 0]);

    ($this->list)(['ref_cod_escola' => $this->school->school->getKey(), 'ano_letivo' => 2030])
        ->assertSee(($this->detail)($wanted), false)
        ->assertDontSee(($this->detail)($otherYear), false)
        ->assertDontSee(($this->detail)($elsewhere), false)
        ->assertDontSee(($this->detail)($inactive), false);
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $number) {
        $this->ana->allocation($this->school->school, ['ano' => 2000 + $number]);
    }

    ($this->list)()->assertSee('>2020<', false)->assertDontSee('>2021<', false);
    ($this->list)(['pagina_formulario' => 2])->assertSee('>2021<', false)->assertDontSee('>2020<', false);
});

test('o usuário de biblioteca só vê as alocações nas suas escolas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $otherSchool = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->institution]);
    $mine = $this->ana->allocation($this->school->school);
    $elsewhere = $this->ana->allocation($otherSchool);

    $this->actingAs(LegacyUserFactory::new()->withSchool($this->school->school)->withAccess(635)->create([
        'ref_cod_instituicao' => $this->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ]));

    ($this->list)()
        ->assertSee(($this->detail)($mine), false)
        ->assertDontSee(($this->detail)($elsewhere), false);
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    ($this->list)()->assertSee('Ana Alves')->assertDontSee('educar_servidor_alocacao_cad.php', false);
});

test('volta para a listagem de servidores quando o servidor não existe na instituição', function (string $query) {
    $this->get('/intranet/educar_servidor_alocacao_lst.php?' . $query)->assertRedirect('educar_servidor_lst.php');
})->with(['sem parâmetros' => [''], 'servidor inexistente' => ['ref_cod_servidor=999999999&ref_cod_instituicao=1']]);
