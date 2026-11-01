<?php

use App\Models\Enums\AbsenceDelayType;
use App\Models\LegacyInstitution;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    LegacyInstitution::query()->whereKey($this->institution)->update(['nm_instituicao' => 'Rede Aurora']);

    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $this->id = $this->ana->employee->getKey();

    $this->keys = "ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}";
    $this->detail = fn ($absence) => "educar_falta_atraso_det.php?cod_falta_atraso={$absence->getKey()}&ref_cod_servidor={$this->id}&ref_cod_escola={$absence->ref_cod_escola}&ref_cod_instituicao={$this->institution}";
    $this->list = fn (array $query = []) => $this->get("/intranet/educar_falta_atraso_lst.php?{$this->keys}&" . http_build_query($query))->assertOk();
});

test('lista as faltas e os atrasos do servidor, do mais recente para o mais antigo, com a matrícula da função', function () {
    $role = $this->ana->role('Professor', '1001');

    $absence = $this->ana->absence($this->school->school, ['tipo' => AbsenceDelayType::ABSENCE->value, 'data_falta_atraso' => '2030-03-05']);
    $delay = $this->ana->absence($this->school->school, ['data_falta_atraso' => '2030-03-10', 'qtd_horas' => 2, 'qtd_min' => 30, 'ref_cod_servidor_funcao' => $role->getKey()]);
    $other = EmployeeScenario::named('Bia Borges', $this->institution)->absence($this->school->school);

    ($this->list)()
        ->assertSee('Ana Alves')
        ->assertSeeInOrder([
            'Escola Aurora', 'Rede Aurora', '1001', 'Atraso', '10/03/2030', '(2)', '(30)',
            'Escola Aurora', 'Rede Aurora', 'Falta', '05/03/2030', '-', '-',
        ])
        ->assertSee(($this->detail)($delay), false)
        ->assertSee(($this->detail)($absence), false)
        ->assertDontSee('cod_falta_atraso=' . $other->getKey() . '&', false)
        ->assertSee("educar_falta_atraso_cad.php?{$this->keys}", false)
        ->assertSee("educar_servidor_det.php?cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}", false);
});

test('desconta do atraso as horas que o servidor compensou na escola', function (int $hours, int $minutes, array $shown) {
    // Em 12/03/2030, uma terça, o servidor trabalha das 8h às 11h; a compensação desse dia vale 3 horas.
    $this->ana->allocation($this->school->school, ['ano' => 2030, 'dia_semana' => 2, 'hora_inicial' => '08:00', 'hora_final' => '11:00']);
    $this->ana->compensation($this->school->school, '2030-03-12', '2030-03-12');
    $this->ana->absence($this->school->school, ['qtd_horas' => $hours, 'qtd_min' => $minutes]);

    ($this->list)()->assertSeeInOrder(array_merge(['Atraso', '10/03/2030'], $shown), false);
})->with([
    'sobra de horas' => [1, 0, ['>2<', '>0<']],
    'sobra com empréstimo de minutos' => [1, 30, ['>1<', '>30<']],
    'falta de horas' => [5, 0, ['>(2)<', '>0<']],
    'falta de horas e minutos' => [3, 30, ['>0<', '>(30)<']],
]);

test('filtra pela escola', function () {
    $other = EnrollmentScenario::for($this->user, 2030);

    $here = $this->ana->absence($this->school->school);
    $there = $this->ana->absence($other->school);

    ($this->list)(['ref_cod_escola' => $this->school->school->getKey()])
        ->assertSee(($this->detail)($here), false)
        ->assertDontSee('cod_falta_atraso=' . $there->getKey() . '&', false);
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $day) {
        $this->ana->absence($this->school->school, ['data_falta_atraso' => sprintf('2030-03-%02d', $day)]);
    }

    ($this->list)()->assertSee('02/03/2030')->assertDontSee('01/03/2030');
    ($this->list)(['pagina_formulario' => 2])->assertSee('01/03/2030')->assertDontSee('02/03/2030');
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    ($this->list)()->assertSee('Ana Alves')->assertDontSee('educar_falta_atraso_cad.php', false);
});

test('defeito: quando faltam horas e sobram minutos, o saldo negativo sai maior do que é', function () {
    // Compensou 3h30 e atrasou 5h: deve 1h30, mas a tela soma em vez de abater e mostra (3) horas e (30) minutos.
    $this->ana->allocation($this->school->school, ['ano' => 2030, 'dia_semana' => 2, 'hora_inicial' => '08:00', 'hora_final' => '11:30']);
    $this->ana->compensation($this->school->school, '2030-03-12', '2030-03-12');
    $this->ana->absence($this->school->school, ['qtd_horas' => 5, 'qtd_min' => 0]);

    ($this->list)()->assertSeeInOrder(['Atraso', '10/03/2030', '>(3)<', '>(30)<'], false);
});
