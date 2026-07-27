<?php

namespace iEducar\Modules\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\EsferaAdministrativa;

class AdministrativeDomainValidator implements EducacensoValidator
{
    private $administrativeDomain;

    private $administrativeDependence;

    private $cityIbgeCode;

    private const BRASILIA = 5300108;

    public function __construct(
        $administrativeDomain,
        $administrativeDependence,
        $cityIbgeCode
    ) {
        $this->administrativeDomain = $administrativeDomain;
        $this->administrativeDependence = $administrativeDependence;
        $this->cityIbgeCode = $cityIbgeCode;
    }

    public function isValid(): bool
    {
        if (empty($this->administrativeDomain)) {
            return true;
        }

        return in_array((int) $this->administrativeDomain, $this->esferasPermitidas(), true);
    }

    /**
     * Layout do Censo (campo 50): as esferas administrativas permitidas
     * dependem da dependência administrativa da escola.
     *
     * @return int[]
     */
    private function esferasPermitidas(): array
    {
        return match ((int) $this->administrativeDependence) {
            DependenciaAdministrativaEscola::FEDERAL => [
                EsferaAdministrativa::FEDERAL,
                EsferaAdministrativa::FEDERAL_SETEC,
            ],
            DependenciaAdministrativaEscola::ESTADUAL => [
                EsferaAdministrativa::ESTADUAL,
            ],
            DependenciaAdministrativaEscola::MUNICIPAL => [
                EsferaAdministrativa::ESTADUAL,
                EsferaAdministrativa::MUNICIPAL,
                EsferaAdministrativa::ESTADUAL_E_MUNICIPAL,
            ],
            DependenciaAdministrativaEscola::PRIVADA => $this->cityIbgeCode == self::BRASILIA
                ? [EsferaAdministrativa::ESTADUAL]
                : [
                    EsferaAdministrativa::ESTADUAL,
                    EsferaAdministrativa::MUNICIPAL,
                    EsferaAdministrativa::ESTADUAL_E_MUNICIPAL,
                ],
            default => [],
        };
    }

    public function getMessage()
    {
        return 'O campo: Esfera administrativa do conselho ou órgão responsável pela Regulamentação/Autorização, foi preenchido com um valor incorreto';
    }
}
