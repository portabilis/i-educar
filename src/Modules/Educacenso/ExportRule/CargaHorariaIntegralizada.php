<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro60;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\EtapaEnsino;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular;

class CargaHorariaIntegralizada implements EducacensoExportRule
{
    /**
     * @param Registro60 $registro60
     */
    public static function handle(RegistroEducacenso $registro60): RegistroEducacenso
    {
        $temItinerarioFormacaoTecnica = in_array(
            OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL,
            array_map('intval', (array) $registro60->organizacaoCurricularTurma),
            true
        );

        $etapaPermite = in_array(
            (int) $registro60->etapaTurma,
            EtapaEnsino::ETAPAS_PERMITEM_CARGA_HORARIA_INTEGRALIZADA,
            true
        );

        // Sem itinerário de formação técnica e sem etapa que permita, o campo não
        // pode ser preenchido, então é exportado como nulo.
        if (!$temItinerarioFormacaoTecnica && !$etapaPermite) {
            $registro60->cargaHorariaIntegralizada = null;

            return $registro60;
        }

        // Quando o campo é permitido mas o(a) aluno(a) não tem carga informada,
        // o valor exportado é 0 (zero), e não vazio.
        if (is_null($registro60->cargaHorariaIntegralizada)) {
            $registro60->cargaHorariaIntegralizada = 0;
        }

        return $registro60;
    }
}
