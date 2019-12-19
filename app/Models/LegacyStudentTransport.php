<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    protected $fillable = ['responsavel', 'user_id', 'aluno_id'];
}
