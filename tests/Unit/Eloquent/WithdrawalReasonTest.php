<?php

namespace Tests\Unit\Eloquent;

use App\Models\EmployeeWithdrawal;
use App\Models\LegacyInstitution;
use App\Models\LegacyUser;
use App\Models\WithdrawalReason;
use Tests\EloquentTestCase;

class WithdrawalReasonTest extends EloquentTestCase
{
    protected $relations = [
        'institution' => LegacyInstitution::class,
        'employeeWithdrawals' => EmployeeWithdrawal::class,
        'createdByUser' => LegacyUser::class,
        'deletedByUser' => LegacyUser::class,
    ];

    protected function getEloquentModelName(): string
    {
        return WithdrawalReason::class;
    }

    protected function getLegacyAttributes(): array
    {
        return [
            'id' => 'cod_motivo_afastamento',
            'name' => 'nm_motivo',
            'description' => 'descricao',
            'created_at' => 'data_cadastro',
            'deleted_by' => 'ref_usuario_exc',
            'created_by' => 'ref_usuario_cad',
            'institution_id' => 'ref_cod_instituicao',
            'active' => 'ativo',
        ];
    }
}
