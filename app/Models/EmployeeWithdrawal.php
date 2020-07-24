<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmployeeWithdrawal extends Model
{
    use HasFiles;

    protected $table = 'pmieducar.servidor_afastamento';

    protected $fillable = [
        'ref_cod_servidor',
        'sequencial',
        'ref_ref_cod_instituicao',
        'ref_cod_motivo_afastamento',
        'ref_usuario_exc',
        'ref_usuario_cad',
        'data_cadastro',
        'data_exclusao',
        'data_retorno',
        'data_saida',
        'ativo',
    ];

    protected $dates = [
        'data_cadastro',
        'data_exclusao',
        'data_retorno',
        'data_saida',
    ];

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'ref_cod_servidor', 'cod_servidor');
    }

    /**
     * @return BelongsTo
     */
    public function reason()
    {
        return $this->belongsTo(WithdrawalReason::class, 'ref_cod_motivo_afastamento', 'cod_motivo_afastamento');
    }
}
