<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * EmployeeInep
 *
 * @property Employee $employee
 *
 */
class LegacyStudentTransport extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.transporte_aluno';

    /**
     * @var string
     */
    protected $primaryKey = 'aluno_id';

    protected $fillable = [
        'responsavel',
        'user_id',
        'aluno_id'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'aluno_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'user_id');
    }
}
