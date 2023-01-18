<?php

namespace iEducar\Modules\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\SchoolManagerAccessCriteria;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;
use iEducar\Modules\Educacenso\Model\SituacaoFuncionamento;
use iEducar\Modules\ValueObjects\SchoolManagerValueObject;

class SchoolManagers implements EducacensoValidator
{
    private $message;
    private $administrativeDependency;
    private $valid = true;
    private $operatingSituation;

    /**
     * @var SchoolManagerValueObject[]
     */
    private $valueObject;

    /**
     * @param SchoolManagerValueObject[] $valueObject
     * @param integer                    $administrativeDependency
     * @param integer                    $operatingSituation
     */
    public function __construct($valueObject, $administrativeDependency, $operatingSituation)
    {
        $this->valueObject = $valueObject;
        $this->administrativeDependency = $administrativeDependency;
        $this->operatingSituation = $operatingSituation;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $individualArray = $this->getIndividualArray();
        if ($this->containsEmptyOrIsNull($individualArray)) {
            $this->message[] = 'Informe pelo menos um(a) gestor(a) como gestor(a) principal';
            $this->valid = false;
        }

        $roleArray = $this->getRoleArray();
        if ($this->containsEmptyOrIsNull($roleArray)) {
            $this->message[] = 'O campo: <b>Cargo do gestor</b> deve ser preenchido';
            $this->valid = false;
        }

        foreach ($this->valueObject as $key => $valueObject) {
            $this->validateAccessCriteria($valueObject);
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
     *
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
        if (empty($valueObject->roleId)) {
            return;
        }

        if ($valueObject->roleId == SchoolManagerRole::DIRETOR && empty($valueObject->accessCriteriaId) && $this->operatingSituation == SituacaoFuncionamento::EM_ATIVIDADE) {
            $this->valid = false;
            $this->message[] = 'O campo: <b>Critério de acesso ao cargo</b> deve ser preenchido quando o campo: <b>Cargo</b> for: <b>Diretor</b> e o campo: <b>Situação de funcionamento</b> for: <b>Em atividade</b>';
        }

        $publicDependency = [
            DependenciaAdministrativaEscola::FEDERAL,
            DependenciaAdministrativaEscola::ESTADUAL,
            DependenciaAdministrativaEscola::MUNICIPAL,
        ];

        if ($valueObject->accessCriteriaId == SchoolManagerAccessCriteria::PROPRIETARIO && in_array($this->administrativeDependency, $publicDependency)) {
            $this->valid = false;
            $this->message[] = 'Não é possível selecionar a opção: <b>Ser proprietário(a) ou sócio(a)-proprietário(a) da escola</b> quando a dependência administrativa for: <b>Federal, Estadual ou Municipal</b>.';
        }

        $publicAccesCriteria = [
            SchoolManagerAccessCriteria::CONCURSO,
            SchoolManagerAccessCriteria::PROCESSO_ELEITORAL_COMUNIDADE,
            SchoolManagerAccessCriteria::PROCESSO_SELETIVO_COMUNIDADE,
        ];

        $accessCriteriaSelected = SchoolManagerAccessCriteria::getDescriptiveValues()[$valueObject->accessCriteriaId];

        if (in_array($valueObject->accessCriteriaId, $publicAccesCriteria) && $this->administrativeDependency == DependenciaAdministrativaEscola::PRIVADA) {
            $this->valid = false;
            $this->message[] = "Não é possível selecionar a opção: <b>{$accessCriteriaSelected}</b> quando a dependência administrativa da escola for: <b>Privada</b>";
        }
    }

    /**
     * @param SchoolManagerValueObject $valueObject
     */
    private function validateAccessLinkType($valueObject)
    {
        if (empty($valueObject->roleId)) {
            return;
        }

        if ($this->administrativeDependency == DependenciaAdministrativaEscola::PRIVADA) {
            return;
        }

        if ((int) $valueObject->roleId === SchoolManagerRole::DIRETOR &&
            empty($valueObject->linkTypeId) &&
            $this->operatingSituation === SituacaoFuncionamento::EM_ATIVIDADE
        ) {
            $this->valid = false;
            $this->message[] = 'O campo: <b>Tipo de vínculo</b> deve ser preenchido quando o campo: <b>Situação de funcionamento</b> for: <b>Em atividade<b>, o campo <b>Cargo</b> for: <b>Diretor</b> e o campo: <b>Dependência administrativa</b> não for: <b>Privada</b>';
        }
    }

    private function getIndividualArray()
    {
        $individualArray = [];
        foreach ($this->valueObject as $manager) {
            $individualArray[] = $manager->employeeId;
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
