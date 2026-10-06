<?php

use App\Models\EmployeeInep;
use App\Models\LegacyPerson;
use App\Models\SchoolManager;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolManagerFactory;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\SchoolFormData;

/**
 * A tabela de gestores do formulário de escola: as regras que valem sempre, o
 * que é gravado junto com cada gestor e a troca da lista inteira a cada edição.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = LegacySchoolFactory::new()->withName('Escola Alfa')->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'sigla' => 'EALFA',
    ]);
    $this->current = SchoolManagerFactory::new()->create(['school_id' => $this->school, 'chief' => true])->employee_id;

    $this->url = '/intranet/educar_escola_cad.php?cod_escola=' . $this->school->getKey();

    // Um servidor que pode ser gestor: pessoa física com registro de servidor.
    $this->employee = function () {
        $individual = LegacyIndividualFactory::new()->create();
        EmployeeFactory::new()->create(['id' => $individual->idpes]);

        return $individual->idpes;
    };

    $this->save = fn (array $managers) => $this->post(
        $this->url,
        LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $managers + ['sigla' => 'MUDOU']),
    );

    $this->managers = fn () => SchoolManager::query()
        ->where('school_id', $this->school->getKey())
        ->orderBy('employee_id')
        ->get()
        ->mapWithKeys(fn ($manager) => [$manager->employee_id => (bool) $manager->chief])
        ->all();
});

test('recusa a tabela de gestores fora das regras', function (Closure $rows, string $field, string $message) {
    $response = ($this->save)($rows->call($this));

    // Estas regras são validadas pelo Laravel: a tela volta com os erros na sessão.
    $response->assertRedirect()->assertSessionHasErrors([$field => $message]);

    expect($this->school->refresh()->sigla)->toBe('EALFA')
        ->and(($this->managers)())->toBe([$this->current => true]);
})->with([
    'mais de três gestores' => [
        fn () => SchoolFormData::managers([($this->employee)(), ($this->employee)(), ($this->employee)(), ($this->employee)()]),
        'servidor_id',
        'Informe no máximo 3 Gestores escolares',
    ],
    'o mesmo gestor duas vezes' => [
        function () {
            $employee = ($this->employee)();

            return SchoolFormData::managers([$employee, $employee]);
        },
        'servidor_id',
        'Não é possível selecionar 2 gestores iguais',
    ],
    'nenhum gestor principal' => [
        fn () => ['managers_chief' => [0]] + SchoolFormData::managers([($this->employee)()]),
        'managers_chief',
        'Informe pelo menos um(a) gestor(a) como gestor(a) principal',
    ],
    'INEP do gestor com tamanho errado' => [
        fn () => ['managers_inep_id' => ['12345']] + SchoolFormData::managers([($this->employee)()]),
        'managers_inep_id.0',
        'O campo: Código INEP do gestor(a) deve conter 12 dígitos',
    ],
]);

test('depois de uma recusa, a tabela volta com os gestores digitados', function () {
    $employee = ($this->employee)();

    ($this->save)(['managers_chief' => [0], 'managers_individual_nome' => ['Gestor Digitado']] + SchoolFormData::managers([$employee]))
        ->assertRedirect();

    $fields = LegacyFields::from($this->get($this->url)->assertOk()->getContent());

    expect($fields['servidor_id'])->toBe([(string) $employee])
        ->and($fields['managers_chief'])->toBe(['0']);
});

test('troca a lista de gestores pela que foi enviada', function () {
    $first = ($this->employee)();
    $second = ($this->employee)();

    ($this->save)(SchoolFormData::managers([$first, $second], chief: 1))->assertRedirect('educar_escola_lst.php');

    $expected = [$first => false, $second => true];
    ksort($expected);

    expect(($this->managers)())->toBe($expected);
});

test('ignora a linha de gestor sem servidor escolhido', function () {
    $employee = ($this->employee)();

    $rows = SchoolFormData::managers([$employee, 0]);
    $rows['servidor_id'][1] = '';

    ($this->save)($rows)->assertRedirect('educar_escola_lst.php');

    expect(($this->managers)())->toBe([$employee => true]);
});

test('grava o e-mail e o INEP do gestor junto com a escola', function () {
    $employee = ($this->employee)();

    ($this->save)(['managers_email' => ['gestor@escola.test'], 'managers_inep_id' => ['123456789012']] + SchoolFormData::managers([$employee]))
        ->assertRedirect('educar_escola_lst.php');

    expect(LegacyPerson::query()->find($employee)->email)->toBe('gestor@escola.test')
        ->and(EmployeeInep::query()->where('cod_servidor', $employee)->value('cod_docente_inep'))->toEqual(123456789012);
});
