<?php

namespace App\Models\View;

use App\Models\LegacyDisciplineScoreAverage;
use App_Model_MatriculaSituacao;
use Illuminate\Database\Eloquent\Model;

class Situation extends Model
{
    protected $table = 'relatorio.view_situacao';

    protected $primaryKey = 'cod_matricula';

    public $timestamps = false;

    public function scopeApproved($query): void
    {
        $query->whereIn('cod_situacao', [
            App_Model_MatriculaSituacao::APROVADO,
            App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA,
            App_Model_MatriculaSituacao::APROVADO_PELO_CONSELHO,
        ]);
    }

    public function scopeSituation($query, int $situation): void
    {
        if ($situation === 16) {
            $query->whereExists(
                LegacyDisciplineScoreAverage::selectRaw(1)
                    ->whereHas('registrationScore', function ($q) {
                        $q->whereColumn('matricula_id', 'cod_matricula');
                    })
                    ->where('situacao', App_Model_MatriculaSituacao::APROVADO_APOS_EXAME)
            );
            $query->approved();
        } else {
            $query->where('cod_situacao', $situation);
        }
    }
}
