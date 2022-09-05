<?php

namespace App\Services\Educacenso\Version2022;

use App\Models\Educacenso\Registro10;
use App\Models\Educacenso\RegistroEducacenso;
use App\Services\Educacenso\Version2020\Registro10Import as Registro10Import2020;
use App\Services\Educacenso\Version2022\Models\Registro10Model;
use iEducar\Modules\Educacenso\Model\Equipamentos;
use iEducar\Modules\Educacenso\Model\InstrumentosPedagogicos;
use iEducar\Modules\Educacenso\Model\Laboratorios;
use iEducar\Modules\Educacenso\Model\SalasAtividades;

class Registro10Import extends Registro10Import2020
{
    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param RegistroEducacenso $model
     * @param int                $year
     * @param                    $user
     *
     * @return void
     */
    public function import(RegistroEducacenso $model, $year, $user)
    {
        parent::import($model, $year, $user);

        $schoolInep = parent::getSchool();

        if (empty($schoolInep)) {
            return;
        }

        /** @var LegacySchool $school */
        $school = $schoolInep->school;
        $model = $this->model;

        $school->nao_ha_funcionarios_para_funcoes = (bool) $model->semFuncionariosParaFuncoes;

        $school->save();
    }

    /**
     * @param $arrayColumns
     *
     * @return Registro10|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro10Model();
        $registro->hydrateModel($arrayColumns);

        return $registro;
    }

    protected function getArrayLaboratorios()
    {
        $laboratorios = parent::getArrayLaboratorios();
        $arrayLaboratorios = transformStringFromDBInArray($laboratorios) ?: [];

        if ($this->model->dependenciaLaboratorioEducacaoProfissional) {
            $arrayLaboratorios[] = Laboratorios::EDUCACAO_PROFISSIONAL;
        }

        return parent::getPostgresIntegerArray($arrayLaboratorios);
    }

    protected function getArraySalasAtividades()
    {
        $salasAtividades = parent::getArraySalasAtividades();
        $arraySalas = transformStringFromDBInArray($salasAtividades) ?: [];

        if ($this->model->dependenciaSalaEducacaoProfissional) {
            $arraySalas[] = SalasAtividades::EDUCACAO_PROFISSIONAL;
        }

        return parent::getPostgresIntegerArray($arraySalas);
    }

    protected function getArrayEquipamentos()
    {
        $equipamentos = parent::getArraySalasAtividades();
        $arrayEquipamentos = transformStringFromDBInArray($equipamentos) ?: [];

        if ($this->model->equipamentosNenhum) {
            $arrayEquipamentos[] = Equipamentos::NENHUM_EQUIPAMENTO_LISTADO;
        }

        return parent::getPostgresIntegerArray($arrayEquipamentos);
    }

    protected function getArrayInstrumentosPedagogicos()
    {
        $instrumentos = parent::getArraySalasAtividades();
        $arrayInstrumentos = transformStringFromDBInArray($instrumentos) ?: [];

        if ($this->model->instrumentosPedagogicosEducacaoProfissional) {
            $arrayInstrumentos[] = InstrumentosPedagogicos::MATERIAL_EDUCACAO_PROFISSIONAL;
        }

        if ($this->model->instrumentosPedagogicosNenhum) {
            $arrayInstrumentos[] = InstrumentosPedagogicos::NENHUM_DOS_INSTRUMENTOS_LISTADOS;
        }

        return parent::getPostgresIntegerArray($arrayInstrumentos);
    }
}
