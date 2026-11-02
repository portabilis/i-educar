<?php

use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('lista os turnos sem o integral e, fora da EJA, sem o noturno', function () {
    $this->getJson('/api/period')
        ->assertOk()
        ->assertExactJson(['data' => [1 => 'Matutino', 2 => 'Vespertino', 3 => 'Noturno']]);

    $regular = LegacySchoolClassFactory::new()->create();

    $this->getJson('/api/period?schoolclass=' . $regular->getKey())
        ->assertOk()
        ->assertExactJson(['data' => [1 => 'Matutino', 2 => 'Vespertino']]);

    $this->getJson('/api/period?schoolclass=999999999')
        ->assertOk()
        ->assertExactJson(['data' => [1 => 'Matutino', 2 => 'Vespertino']]);

    $regular->course->update(['modalidade_curso' => ModalidadeCurso::EJA]);

    $this->getJson('/api/period?schoolclass=' . $regular->getKey())
        ->assertOk()
        ->assertExactJson(['data' => [1 => 'Matutino', 2 => 'Vespertino', 3 => 'Noturno']]);
});
