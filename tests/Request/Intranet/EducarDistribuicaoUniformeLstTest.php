<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\UniformDistributionFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');

    $this->distribution = fn (array $attributes = []) => UniformDistributionFactory::new()->create($attributes + [
        'student_id' => $this->student,
        'school_id' => $this->scenario->school,
        'year' => 2030,
        'type' => 'Entregue',
        'distribution_date' => '15/03/2030',
        'complete_kit' => true,
    ]);

    $this->detail = fn ($distribution) => "educar_distribuicao_uniforme_det.php?ref_cod_aluno={$this->student->getKey()}&cod_distribuicao_uniforme={$distribution->getKey()}";
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_distribuicao_uniforme_lst.php?' . http_build_query($query + ['ref_cod_aluno' => $this->student->getKey()]))->assertOk();
});

test('lista as distribuições do aluno em ordem de ano, com o kit, o tipo, a data e os botões', function () {
    $second = ($this->distribution)(['year' => 2031, 'type' => 'Solicitado', 'distribution_date' => null, 'complete_kit' => false]);
    $first = ($this->distribution)();
    $other = ($this->distribution)(['student_id' => $this->scenario->student('Bia Borges')]);

    ($this->list)()
        ->assertSee('ANA ALVES')
        ->assertSeeInOrder(['2030', 'SIM', 'Entregue', '15/03/2030', '2031', 'NÃO', 'Solicitado'])
        ->assertSee(($this->detail)($first), false)
        ->assertSee(($this->detail)($second), false)
        ->assertDontSee('cod_distribuicao_uniforme=' . $other->getKey(), false)
        ->assertSee('go("educar_distribuicao_uniforme_cad.php?ref_cod_aluno=' . $this->student->getKey() . '")', false)
        ->assertSee('educar_aluno_det.php?cod_aluno=' . $this->student->getKey(), false);
});

test('filtra pelo ano', function () {
    $first = ($this->distribution)();
    $second = ($this->distribution)(['year' => 2031]);

    ($this->list)(['ano' => 2031])
        ->assertSee(($this->detail)($second), false)
        ->assertDontSee(($this->detail)($first), false);
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $distribution = ($this->distribution)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    ($this->list)()
        ->assertSee(($this->detail)($distribution), false)
        ->assertDontSee('educar_distribuicao_uniforme_cad.php', false);
});

test('volta para a listagem de alunos quando o aluno não é informado', function () {
    $this->get('/intranet/educar_distribuicao_uniforme_lst.php')->assertRedirect('educar_aluno_lst.php');
});
