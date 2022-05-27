<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro00 as Registro00Model;
use iEducar\Modules\Educacenso\ExportRule\DependenciaAdministrativa;
use iEducar\Modules\Educacenso\ExportRule\EsferaAdministrativa;
use iEducar\Modules\Educacenso\ExportRule\Regulamentacao;
use iEducar\Modules\Educacenso\ExportRule\SituacaoFuncionamento;
use iEducar\Modules\Educacenso\Formatters;
use Portabilis_Date_Utils;

class Registro00 extends AbstractRegistro
{
    use Formatters;

    /**
     * @var Registro00Model
     */
    protected $model;

    public $codigoInep;
    public $nomeEscola;
    public $situacaoFuncionamento;

    /**
     * @param $escola
     * @param $ano
     *
     * @return Registro00Model
     */
    public function getData($school, $year)
    {
        $data = $this->repository->getDataForRecord00($school, $year);

        $models = [];
        foreach ($data as $record) {
            $record = $this->processData($record);
            $models[] = $this->hydrateModel($record);
        }

        return $models;
    }

    /**
     * @param $escola
     * @param $year
     *
     * @return array
     */
    public function getExportFormatData($escola, $year)
    {
        $records = $this->getData($escola, $year);

        $data = [];

        foreach ($records as $record) {
            $data[] = $this->getRecordExportData($record);
        }

        return $data;
    }

    /**
     * @param $Registro00Model
     *
     * @return array
     */
    public function getRecordExportData($record)
    {
        $this->codigoInep = $record->codigoInep;
        $this->nomeEscola = $record->nome;
        $this->situacaoFuncionamento = $record->situacaoFuncionamento;

        $record = SituacaoFuncionamento::handle($record);
        $record = DependenciaAdministrativa::handle($record);
        $record = Regulamentacao::handle($record);
        $record = EsferaAdministrativa::handle($record);

        return [
            $record->registro,
            $record->codigoInep,
            $record->situacaoFuncionamento,
            $record->inicioAnoLetivo,
            $record->fimAnoLetivo,
            $record->nome,
            $record->cep,
            $record->codigoIbgeMunicipio,
            $record->codigoIbgeDistrito,
            $record->logradouro,
            $record->numero,
            $record->complemento,
            $record->bairro,
            $record->ddd,
            $record->telefone,
            $record->telefoneOutro,
            $record->email,
            $record->orgaoRegional,
            $record->zonaLocalizacao,
            $record->localizacaoDiferenciada,
            $record->dependenciaAdministrativa,
            $record->orgaoEducacao,
            $record->orgaoSeguranca,
            $record->orgaoSaude,
            $record->orgaoOutro,
            $record->mantenedoraEmpresa,
            $record->mantenedoraSindicato,
            $record->mantenedoraOng,
            $record->mantenedoraInstituicoes,
            $record->mantenedoraSistemaS,
            $record->mantenedoraOscip,
            $record->categoriaEscolaPrivada,
            $record->conveniadaPoderPublico,
            $record->cnpjMantenedoraPrincipal,
            $record->cnpjEscolaPrivada,
            $record->regulamentacao,
            $record->esferaFederal,
            $record->esferaEstadual,
            $record->esferaMunicipal,
            $record->unidadeVinculada,
            $record->inepEscolaSede,
            $record->codigoIes,
        ];
    }

    private function processData($data)
    {
        $data->codigoInep = substr($data->codigoInep, 0, 8);
        $data->inicioAnoLetivo = Portabilis_Date_Utils::pgSQLToBr($data->inicioAnoLetivo);
        $data->fimAnoLetivo = Portabilis_Date_Utils::pgSQLToBr($data->fimAnoLetivo);
        $data->nome = $this->convertStringToCenso($data->nome);
        $data->logradouro = $this->convertStringToCenso($data->logradouro);
        $data->numero = $this->convertStringToCenso($data->numero);
        $data->complemento = $this->convertStringToCenso($data->complemento);
        $data->bairro = $this->convertStringToCenso($data->bairro);
        $data->email = mb_strtoupper($data->email);
        $data->orgaoRegional = ($data->orgaoRegional ? str_pad($data->orgaoRegional, 5, '0', STR_PAD_LEFT) : null);
        $data->cnpjEscolaPrivada = $this->cnpjToCenso($data->cnpjEscolaPrivada);
        $data->cnpjMantenedoraPrincipal = $this->cnpjToCenso($data->cnpjMantenedoraPrincipal);

        return $data;
    }

    /**
     * @param $data
     */
    protected function hydrateModel($data)
    {
        $model = clone $this->model;
        foreach ($data as $field => $value) {
            if (property_exists($model, $field)) {
                $model->$field = $value;
            }
        }

        return $model;
    }
}
