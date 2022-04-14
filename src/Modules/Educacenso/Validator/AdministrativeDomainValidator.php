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

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->regulations == Regulamentacao::NAO) {
            return true;
        }

        /**
         * Se a dependência administrativa da escola for: 2 (Estadual)
         * a esfera administrativa também deve ser 2
         */
        if (
            $this->administrativeDependence == DependenciaAdministrativaEscola::ESTADUAL &&
            $this->administrativeDomain != EsferaAdministrativa::ESTADUAL
        ) {
            return false;
        }

        /**
         * Se a dependência administrativa da escola for: 1 (Federal)
         * a esfera administrativa deve ser 1 ou 2
         */
        if (
            $this->administrativeDependence == DependenciaAdministrativaEscola::FEDERAL &&
            (
                $this->administrativeDomain != EsferaAdministrativa::FEDERAL &&
                $this->administrativeDomain != EsferaAdministrativa::ESTADUAL
            )
        ) {
            return false;
        }

        /**
         * Se a dependência administrativa da escola for: 3 (Municipal)
         * a esfera administrativa deve ser 2 ou 3
         */
        if (
            $this->administrativeDependence == DependenciaAdministrativaEscola::MUNICIPAL &&
            (
                $this->administrativeDomain != EsferaAdministrativa::ESTADUAL &&
                $this->administrativeDomain != EsferaAdministrativa::MUNICIPAL
            )
        ) {
            return false;
        }

        /**
         * Se o município da escola for: Brasília
         * a esfera administrativa deve ser diferente de Municipal
         */
        if (
            $this->cityIbgeCode == self::BRASILIA &&
            $this->administrativeDomain == EsferaAdministrativa::MUNICIPAL
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
