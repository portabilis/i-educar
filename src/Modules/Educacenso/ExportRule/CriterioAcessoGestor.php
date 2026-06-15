<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro40;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\SituacaoFuncionamento;

class CriterioAcessoGestor implements EducacensoExportRule
{
    /**
     * @param Registro40 $registro40
     */
    public static function handle(RegistroEducacenso $registro40): RegistroEducacenso
    {
        if ($registro40->situacaoFuncionamento != SituacaoFuncionamento::EM_ATIVIDADE) {
            $registro40->criterioAcesso = null;
        }

        return $registro40;
    }
}
