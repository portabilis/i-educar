<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolYearLockBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static LegacySchoolYearLockBuilder query()
 */
class LegacySchoolYearLock extends Model
{
    /** @use HasBuilder<LegacySchoolYearLockBuilder> */
    use HasBuilder;

    protected static string $builder = LegacySchoolYearLockBuilder::class;

    protected $table = 'pmieducar.bloqueio_ano_letivo';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'ref_cod_instituicao',
        'ref_ano',
        'data_inicio',
        'data_fim',
    ];
}
