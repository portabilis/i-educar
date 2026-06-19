<?php

namespace iEducar\Modules\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\EsferaAdministrativa;
use iEducar\Modules\Educacenso\Model\Regulamentacao;

class AdministrativeDomainValidator implements EducacensoValidator
{
    private $administrativeDomain;

    private $regulations;

    private $administrativeDependence;

    private $cityIbgeCode;

    private const BRASILIA = 5300108;

    public function __construct(
        $administrativeDomain,
        $regulations,
        $administrativeDependence,
        $cityIbgeCode
    ) {
        $this->administrativeDomain = $administrativeDomain;
        $this->regulations = $regulations;
        $this->administrativeDependence = $administrativeDependence;
        $this->cityIbgeCode = $cityIbgeCode;
    }

    public function isValid(): bool
    {
        if ($this->regulations == Regulamentacao::NAO) {
            return true;
        }

        $esferasNaoPermitidas = [
            EsferaAdministrativa::MUNICIPAL,
            EsferaAdministrativa::ESTADUAL_E_MUNICIPAL,
        ];

        /**
         * Layout do Censo (campo 50): a esfera administrativa não pode ser
         * 3 (Municipal) ou 4 (Estadual e Municipal) quando a dependência
         * administrativa for 1 (Federal) ou 2 (Estadual).
         */
        if (
            in_array($this->administrativeDependence, [
                DependenciaAdministrativaEscola::FEDERAL,
                DependenciaAdministrativaEscola::ESTADUAL,
            ]) &&
            in_array($this->administrativeDomain, $esferasNaoPermitidas)
        ) {
            return false;
        }

        /**
         * Layout do Censo (campo 50): a esfera administrativa não pode ser
         * 3 (Municipal) ou 4 (Estadual e Municipal) quando o município for Brasília.
         */
        if (
            $this->cityIbgeCode == self::BRASILIA &&
            in_array($this->administrativeDomain, $esferasNaoPermitidas)
        ) {
            return false;
        }

        return true;
    }

    public function getMessage()
    {
        return 'O campo: Esfera administrativa do conselho ou órgão responsável pela Regulamentação/Autorização, foi preenchido com um valor incorreto';
    }
}
