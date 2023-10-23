<?php

namespace iEducar\Modules\Educacenso\Validator;

use App_Model_ZonaLocalizacao;
use iEducar\Modules\Educacenso\Model\LocalizacaoDiferenciadaPessoa;

class DifferentiatedLocationValidator implements EducacensoValidator
{
    private $message = '';

    private $differentiatedLocation;

    private $locationZone;

    public function __construct($differentiatedLocation, $locationZone)
    {
        $this->differentiatedLocation = $differentiatedLocation;
        $this->locationZone = $locationZone;
    }

    public function isValid(): bool
    {
        if ($this->differentiatedLocation == LocalizacaoDiferenciadaPessoa::AREA_ASSENTAMENTO &&
            $this->locationZone == App_Model_ZonaLocalizacao::URBANA) {
            $this->message = 'O campo: Localização diferenciada de residência não pode ser preenchido com <b>Área de assentamento</b> quando o campo: Zona de residência for <b>Urbana</b>.';

            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
