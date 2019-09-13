<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\ItemOfRegistro30;
use App\Models\Educacenso\Registro30 as Registro30Model;
use App\Repositories\EducacensoRepository;
use iEducar\Modules\Educacenso\Formatters;
use Portabilis_Utils_Database;

class Registro30 extends AbstractRegistro
{
    use Formatters;

    /**
     * @var EducacensoRepository
     */
    protected $repository;

    /**
     * @var Registro30Model
     */
    protected $model;

    /**
     * @var Registro30Model[]
     */
    protected $modelArray;

    /**
     * @param $schoolId
     * @return Registro30Model[]
     */
    public function getData($schoolId)
    {
        $arrayPersonId = $this->getArrayPersonId();
        $commonData = $this->repository->getCommonDataForRecord30($arrayPersonId, $schoolId);
        foreach ($commonData as $data) {
            $this->model = $this->modelArray[$data->codigoPessoa];
            $this->hydrateModel($data);
            $this->modelArray[$data->codigoPessoa] = $this->model;
        }

        $arrayEmployeeId = $this->getArrayEmployeeId();
        $employeeData = $this->repository->getEmployeeDataForRecord30($arrayEmployeeId);
        foreach ($employeeData as $data) {
            $data->email = strtoupper($data->email);
            $this->model = $this->modelArray[$data->codigoPessoa];
            $this->hydrateModel($data);
            $this->modelArray[$data->codigoPessoa] = $this->model;
        }

        $arrayStudentId = $this->getArrayStudentId();
        $studentData = $this->repository->getStudentDataForRecord30($arrayStudentId);
        foreach ($studentData as $data) {
            $this->model = $this->modelArray[$data->codigoPessoa];
            $this->hydrateModel($data);
            $this->modelArray[$data->codigoPessoa] = $this->model;
        }

        foreach ($this->modelArray as &$record) {
            $record->formacaoAnoConclusao = Portabilis_Utils_Database::pgArrayToArray($record->formacaoAnoConclusao);
            $record->formacaoCurso = Portabilis_Utils_Database::pgArrayToArray($record->formacaoCurso);
            $record->formacaoInstituicao = Portabilis_Utils_Database::pgArrayToArray($record->formacaoInstituicao);
            $record->formacaoComponenteCurricular = Portabilis_Utils_Database::pgArrayToArray($record->formacaoComponenteCurricular);
        }

        return $this->modelArray;
    }

    /**
     * @param ItemOfRegistro30[] $array
     * @param string $type
     */
    public function setArrayDataByType($array, $type)
    {
        foreach ($array as $model) {
            if (!($model instanceof ItemOfRegistro30)) {
                continue;
            }

            $registro30Model = new Registro30Model();

            if (isset($this->modelArray[$model->getCodigoPessoa()])) {
                $registro30Model = $this->modelArray[$model->getCodigoPessoa()];
            }

            $registro30Model->codigoPessoa = $model->getCodigoPessoa();
            $registro30Model->codigoAluno = $model->getCodigoAluno();
            $registro30Model->codigoServidor = $model->getCodigoServidor();

            if ($type == Registro30Model::TIPO_STUDENT) {
                $registro30Model->dadosAluno = $model;
            }

            $registro30Model->tipos[$type] = true;
            $this->modelArray[$model->getCodigoPessoa()] = $registro30Model;
        }
    }

    /**
     * @param $escolaId
     * @return Registro30Model[]
     * @throws \Exception
     */
    public function getExportFormatData($escolaId)
    {
        $modelArray = $this->getData($escolaId);

        foreach ($modelArray as &$registro30) {
            $registro30->nomePessoa = $this->convertStringToCenso($registro30->nomePessoa);
            $registro30->filiacao1 = $this->convertStringToCenso($registro30->filiacao1);
            $registro30->filiacao2 = $this->convertStringToCenso($registro30->filiacao2);
            $registro30->dataNascimento = (new \DateTime($registro30->dataNascimento))->format('d/m/Y');
            $registro30->cpf = $this->cpfToCenso($registro30->cpf);
        }

        return $modelArray;
    }

    private function getArrayPersonId()
    {
        if (empty($this->modelArray)) {
            return [];
        }

        $arrayId = [];
        foreach ($this->modelArray as $model) {
            $arrayId[] = $model->codigoPessoa;
        }

        return $arrayId;
    }

    private function getArrayEmployeeId()
    {
        if (empty($this->modelArray)) {
            return [];
        }

        $arrayId = [];
        foreach ($this->modelArray as $model) {
            if (empty($model->codigoServidor)) {
                continue;
            }

            $arrayId[] = $model->codigoServidor;
        }

        return $arrayId;
    }

    private function getArrayStudentId()
    {
        if (empty($this->modelArray)) {
            return [];
        }

        $arrayId = [];
        foreach ($this->modelArray as $model) {
            if (empty($model->codigoAluno)) {
                continue;
            }

            $arrayId[] = $model->codigoAluno;
        }

        return $arrayId;
    }
}
