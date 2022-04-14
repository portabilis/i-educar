<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro00;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;

class DependenciaAdministrativa implements EducacensoExportRule
{
    /**
     * @param Registro00 $registro00
     *
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro00): RegistroEducacenso
    {
        if ($registro00->dependenciaAdministrativa != DependenciaAdministrativaEscola::PRIVADA) {
            $registro00->mantenedoraEscolaPrivada = null;
            $registro00->mantenedoraEmpresa = null;
            $registro00->mantenedoraSindicato = null;
            $registro00->mantenedoraOng = null;
            $registro00->mantenedoraInstituicoes = null;
            $registro00->mantenedoraSistemaS = null;
            $registro00->mantenedoraOscip = null;
            $registro00->categoriaEscolaPrivada = null;
            $registro00->conveniadaPoderPublico = null;
            $registro00->cnpjMantenedoraPrincipal = null;
            $registro00->cnpjEscolaPrivada = null;
        }

        return $registro00;
    }
}
