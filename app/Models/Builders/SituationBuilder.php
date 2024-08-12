<?php

namespace App\Models\Builders;

use App\Models\LegacyDisciplineScoreAverage;
use App_Model_MatriculaSituacao;

class SituationBuilder extends LegacyBuilder
{
    /**
     * Filtra por situação específica
     */
    public function situation(int $situation): self
    {
        if ($situation === 16) {
            $this->whereExists(
                LegacyDisciplineScoreAverage::selectRaw(1)
                    ->whereHas('registrationScore', function ($q) {
                        $q->whereColumn('matricula_id', 'cod_matricula');
                    })
                    ->where('situacao', App_Model_MatriculaSituacao::APROVADO_APOS_EXAME)
            );
            $this->approved();
        } else {
            $this->where('cod_situacao', $situation);
        }

        return $this;
    }

    /**
     * Filtra por situações aprovadas
     */
    public function approved(): self
    {
        return $this->whereIn('cod_situacao', [
            App_Model_MatriculaSituacao::APROVADO,
            App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA,
            App_Model_MatriculaSituacao::APROVADO_PELO_CONSELHO,
        ]);
    }
}
