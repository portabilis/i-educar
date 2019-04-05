<?php

namespace iEducar\Modules\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\SchoolManagerAccessCriteria;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;

class SchoolManagers implements EducacensoValidator
{
    private $message;
    private $individualArray;
    private $roleArray;
    private $accessCriteriaArray;
    private $accessCriteriaDescriptionArray;
    private $linkTypeArray;
    private $isChiefArray;
    private $administrativeDependency;
    private $valid = true;

    /**
     * @param integer[] $individualArray
     * @param integer[] $roleArray
     * @param integer[] $accessCriteriaArray
     * @param string[] $accessCriteriaDescriptionArray
     * @param integer[] $linkTypeArray
     * @param boolean[] $isChiefArray
     */
    public function __construct($individualArray, $roleArray, $accessCriteriaArray, $accessCriteriaDescriptionArray, $linkTypeArray, $isChiefArray, $administrativeDependency)
    {
        $this->individualArray = $individualArray;
        $this->roleArray = $roleArray;
        $this->accessCriteriaArray = $accessCriteriaArray;
        $this->accessCriteriaDescriptionArray = $accessCriteriaDescriptionArray;
        $this->linkTypeArray = $linkTypeArray;
        $this->isChiefArray = $isChiefArray;
        $this->administrativeDependency = $administrativeDependency;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->containsEmptyOrIsNull($this->individualArray)) {
            $this->message[] = 'Você precisa cadastrar pelo menos um gestor';
            $this->valid = false;
        }

        if ($this->containsEmptyOrIsNull($this->roleArray)) {
            $this->message[] = 'Você precisa informar o cargo dos gestores';
            $this->valid = false;
        }

        foreach ($this->individualArray as $key => $value) {
            $this->validateAccessCriteria($key);
            $this->validateAccessCriteriaDescription($key);
            $this->validateAccessLinkType($key);
        }

        return $this->valid;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $array
     * @return bool
     */
    private function containsEmptyOrIsNull($array)
    {
        return empty($array) || in_array(null, $array);
    }

    /**
     * @param $key
     */
    private function validateAccessCriteria($key)
    {
        if ($this->roleArray[$key] == SchoolManagerRole::DIRETOR && empty($this->accessCriteriaArray[$key])) {
            $this->valid = false;
            $this->message[] = 'Se o cargo do gestor for <b>Diretor</b>, você precisa informar o critério de acesso ao cargo';
        }
    }

    private function validateAccessCriteriaDescription($key)
    {
        if ($this->accessCriteriaArray[$key] == SchoolManagerAccessCriteria::OUTRO && empty($this->accessCriteriaDescriptionArray[$key])) {
            $this->valid = false;
            $this->message[] = 'Se o citério de acesso ao cargo do gestor for <b>Outros</b>, você precisa informar uma especificação';
        }
    }

    private function validateAccessLinkType(int $key)
    {
        if ($this->administrativeDependency == DependenciaAdministrativaEscola::PRIVADA) {
            return;
        }

        if ($this->roleArray[$key] == SchoolManagerRole::DIRETOR && empty($this->linkTypeArray[$key])) {
            $this->valid = false;
            $this->message[] = 'O campo <b>Tipo de vínculo</b> do gestor precisa ser preenchido';
        }
    }
}
