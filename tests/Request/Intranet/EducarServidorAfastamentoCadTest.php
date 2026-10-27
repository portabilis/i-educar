<?php

use App\Models\EmployeeWithdrawal;
use App\Support\View\Employee\EmployeeReturn;
use Database\Factories\FileFactory;
use Database\Factories\FileRelationFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\WithdrawalReasonFactory;
use Illuminate\Support\Facades\Storage;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, (int) date('Y'));
    $this->institution = $this->school->institution;
    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $this->id = $this->ana->employee->getKey();
    $this->reason = WithdrawalReasonFactory::new()->create(['nm_motivo' => 'Licença médica', 'ref_cod_instituicao' => $this->institution]);

    $this->keys = "ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}";
    $this->new = "/intranet/educar_servidor_afastamento_cad.php?{$this->keys}";
    $this->edit = fn (EmployeeWithdrawal $withdrawal, bool $return = false) => "{$this->new}&sequencial={$withdrawal->sequencial}" . ($return ? '&retornar_servidor=' . EmployeeReturn::SIM : '');
    $this->detail = "educar_servidor_det.php?cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}";

    $this->out = now()->subDays(20);
    $this->back = now()->subDays(5);

    $this->payload = fn (array $overrides = []) => $overrides + [
        'tipoacao' => 'Novo',
        'ref_cod_servidor' => (string) $this->id,
        'ref_cod_instituicao' => (string) $this->institution,
        'ref_cod_motivo_afastamento' => (string) $this->reason->getKey(),
        'data_saida' => $this->out->format('d/m/Y'),
    ];

    $this->withdrawals = fn () => EmployeeWithdrawal::query()->where('ref_cod_servidor', $this->id);

    $this->viewer = function (bool $modify, bool $remove) {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

        $this->actingAs(
            LegacyUserFactory::new()->institutional()->withAccess(635, modify: $modify, remove: $remove)->create(['ref_cod_instituicao' => $this->institution])
        );
    };
});

test('abre o afastamento com os motivos em ordem de nome e sem a data de retorno', function () {
    WithdrawalReasonFactory::new()->create(['nm_motivo' => 'Curso de formação', 'ref_cod_instituicao' => $this->institution]);

    $html = $this->get($this->new)->assertOk()->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['ref_cod_servidor'])->toBe((string) $this->id)
        ->and($fields)->toHaveKey('data_saida')
        ->not->toHaveKey('data_retorno')
        ->and(array_values(array_intersect(LegacyForm::from($html)->options('ref_cod_motivo_afastamento'), ['Licença médica', 'Curso de formação'])))->toBe(['Curso de formação', 'Licença médica']);
});

test('afasta o servidor', function () {
    $this->post($this->new, ($this->payload)())->assertRedirect($this->detail);

    $withdrawal = ($this->withdrawals)()->sole();

    expect($withdrawal)
        ->ref_ref_cod_instituicao->toBe($this->institution)
        ->ref_cod_motivo_afastamento->toBe($this->reason->getKey())
        ->ref_usuario_cad->toBe($this->user->getKey())
        ->sequencial->toBe(1)
        ->data_retorno->toBeNull()
        ->and($withdrawal->data_saida->format('Y-m-d'))->toBe($this->out->format('Y-m-d'));
});

test('numera os afastamentos do servidor em sequência e aceita a data de retorno já no cadastro', function () {
    $this->ana->withdrawal($this->reason, ['data_saida' => now()->subMonths(6), 'data_retorno' => now()->subMonths(5)]);

    $this->post($this->new, ($this->payload)(['data_retorno' => $this->back->format('d/m/Y')]))->assertRedirect($this->detail);

    $withdrawal = ($this->withdrawals)()->orderByDesc('sequencial')->first();

    expect($withdrawal->sequencial)->toBe(2)
        ->and($withdrawal->data_retorno->format('Y-m-d'))->toBe($this->back->format('Y-m-d'));
});

test('recusa a data de afastamento vazia, que não é data ou de mais de um ano atrás', function (Closure $date) {
    $this->post($this->new, ($this->payload)(['data_saida' => $date->call($this)]))
        ->assertOk()
        ->assertSee('Data de Afastamento Inválida.');

    expect(($this->withdrawals)()->exists())->toBeFalse();
})->with([
    'vazia' => [fn () => ''],
    'que não é data' => [fn () => 'abc'],
    'há mais de um ano' => [fn () => now()->subYear()->subDay()->format('d/m/Y')],
]);

test('abre o retorno com o motivo e a data de afastamento travados e o campo de retorno', function () {
    $withdrawal = $this->ana->withdrawal($this->reason, ['data_saida' => $this->out]);

    $html = $this->get(($this->edit)($withdrawal, return: true))->assertOk()->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['sequencial'])->toBe((string) $withdrawal->sequencial)
        ->and($fields['retornar_servidor'])->toBe((string) EmployeeReturn::SIM)
        ->and(urldecode($fields['data_saida']))->toBe($this->out->format('d/m/Y'))
        ->and($fields)->toHaveKey('data_retorno')
        ->and($html)->toContain('disabled');
});

test('retorna o servidor gravando a data de retorno', function () {
    $withdrawal = $this->ana->withdrawal($this->reason, ['data_saida' => $this->out]);

    $this->post(($this->edit)($withdrawal, return: true), [
        'tipoacao' => 'Editar',
        'ref_cod_servidor' => (string) $this->id,
        'ref_cod_instituicao' => (string) $this->institution,
        'sequencial' => (string) $withdrawal->sequencial,
        'data_saida' => urlencode($this->out->format('d/m/Y')),
        'data_retorno' => $this->back->format('d/m/Y'),
    ])->assertRedirect($this->detail);

    expect($withdrawal->refresh()->data_retorno->format('Y-m-d'))->toBe($this->back->format('Y-m-d'))
        ->and($withdrawal->ref_cod_motivo_afastamento)->toBe($this->reason->getKey())
        ->and($withdrawal->data_saida->format('Y-m-d'))->toBe($this->out->format('Y-m-d'));
});

test('edita o histórico: motivo, datas e arquivos', function () {
    Storage::fake();

    $withdrawal = $this->ana->withdrawal($this->reason, ['data_saida' => $this->out, 'data_retorno' => $this->back]);
    $other = WithdrawalReasonFactory::new()->create(['nm_motivo' => 'Licença prêmio', 'ref_cod_instituicao' => $this->institution]);
    $old = FileFactory::new()->create(['original_name' => 'antigo.pdf']);
    FileRelationFactory::new()->create(['relation_type' => EmployeeWithdrawal::class, 'relation_id' => $withdrawal->getKey(), 'file_id' => $old->getKey()]);

    $html = $this->get(($this->edit)($withdrawal))->assertOk()->assertSee('antigo.pdf')->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['id'])->toBe((string) $withdrawal->getKey())
        ->and($fields['data_saida'])->toBe($this->out->format('d/m/Y'))
        ->and($fields['data_retorno'])->toBe($this->back->format('d/m/Y'));

    $this->post(($this->edit)($withdrawal), [
        'ref_cod_motivo_afastamento' => (string) $other->getKey(),
        'data_saida' => now()->subDays(30)->format('d/m/Y'),
        'data_retorno' => now()->subDays(2)->format('d/m/Y'),
        'file_url' => json_encode([['url' => 'https://arquivos.test/atestado.pdf', 'size' => 1234, 'originalName' => 'atestado.pdf', 'extension' => 'pdf']]),
        'file_url_deleted' => (string) $old->getKey(),
    ] + $fields)->assertRedirect($this->detail);

    expect($withdrawal->refresh())
        ->ref_cod_motivo_afastamento->toBe($other->getKey())
        ->and($withdrawal->data_saida->format('Y-m-d'))->toBe(now()->subDays(30)->format('Y-m-d'))
        ->and($withdrawal->data_retorno->format('Y-m-d'))->toBe(now()->subDays(2)->format('Y-m-d'))
        ->and($withdrawal->files()->pluck('original_name')->all())->toBe(['atestado.pdf']);

    $this->assertModelMissing($old);
});

test('recusa a edição com a data de afastamento que não é data ou o retorno anterior a ela', function (Closure $dates, string $message) {
    $withdrawal = $this->ana->withdrawal($this->reason, ['data_saida' => $this->out]);

    $this->post(($this->edit)($withdrawal), $dates->call($this) + [
        'tipoacao' => 'Editar',
        'ref_cod_servidor' => (string) $this->id,
        'ref_cod_instituicao' => (string) $this->institution,
        'sequencial' => (string) $withdrawal->sequencial,
    ])->assertOk()->assertSee($message);

    expect($withdrawal->refresh()->data_retorno)->toBeNull();
})->with([
    'afastamento que não é data' => [fn () => ['data_saida' => 'abc'], 'Data de Afastamento Inválida.'],
    'retorno antes do afastamento' => [fn () => ['data_saida' => $this->out->format('d/m/Y'), 'data_retorno' => $this->out->copy()->subDay()->format('d/m/Y')], 'A data de retorno não pode ser inferior à data de afastamento.'],
]);

test('grava os arquivos anexados no cadastro do afastamento', function () {
    $this->post($this->new, ($this->payload)([
        'file_url' => json_encode([['url' => 'https://arquivos.test/atestado.pdf', 'size' => 1234, 'originalName' => 'atestado.pdf', 'extension' => 'pdf']]),
    ]))->assertRedirect($this->detail);

    expect(($this->withdrawals)()->sole()->files()->pluck('original_name')->all())->toBe(['atestado.pdf']);
});

test('defeito: um dia que não existe no mês é aceito e vira outra data', function () {
    $year = now()->year;

    $this->post($this->new, ($this->payload)(['data_saida' => "31/02/{$year}"]))->assertRedirect($this->detail);

    expect(($this->withdrawals)()->sole()->data_saida->format('m'))->toBe('03');
})->skip(fn () => now()->month < 3, 'A data cairia no futuro ou há mais de um ano; só vale de março em diante.');

test('defeito: a exclusão apaga o afastamento e manda para uma tela que não existe', function () {
    $withdrawal = $this->ana->withdrawal($this->reason);

    $this->post(($this->edit)($withdrawal), [
        'tipoacao' => 'Excluir',
        'ref_cod_servidor' => (string) $this->id,
        'ref_cod_instituicao' => (string) $this->institution,
        'sequencial' => (string) $withdrawal->sequencial,
    ])->assertRedirect('educar_servidor_afastamento_lst.php');

    expect(EmployeeWithdrawal::query()->whereKey($withdrawal->getKey())->exists())->toBeFalse()
        ->and(file_exists(base_path('ieducar/intranet/educar_servidor_afastamento_lst.php')))->toBeFalse();
});

test('quem só pode visualizar não abre, não grava e não exclui', function (string $action) {
    $withdrawal = $this->ana->withdrawal($this->reason, ['data_saida' => $this->out]);

    ($this->viewer)(false, false);

    $this->get($this->new)->assertRedirect($this->detail);

    $this->post(($this->edit)($withdrawal), ($this->payload)([
        'tipoacao' => $action,
        'sequencial' => (string) $withdrawal->sequencial,
        'data_retorno' => $this->back->format('d/m/Y'),
    ]))->assertRedirect($this->detail);

    expect(($this->withdrawals)()->count())->toBe(1)
        ->and($withdrawal->refresh()->data_retorno)->toBeNull();
})->with(['Novo', 'Editar', 'Excluir']);
