<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro60;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\PaisResidencia;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Model\TipoMediacaoDidaticoPedagogico;

class TransporteEscolarPublico implements EducacensoExportRule
{
    /**
     * @param Registro60 $registro60
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro60): RegistroEducacenso
    {
        $arrayTipoMediacao = [
            TipoMediacaoDidaticoPedagogico::PRESENCIAL,
            TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL,
        ];

        if ($registro60->tipoAtendimentoTurma != TipoAtendimentoTurma::ESCOLARIZACAO ||
            !in_array($registro60->tipoMediacaoTurma, $arrayTipoMediacao) ||
            $registro60->paisResidenciaAluno != PaisResidencia::BRASIL) {
            $registro60->transportePublico = null;
        }

        return $registro60;
    }
}
