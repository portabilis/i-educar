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
        'employeeWithdrawals' => [EmployeeWithdrawal::class],
        'createdByUser' => LegacyUser::class,
        'deletedByUser' => LegacyUser::class
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return WithdrawalReason::class;
    }

    protected function getLegacyAttributes(): array
    {
        return [
            'id' => 'cod_motivo_afastamento',
            'name' => 'nm_motivo',
            'description' => 'descricao'
        ];
    }
}
