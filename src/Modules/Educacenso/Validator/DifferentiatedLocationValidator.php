<?php

namespace iEducar\Modules\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\LocalizacaoDiferenciadaPessoa;
use App_Model_ZonaLocalizacao;

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

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if (LocalizacaoDiferenciadaPessoa::AREA_ASSENTAMENTO == $this->differentiatedLocation &&
            App_Model_ZonaLocalizacao::URBANA == $this->locationZone) {
            $this->message = 'O campo: Localização diferenciada não pode ser preenchido com <b>Área de assentamento</b> quando o campo: Zona de residência for <b>Urbana</b>.';

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
