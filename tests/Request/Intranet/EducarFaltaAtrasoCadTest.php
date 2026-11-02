<?php

use App\Models\Enums\AbsenceDelayType;
use App\Models\File;
use App\Models\LegacyAbsenceDelay;
use Database\Factories\FileFactory;
use Database\Factories\FileRelationFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;

    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $this->id = $this->ana->employee->getKey();

    $this->list = "educar_falta_atraso_lst.php?ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}";
    $this->new = "/intranet/educar_falta_atraso_cad.php?ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}";
    $this->edit = fn (LegacyAbsenceDelay $absence) => "{$this->new}&ref_cod_escola={$absence->ref_cod_escola}&cod_falta_atraso={$absence->getKey()}";

    $this->payload = fn (array $overrides = []) => LegacyFields::from($this->get($this->new)->assertOk()->getContent(), $overrides + [
        'ref_cod_escola' => (string) $this->school->school->getKey(),
        'tipo' => (string) AbsenceDelayType::DELAY->value,
        'qtd_horas' => '2',
        'qtd_min' => '15',
        'justificada' => '0',
        'data_falta_atraso' => '10/03/2030',
    ]);

    $this->absences = fn () => LegacyAbsenceDelay::query()->where('ref_cod_servidor', $this->id);

    $this->viewer = function () {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

        $this->actingAs(
            LegacyUserFactory::new()->institutional()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
        );
    };
});

afterEach(function () {
    // A gravação de anexos abre transação própria; garante que nada fique aberto para o teste seguinte.
    while (DB::transactionLevel() > 1) {
        DB::rollBack();
    }
});

test('abre o cadastro com as funções do servidor em ordem de matrícula', function () {
    $coordinator = $this->ana->role('Coordenador', '2002', teacher: false);
    $teacher = $this->ana->role('Professor', '1001');

    $html = $this->get($this->new)->assertOk()->assertSee('Salvar')->getContent();
    $fields = LegacyFields::from($html);
    $form = LegacyForm::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['ref_cod_servidor'])->toBe((string) $this->id)
        ->and($form->options('tipo'))->toBe(['' => 'Selecione', '1' => 'Atraso', '2' => 'Falta'])
        ->and($form->options('justificada'))->toBe(['' => 'Selecione', '0' => 'Sim', '1' => 'Não'])
        ->and($form->options('ref_cod_servidor_funcao'))->toBe([
            '' => 'Selecione',
            (string) $teacher->getKey() => 'Professor - 1001',
            (string) $coordinator->getKey() => 'Coordenador - 2002',
        ])
        ->and($html)->toContain($this->list)
        ->not->toContain("id='btn_excluir'");
});

test('cadastra um atraso com horas, minutos, função e anexo', function () {
    $role = $this->ana->role('Professor', '1001');

    $this->post($this->new, ($this->payload)([
        'ref_cod_servidor_funcao' => (string) $role->getKey(),
        'file_url' => json_encode([['url' => 'https://arquivos.test/atestado.pdf', 'size' => 1234, 'originalName' => 'atestado.pdf', 'extension' => 'pdf']]),
    ]))->assertRedirect($this->list);

    $delay = ($this->absences)()->sole();

    expect($delay->getAttributes())->toMatchArray([
        'ref_cod_escola' => $this->school->school->getKey(),
        'ref_ref_cod_instituicao' => $this->institution,
        'ref_usuario_cad' => $this->user->getKey(),
        'ref_usuario_exc' => null,
        'tipo' => 1,
        'data_falta_atraso' => '2030-03-10 00:00:00',
        'qtd_horas' => 2,
        'qtd_min' => 15,
        'justificada' => 0,
        'ref_cod_servidor_funcao' => $role->getKey(),
    ])
        ->and($delay->files()->sole()->only(['url', 'size', 'original_name', 'extension']))->toEqual([
            'url' => 'https://arquivos.test/atestado.pdf',
            'size' => 1234,
            'original_name' => 'atestado.pdf',
            'extension' => 'pdf',
        ]);
});

test('cadastra uma falta sem horas nem minutos, mesmo que venham preenchidos', function () {
    $this->post($this->new, ($this->payload)(['tipo' => (string) AbsenceDelayType::ABSENCE->value, 'justificada' => '1']))
        ->assertRedirect($this->list);

    expect(($this->absences)()->sole()->only(['tipo', 'qtd_horas', 'qtd_min', 'justificada', 'ref_cod_servidor_funcao']))->toBe([
        'tipo' => 2,
        'qtd_horas' => null,
        'qtd_min' => null,
        'justificada' => 1,
        'ref_cod_servidor_funcao' => null,
    ]);
});

test('recusa o cadastro', function (array $overrides, string $message) {
    $this->post($this->new, ($this->payload)($overrides))->assertOk()->assertSee($message);

    expect(($this->absences)()->count())->toBe(0);
})->with([
    'dia que não existe' => [['data_falta_atraso' => '31/02/2030'], 'O dia informado é inválido.'],
    'atraso sem horas' => [['qtd_horas' => ''], 'Preencha os campos de quantidade de horas e minutos.'],
    'atraso sem minutos' => [['qtd_min' => ''], 'Preencha os campos de quantidade de horas e minutos.'],
]);

test('abre a edição com os dados gravados, os anexos e o botão de excluir', function () {
    $delay = $this->ana->absence($this->school->school, ['justificada' => 1]);
    FileRelationFactory::new()->create([
        'relation_type' => LegacyAbsenceDelay::class,
        'relation_id' => $delay->getKey(),
        'file_id' => FileFactory::new()->create(['url' => 'https://arquivos.test/atestado.pdf', 'original_name' => 'atestado.pdf']),
    ]);

    $html = $this->get(($this->edit)($delay))->assertOk()->assertSee('atestado.pdf')->getContent();
    $fields = LegacyFields::from($html);

    expect($fields)->toMatchArray([
        'tipoacao' => 'Editar',
        'cod_falta_atraso' => (string) $delay->getKey(),
        'ref_cod_escola' => (string) $this->school->school->getKey(),
        'tipo' => '1',
        'qtd_horas' => '1',
        'qtd_min' => '30',
        'justificada' => '1',
        'data_falta_atraso' => '10/03/2030',
    ])
        ->and($html)->toContain("id='btn_excluir'");
});

test('ao abrir a edição de uma falta, horas e minutos vêm vazios', function () {
    $absence = $this->ana->absence($this->school->school, ['tipo' => AbsenceDelayType::ABSENCE->value]);

    $fields = LegacyFields::from($this->get(($this->edit)($absence))->assertOk()->getContent());

    expect($fields['qtd_horas'])->toBe('')
        ->and($fields['qtd_min'])->toBe('');
});

test('edita o atraso, acrescenta um anexo e remove outro', function () {
    $delay = $this->ana->absence($this->school->school);
    $old = FileFactory::new()->create(['url' => 'https://arquivos.test/antigo.pdf', 'original_name' => 'antigo.pdf']);
    FileRelationFactory::new()->create(['relation_type' => LegacyAbsenceDelay::class, 'relation_id' => $delay->getKey(), 'file_id' => $old]);

    $payload = LegacyFields::from($this->get(($this->edit)($delay))->assertOk()->getContent(), [
        'qtd_horas' => '3',
        'qtd_min' => '45',
        'justificada' => '1',
        'data_falta_atraso' => '11/03/2030',
        'file_url' => json_encode([['url' => 'https://arquivos.test/novo.pdf', 'size' => 99, 'originalName' => 'novo.pdf', 'extension' => 'pdf']]),
        'file_url_deleted' => (string) $old->getKey(),
    ]);

    $this->post(($this->edit)($delay), $payload)->assertRedirect($this->list);

    expect($delay->refresh()->only(['qtd_horas', 'qtd_min', 'justificada', 'ref_usuario_exc']))->toBe([
        'qtd_horas' => 3,
        'qtd_min' => 45,
        'justificada' => 1,
        'ref_usuario_exc' => $this->user->getKey(),
    ])
        ->and($delay->getRawOriginal('data_falta_atraso'))->toBe('2030-03-11 00:00:00')
        ->and($delay->files()->pluck('original_name')->all())->toBe(['novo.pdf'])
        ->and(File::query()->whereKey($old->getKey())->exists())->toBeFalse();
});

test('ao trocar o atraso por falta, apaga as horas e os minutos', function () {
    $delay = $this->ana->absence($this->school->school);

    $payload = LegacyFields::from($this->get(($this->edit)($delay))->assertOk()->getContent(), ['tipo' => (string) AbsenceDelayType::ABSENCE->value]);

    $this->post(($this->edit)($delay), $payload)->assertRedirect($this->list);

    expect($delay->refresh()->only(['tipo', 'qtd_horas', 'qtd_min']))->toBe(['tipo' => 2, 'qtd_horas' => null, 'qtd_min' => null]);
});

test('recusa a edição de um atraso sem horas', function () {
    $delay = $this->ana->absence($this->school->school);

    $payload = LegacyFields::from($this->get(($this->edit)($delay))->assertOk()->getContent(), ['qtd_horas' => '']);

    $this->post(($this->edit)($delay), $payload)->assertOk()->assertSee('Preencha os campos de quantidade de horas e minutos.');

    expect($delay->refresh()->qtd_horas)->toBe(1);
});

test('exclui a falta ou o atraso', function () {
    $delay = $this->ana->absence($this->school->school);

    $payload = LegacyFields::from($this->get(($this->edit)($delay))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($delay), $payload)->assertRedirect($this->list);

    expect(($this->absences)()->count())->toBe(0);
});

test('avisa quando o registro a excluir já não existe', function () {
    $this->post($this->new, ($this->payload)(['tipoacao' => 'Excluir', 'cod_falta_atraso' => '0']))
        ->assertOk()
        ->assertSee('Exclusão não realizada.');
});

test('quem só pode visualizar volta para a listagem ao abrir, gravar, editar ou excluir', function () {
    $delay = $this->ana->absence($this->school->school);
    $new = ($this->payload)();
    $edit = LegacyFields::from($this->get(($this->edit)($delay))->assertOk()->getContent(), ['qtd_horas' => '9']);

    ($this->viewer)();

    $this->get($this->new)->assertRedirect('educar_falta_atraso_lst.php');
    $this->post($this->new, $new)->assertRedirect($this->list);
    $this->post(($this->edit)($delay), $edit)->assertRedirect($this->list);
    $this->post(($this->edit)($delay), ['tipoacao' => 'Excluir'] + $edit)->assertRedirect($this->list);

    expect(($this->absences)()->sole()->qtd_horas)->toBe(1);
});
