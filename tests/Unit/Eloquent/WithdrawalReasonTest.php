<?php

use App\Models\EmployeeWithdrawal;
use App\Models\LegacyInstitution;
use App\Models\LegacyUser;
use App\Models\WithdrawalReason;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(
    WithdrawalReason::class,
    relations: [
        'institution' => LegacyInstitution::class,
        'employeeWithdrawals' => EmployeeWithdrawal::class,
        'createdByUser' => LegacyUser::class,
        'deletedByUser' => LegacyUser::class,
    ],
    legacyAttributes: [
        'id' => 'cod_motivo_afastamento',
        'name' => 'nm_motivo',
        'description' => 'descricao',
        'created_at' => 'data_cadastro',
        'deleted_by' => 'ref_usuario_exc',
        'created_by' => 'ref_usuario_cad',
        'institution_id' => 'ref_cod_instituicao',
        'active' => 'ativo',
    ],
));

EloquentModelTestCase::registerModelTests();
