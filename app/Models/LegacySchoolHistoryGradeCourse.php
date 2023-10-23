<?php

namespace App\Models;

class LegacySchoolHistoryGradeCourse extends LegacyModel
{
    protected $table = 'pmieducar.historico_grade_curso';

    protected $fillable = [
        'descricao_etapa',
        'quantidade_etapas',
        'ativo',
    ];
}
