<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro10 as Registro10Model;
use iEducar\Modules\Educacenso\Formatters;
use Portabilis_Date_Utils;
use Portabilis_Utils_Database;

class Registro10 extends AbstractRegistro
{
    use Formatters;

    /**
     * @var Registro10Model
     */
    protected $model;

    /**
     * @param $escola
     * @return Registro10Model
     */
    public function getData($escola)
    {
        $data = $this->processData($this->repository->getDataForRecord10($escola)[0]);
        $this->hydrateModel($data);

        return $this->model;
    }

    private function processData($data)
    {
        $data->localFuncionamento = Portabilis_Utils_Database::pgArrayToArray($data->localFuncionamento);
        $data->tratamentoLixo = Portabilis_Utils_Database::pgArrayToArray($data->tratamentoLixo);
        $data->recursosAcessibilidade = Portabilis_Utils_Database::pgArrayToArray($data->recursosAcessibilidade);
        $data->usoInternet = Portabilis_Utils_Database::pgArrayToArray($data->usoInternet);
        $data->equipamentos_acesso_internet = Portabilis_Utils_Database::pgArrayToArray($data->equipamentos_acesso_internet);
        $data->equipamentos = Portabilis_Utils_Database::pgArrayToArray($data->equipamentos);
        $data->redeLocal = Portabilis_Utils_Database::pgArrayToArray($data->redeLocal);
        $data->orgaosColegiados = Portabilis_Utils_Database::pgArrayToArray($data->orgaosColegiados);
        $data->reservaVagasCotas = Portabilis_Utils_Database::pgArrayToArray($data->reservaVagasCotas);
        $data->nomeEscola = strtoupper($data->nomeEscola);

        return $data;
    }
}
