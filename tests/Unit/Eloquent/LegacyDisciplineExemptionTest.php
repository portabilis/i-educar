<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionStage;
use App\Models\LegacyExemptionType;
use App\Models\LegacyRegistration;
use App\Models\LegacyUser;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(
    LegacyDisciplineExemption::class,
    relations: [
        'registration' => LegacyRegistration::class,
        'discipline' => LegacyDiscipline::class,
        'type' => LegacyExemptionType::class,
        'stages' => LegacyExemptionStage::class,
        'deletedByUser' => LegacyUser::class,
        'createdByUser' => LegacyUser::class,
        'createdBy' => LegacyUser::class,
    ],
    legacyAttributes: [
        'id' => 'cod_dispensa',
        'registration_id' => 'ref_cod_matricula',
        'discipline_id' => 'ref_cod_disciplina',
        'school_id' => 'ref_cod_escola',
        'grade_id' => 'ref_cod_serie',
        'exemption_type_id' => 'ref_cod_tipo_dispensa',
        'observation' => 'observacao',
        'created_at' => 'data_cadastro',
        'deleted_at' => 'data_exclusao',
        'active' => 'ativo',
        'deleted_by' => 'ref_usuario_exc',
        'created_by' => 'ref_usuario_cad',
    ],
));

EloquentModelTestCase::registerModelTests();

test('scope active', function () {
    $query = LegacyDisciplineExemption::query()
        ->active()
        ->first();
    $this->assertInstanceOf(LegacyDisciplineExemption::class, $query);
    $this->assertEquals(1, $query->ativo);
});
