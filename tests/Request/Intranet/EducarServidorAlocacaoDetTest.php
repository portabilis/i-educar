<?php

use App\Models\LegacyBondType;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);

    $this->keys = "ref_cod_servidor={$this->ana->employee->getKey()}&ref_cod_instituicao={$this->institution}";
    $this->url = fn ($allocation) => '/intranet/educar_servidor_alocacao_det.php?cod_servidor_alocacao=' . $allocation->getKey();
});

test('mostra os dados da alocação, a função, o vínculo, as datas e os links', function () {
    $allocation = $this->ana->allocation($this->school->school, [
        'ano' => 2030,
        'periodo' => 2,
        'carga_horaria' => '20:30:00',
        'ref_cod_servidor_funcao' => $this->ana->role('Professor', '1001')->getKey(),
        'ref_cod_funcionario_vinculo' => LegacyBondType::query()->create(['nm_vinculo' => 'Efetivo', 'abreviatura' => 'EF'])->getKey(),
        'data_admissao' => '2030-02-01',
        'data_saida' => '2030-12-15',
    ]);

    $this->get(($this->url)($allocation))
        ->assertOk()
        ->assertSeeInOrder([
            'Servidor', 'Ana Alves',
            'Escola', 'Escola Aurora',
            'Ano', '2030',
            'Periodo', 'Vespertino',
            'Carga horária', '20:30',
            'Função', 'Professor',
            'Vinculo', 'Efetivo',
            'Data de admissão', '01/02/2030',
            'Data de saída', '15/12/2030',
        ])
        ->assertSee("educar_servidor_alocacao_cad.php?{$this->keys}", false)
        ->assertSee('educar_servidor_alocacao_cad.php?cod_servidor_alocacao=' . $allocation->getKey(), false)
        ->assertSee("educar_servidor_alocacao_lst.php?{$this->keys}", false);
});

test('omite a função, o vínculo e as datas que a alocação não tem', function () {
    $allocation = $this->ana->allocation($this->school->school, ['periodo' => 1]);

    $this->get(($this->url)($allocation))
        ->assertOk()
        ->assertSeeInOrder(['Periodo', 'Matutino', 'Carga horária', '20:00'])
        ->assertDontSee('Função')
        ->assertDontSee('Vinculo')
        ->assertDontSee('Data de admissão')
        ->assertDontSee('Data de saída');
});

test('não oferece novo nem editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $allocation = $this->ana->allocation($this->school->school);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get(($this->url)($allocation))
        ->assertOk()
        ->assertSee('Ana Alves')
        ->assertDontSee('educar_servidor_alocacao_cad.php', false);
});

test('volta para a listagem de servidores quando a alocação não existe', function (string $code) {
    $this->get('/intranet/educar_servidor_alocacao_det.php?cod_servidor_alocacao=' . $code)->assertRedirect('educar_servidor_lst.php');
})->with(['inexistente' => ['999999999'], 'não numérico' => ['abc']]);
