<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro60;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Model\TipoMediacaoDidaticoPedagogico;

class RecebeEscolarizacaoOutroEspaco implements EducacensoExportRule
{
    /**
     * @param Registro60 $registro60
     */
    public static function handle(RegistroEducacenso $registro60): RegistroEducacenso
    {
        if (!in_array(TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO, $registro60->tipoAtendimentoTurma) ||
            $registro60->tipoMediacaoTurma != TipoMediacaoDidaticoPedagogico::PRESENCIAL ||
            (
                $registro60->localFuncionamentoDiferenciadoTurma != \App_Model_LocalFuncionamentoDiferenciado::NAO_ESTA &&
                $registro60->localFuncionamentoDiferenciadoTurma != \App_Model_LocalFuncionamentoDiferenciado::SALA_ANEXA
            )
        ) {
            $registro60->recebeEscolarizacaoOutroEspacao = null;
        }

        return $registro60;
    }
}
