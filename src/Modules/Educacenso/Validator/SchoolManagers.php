<?php

namespace iEducar\Modules\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\SchoolManagerAccessCriteria;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;
use iEducar\Modules\ValueObjects\SchoolManagerValueObject;

class SchoolManagers implements EducacensoValidator
{
    private $message;
    private $administrativeDependency;
    private $valid = true;

    /**
     * @var SchoolManagerValueObject[]
     */
    private $valueObject;

    /**
     * @param SchoolManagerValueObject[] $valueObject
     * @param integer $administrativeDependency
     */
    public function __construct($valueObject, $administrativeDependency)
    {
        $this->valueObject = $valueObject;
        $this->administrativeDependency = $administrativeDependency;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $individualArray = $this->getIndividualArray();
        if ($this->containsEmptyOrIsNull($individualArray)) {
            $this->message[] = 'Você precisa cadastrar pelo menos um gestor';
            $this->valid = false;
        }

        $roleArray = $this->getRoleArray();
        if ($this->containsEmptyOrIsNull($roleArray)) {
            $this->message[] = 'Você precisa informar o cargo dos gestores';
            $this->valid = false;
        }

        foreach ($this->valueObject as $key => $valueObject) {
            $this->validateAccessCriteria($valueObject);
            $this->validateAccessCriteriaDescription($valueObject);
            $this->validateAccessLinkType($valueObject);
        }

        return $this->valid;
    }

    /**
     * @return array
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param array $array
     * @return bool
     */
    private function containsEmptyOrIsNull($array)
    {
        return empty($array) || in_array(null, $array);
    }

    /**
     * @param SchoolManagerValueObject $valueObject
     */
    private function validateAccessCriteria($valueObject)
    {
        if (!isset($valueObject->roleId)) {
            return;
        }

        if ($valueObject->roleId == SchoolManagerRole::DIRETOR && empty($valueObject->accessCriteriaId)) {
            $this->valid = false;
            $this->message[] = 'Se o cargo do gestor for <b>Diretor</b>, você precisa informar o critério de acesso ao cargo';
        }
    }

    /**
     * @param SchoolManagerValueObject $valueObject
     */
    private function validateAccessCriteriaDescription($valueObject)
    {
        if (!isset($valueObject->accessCriteriaId)) {
            return;
        }

        if ($valueObject->accessCriteriaId == SchoolManagerAccessCriteria::OUTRO && empty($valueObject->accessCriteriaDescription)) {
            $this->valid = false;
            $this->message[] = 'Se o citério de acesso ao cargo do gestor for <b>Outros</b>, você precisa informar uma especificação';
        }
    }

    /**
     * @param SchoolManagerValueObject $valueObject
     */
    private function validateAccessLinkType($valueObject)
    {
        if (!isset($valueObject->roleId)) {
            return;
        }

        if ($this->administrativeDependency == DependenciaAdministrativaEscola::PRIVADA) {
            return;
        }

        if ($valueObject->roleId == SchoolManagerRole::DIRETOR && empty($valueObject->linkTypeId)) {
            $this->valid = false;
            $this->message[] = 'O campo <b>Tipo de vínculo</b> do gestor precisa ser preenchido';
        }
    }

    private function getIndividualArray()
    {
        $individualArray = [];
        foreach ($this->valueObject as $manager) {
            $individualArray[] = $manager->individualId;
        }
        return $individualArray;
    }

    private function getRoleArray()
    {
        $roleArray = [];
        foreach ($this->valueObject as $manager) {
            $roleArray[] = $manager->roleId;
        }
        return $roleArray;
    }
}
