<?php

use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyForm;
use Tests\Request\Support\StudentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->person = fn (string $name, array $attributes = []) => StudentScenario::individual($name, $attributes);
    $this->detail = fn ($individual) => 'atendidos_det.php?cod_pessoa=' . $individual->getKey();
    $this->list = fn (array $query = []) => $this->get('/intranet/atendidos_lst.php?' . http_build_query($query))->assertOk();
});

test('lista as pessoas físicas em ordem de nome, com a situação do CPF e o botão Novo', function () {
    $beta = ($this->person)('Registro Beta');
    $alfa = ($this->person)('Registro Alfa', ['cpf' => '12345678909']);

    ($this->list)(['nm_pessoa' => 'Registro'])
        ->assertSeeInOrder(['Registro Alfa', '123.456.789-09', 'Registro Beta', 'Sem CPF'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee("<span class='cpf-situacao cpf-situacao-com'>123.456.789-09</span>", false)
        ->assertSee('go("atendidos_cad.php")', false);
});

test('defeito: o nome social nunca aparece', function () {
    // A pessoa física é carregada só com o CPF, então o nome social vem vazio.
    $alfa = ($this->person)('Registro Alfa', ['nome_social' => 'Nina Alfa']);

    ($this->list)(['nm_pessoa' => 'Registro Alfa'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee('Nina Alfa');
});

test('filtra pelo nome sem acento e pelo CPF com máscara', function (array $query) {
    $alfa = ($this->person)('Registro Álfa', ['cpf' => '12345678909']);
    $beta = ($this->person)('Registro Beta', ['cpf' => '98765432100']);

    ($this->list)($query)
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
})->with([
    'nome' => [['nm_pessoa' => 'tro alfa']],
    'CPF' => [['id_federal' => '123.456.789-09']],
]);

test('filtra pela situação do CPF e pelo motivo da ausência', function (string $situation, ?string $reason, array $shown) {
    $reasons = [
        'documento em emissão' => DocumentAbsenceReasonFactory::new()->create(['name' => 'Documento em emissão']),
        'estrangeiro' => DocumentAbsenceReasonFactory::new()->create(['name' => 'Estrangeiro']),
    ];

    $people = [
        'com CPF' => ($this->person)('Registro Alfa', ['cpf' => '12345678909']),
        'justificada' => ($this->person)('Registro Beta', ['document_absence_reason_id' => $reasons['documento em emissão']->getKey()]),
        'outra justificativa' => ($this->person)('Registro Gama', ['document_absence_reason_id' => $reasons['estrangeiro']->getKey()]),
        'sem justificativa' => ($this->person)('Registro Delta'),
    ];

    $response = ($this->list)(array_filter([
        'nm_pessoa' => 'Registro',
        'situacao_cpf' => $situation,
        'motivo_ausencia_cpf' => $reason ? $reasons[$reason]->getKey() : null,
    ]));

    foreach ($people as $label => $person) {
        in_array($label, $shown, true)
            ? $response->assertSee(($this->detail)($person), false)
            : $response->assertDontSee(($this->detail)($person), false);
    }
})->with([
    'com CPF' => ['with_cpf', null, ['com CPF']],
    'ausência justificada' => ['justified_absence', null, ['justificada', 'outra justificativa']],
    'ausência justificada por um motivo' => ['justified_absence', 'estrangeiro', ['outra justificativa']],
    'sem justificativa' => ['unjustified_absence', null, ['sem justificativa']],
    'situação desconhecida lista todas' => ['qualquer', null, ['com CPF', 'justificada', 'outra justificativa', 'sem justificativa']],
    // O motivo só vale com a situação "ausência justificada".
    'motivo sem a situação' => ['', 'estrangeiro', ['com CPF', 'justificada', 'outra justificativa', 'sem justificativa']],
]);

test('oferece as situações e só os motivos de ausência que não são do sistema', function () {
    DocumentAbsenceReasonFactory::new()->create(['name' => 'Documento em emissão']);
    DocumentAbsenceReasonFactory::new()->create(['name' => 'Motivo do sistema', 'from_system' => true]);

    $form = LegacyForm::from(($this->list)()->getContent());

    expect(array_values($form->options('situacao_cpf')))->toBe(['Todos', 'Com CPF', 'Sem CPF - ausência justificada', 'Sem CPF - sem justificativa'])
        ->and(array_values($form->options('motivo_ausencia_cpf')))->toContain('Todos', 'Documento em emissão')->not->toContain('Motivo do sistema');
});

test('não lista pessoa inativa', function () {
    $inactive = ($this->person)('Registro Alfa', ['ativo' => 0]);

    ($this->list)(['nm_pessoa' => 'Registro Alfa'])->assertDontSee(($this->detail)($inactive), false);
});

test('pagina de 10 em 10', function () {
    foreach (range(1, 11) as $number) {
        ($this->person)(sprintf('Registro %02d', $number));
    }

    ($this->list)(['nm_pessoa' => 'Registro'])->assertSee('Registro 10')->assertDontSee('Registro 11');
    ($this->list)(['nm_pessoa' => 'Registro', 'pagina_formulario' => 2])->assertSee('Registro 11')->assertDontSee('Registro 10');
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(43, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    ($this->list)()->assertDontSee('go("atendidos_cad.php")', false);
});
