<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro00;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Validator\AdministrativeDomainValidator;

class EsferaAdministrativa implements EducacensoExportRule
{
    /**
     * Anula a esfera administrativa (campo 50) no arquivo quando o valor
     * não é permitido para a dependência administrativa da escola, sem
     * alterar o dado no banco.
     *
     * @param Registro00 $registro00
     */
    public static function handle(RegistroEducacenso $registro00): RegistroEducacenso
    {
        $validator = new AdministrativeDomainValidator(
            $registro00->esferaAdministrativa,
            $registro00->dependenciaAdministrativa,
            $registro00->codigoIbgeMunicipio
        );

        if (!$validator->isValid()) {
            $registro00->esferaAdministrativa = null;
        }

        return $registro00;
    }
}
