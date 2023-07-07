<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\Ativo;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWithdrawal extends LegacyModel
{
    use Ativo;
    use HasFiles;
    use HasLegacyDates;
    use HasLegacyUserAction;
    use LegacySoftDeletes;

    protected $table = 'pmieducar.servidor_afastamento';

    protected $fillable = [
        'ref_cod_servidor',
        'sequencial',
        'ref_ref_cod_instituicao',
        'ref_cod_motivo_afastamento',
        'data_retorno',
        'data_saida',
        'sequencial',
    ];

    protected $casts = [
        'data_retorno' => 'date',
        'data_saida' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'ref_cod_servidor');
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(WithdrawalReason::class, 'ref_cod_motivo_afastamento');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employeeWithdrawal) {
            $employeeWithdrawal->sequencial = $employeeWithdrawal->employee->withdrawals()->withTrashed()->count() + 1;
        });
    }
}
