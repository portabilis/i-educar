<?php

use App\Models\Enums\AbsenceDelayType;
use App\Models\LegacyAbsenceDelay;
use App\Models\LegacyInstitution;
use Database\Factories\FileFactory;
use Database\Factories\FileRelationFactory;
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

    $this->keys = "ref_cod_servidor={$this->id}&ref_cod_escola={$this->school->school->getKey()}&ref_cod_instituicao={$this->institution}";
    $this->url = fn ($absence) => "/intranet/educar_falta_atraso_det.php?{$this->keys}&cod_falta_atraso={$absence->getKey()}";
});

test('mostra o atraso com as horas, a escola, a instituição, os anexos e os botões', function () {
    $delay = $this->ana->absence($this->school->school, ['qtd_horas' => 2, 'qtd_min' => 30]);
    FileRelationFactory::new()->create([
        'relation_type' => LegacyAbsenceDelay::class,
        'relation_id' => $delay->getKey(),
        'file_id' => FileFactory::new()->create(['url' => 'https://arquivos.test/atestado.pdf', 'original_name' => 'atestado.pdf']),
    ]);

    $this->get(($this->url)($delay))
        ->assertOk()
        ->assertSeeInOrder(['Faltas/Atrasos', '10/03/2030', 'Atraso', '2', '30', 'Escola Aurora', 'Rede Aurora', 'atestado.pdf'])
        ->assertSee('<a href="https://arquivos.test/atestado.pdf" target="_blank">atestado.pdf</a>', false)
        ->assertSee('Compensar')
        ->assertSee("educar_falta_atraso_compensado_cad.php?{$this->keys}", false)
        ->assertSee("educar_falta_atraso_cad.php?{$this->keys}&cod_falta_atraso={$delay->getKey()}", false)
        ->assertSee("educar_falta_atraso_lst.php?ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}", false);
});

test('na falta, as horas e os minutos viram traço', function () {
    $absence = $this->ana->absence($this->school->school, ['tipo' => AbsenceDelayType::ABSENCE->value]);

    $this->get(($this->url)($absence))
        ->assertOk()
        ->assertSeeInOrder(['10/03/2030', 'Falta', '>-<', '>-<', 'Escola Aurora'], false);
});

test('defeito: a matrícula da função nunca aparece', function () {
    // A tela lê `matricula` do registro da falta, que não tem essa coluna.
    $absence = $this->ana->absence($this->school->school, ['ref_cod_servidor_funcao' => $this->ana->role('Professor', 'MAT-1001')->getKey()]);

    $this->get(($this->url)($absence))->assertOk()->assertDontSee('MAT-1001');
});

test('não oferece compensar nem editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $absence = $this->ana->absence($this->school->school);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get(($this->url)($absence))
        ->assertOk()
        ->assertSee('10/03/2030')
        ->assertDontSee('educar_falta_atraso_compensado_cad.php', false)
        ->assertDontSee('educar_falta_atraso_cad.php', false);
});

test('volta para a listagem do servidor quando o registro não existe', function () {
    $this->get("/intranet/educar_falta_atraso_det.php?{$this->keys}&cod_falta_atraso=0")
        ->assertRedirect("educar_falta_atraso_lst.php?ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}");
});
