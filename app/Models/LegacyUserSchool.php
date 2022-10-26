<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyUserSchool extends Model
{
    protected $table = 'pmieducar.escola_usuario';

    public $timestamps = false;

    protected $fillable = [
        'ref_cod_usuario',
        'ref_cod_escola',
        'escola_atual',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'ref_cod_usuario');
    }
}
