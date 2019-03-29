<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro00 as Registro00Model;
use iEducar\Modules\Educacenso\Formatters;
use Portabilis_Date_Utils;

class Registro00 extends AbstractRegistro
{
    use Formatters;

    /**
     * @var Registro00Model
     */
    protected $model;

    /**
     * @param $escola
     * @param $ano
     * @return Registro00Model
     */
    public function getData($escola, $ano)
    {
        $data = $this->repository->getDataForRecord00($escola, $ano);

        $this->hydrateModel($data[0]);

        return $this->model;
    }

    public function getExportFormatData($escola, $ano)
    {
        $model = $this->getData($escola, $ano);

        $this->model->codigoInep = substr($this->model->codigoInep, 0, 8);
        $this->model->inicioAnoLetivo = Portabilis_Date_Utils::pgSQLToBr($this->model->inicioAnoLetivo);
        $this->model->fimAnoLetivo = Portabilis_Date_Utils::pgSQLToBr($this->model->fimAnoLetivo);
        $this->model->nome = $this->convertStringToCenso($this->model->nome);
        $this->model->logradouro = $this->convertStringToCenso($this->model->logradouro);
        $this->model->numero = $this->convertStringToCenso($this->model->numero);
        $this->model->complemento = $this->convertStringToCenso($this->model->complemento);
        $this->model->bairro = $this->convertStringToCenso($this->model->bairro);
        $this->model->email = strtoupper($this->model->email);
        $this->model->orgaoRegional = ($this->model->orgaoRegional ? str_pad($this->model->orgaoRegional, 5, "0", STR_PAD_LEFT) : null);
        $this->model->cnpjEscolaPrivada = $this->cnpjToCenso($this->model->cnpjEscolaPrivada);
        $this->model->cnpjMantenedoraPrincipal = $this->cnpjToCenso($this->model->cnpjMantenedoraPrincipal);
        
        return $model;
    }
}
