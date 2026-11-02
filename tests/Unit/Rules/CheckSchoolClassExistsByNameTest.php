<?php

use App\Rules\CheckSchoolClassExistsByName;
use Database\Factories\LegacySchoolClassFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

// A regra lê os campos do formulário (curso, série, escola e ano letivo), não
// as colunas da turma.
test('o nome não pode repetir o de outra turma visível da mesma escola, curso, série e ano', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma A']);
    $rule = new CheckSchoolClassExistsByName;
    $form = fn (array $overrides = []) => (object) ($overrides + [
        'nm_turma' => 'Turma A',
        'ref_cod_curso' => $schoolClass->ref_cod_curso,
        'ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
        'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
        'ano_letivo' => $schoolClass->ano,
        'id' => null,
    ]);

    expect($rule->passes('schoolClass', $form()))->toBeFalse()
        ->and($rule->message())->toBe('O nome da turma já está sendo utilizado nesta escola, para o curso, série e anos informados.')
        ->and($rule->passes('schoolClass', $form(['id' => $schoolClass->getKey()])))->toBeTrue()
        ->and($rule->passes('schoolClass', $form(['nm_turma' => 'Turma B'])))->toBeTrue();
});
