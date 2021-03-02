<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro40;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\SchoolManagerAccessCriteria;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;

class CriterioAcessoGestor implements EducacensoExportRule
{
    /**
     * @param Registro40 $registro40
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro40): RegistroEducacenso
    {
        if ($registro40->situacaoFuncionamento != \iEducar\Modules\Educacenso\Model\SituacaoFuncionamento::EM_ATIVIDADE) {
            $registro40->criterioAcesso = null;
        }

        return $registro40;
    }
}
