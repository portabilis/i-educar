<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro00 as Registro00Model;
use iEducar\Modules\Educacenso\ExportRule\DependenciaAdministrativa;
use iEducar\Modules\Educacenso\ExportRule\EsferaAdministrativa;
use iEducar\Modules\Educacenso\ExportRule\PoderPublicoConveniado as ExportRulePoderPublicoConveniado;
use iEducar\Modules\Educacenso\ExportRule\Regulamentacao;
use iEducar\Modules\Educacenso\ExportRule\SituacaoFuncionamento;
use iEducar\Modules\Educacenso\Formatters;
use iEducar\Modules\Educacenso\Model\FormasContratacaoPoderPublico;
use iEducar\Modules\Educacenso\Model\PoderPublicoConveniado;
use Portabilis_Date_Utils;
use Portabilis_Utils_Database;

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

        return $models[0];
    }

    public function getExportFormatData($escola, $year)
    {
        $data = $this->getData($escola, $year);
        $data = $this->getRecordExportData($data);

        return $data;
    }

    /**
     * @param $Registro00Model
     * @return array
     */
    public function getRecordExportData($record)
    {
        $this->codigoInep = $record->codigoInep;
        $this->nomeEscola = $record->nome;
        $this->situacaoFuncionamento = $record->situacaoFuncionamento;

        $record = SituacaoFuncionamento::handle($record);
        $record = ExportRulePoderPublicoConveniado::handle($record);
        $record = DependenciaAdministrativa::handle($record);
        $record = Regulamentacao::handle($record);
        $record = EsferaAdministrativa::handle($record);

        return [
            $record->registro, // 1	Tipo de registro
            $record->codigoInep, // 2	Código de escola - Inep
            $record->situacaoFuncionamento, // 3	Situação de funcionamento
            $record->inicioAnoLetivo, // 4	Data de início do ano letivo
            $record->fimAnoLetivo, // 5	Data de término do ano letivo
            $record->nome, // 6	Nome da escola
            $record->cep, // 7	CEP
            $record->codigoIbgeMunicipio, // 8	Município
            $record->codigoIbgeDistrito, // 9	Distrito
            $record->logradouro, // 10	Endereço
            $record->numero, // 11	Número
            $record->complemento, // 12	Complemento
            $record->bairro, // 13	Bairro
            $record->ddd, // 14	DDD
            $record->telefone, // 15	Telefone
            $record->telefoneOutro, // 16	Outro telefone de contato
            $record->email, // 17	Endereço eletrônico (e-mail) da escola
            $record->orgaoRegional, // 18	Código do órgão regional de ensino
            $record->zonaLocalizacao, // 19	Localização/Zona da escola
            $record->localizacaoDiferenciada, // 20	Localização diferenciada da escola
            $record->dependenciaAdministrativa, // 21	Dependência administrativa
            $record->orgaoEducacao, // 22	Secretaria de Educação/Ministério da Educação
            $record->orgaoSeguranca, // 23	Secretaria de Segurança Pública/Forças Armadas/Militar
            $record->orgaoSaude, // 24	Secretaria da Saúde/Ministério da Saúde
            $record->orgaoOutro, // 25	Outro órgão da administração pública
            $record->mantenedoraEmpresa, // 26	Empresa, grupos empresariais do setor privado ou pessoa física
            $record->mantenedoraSindicato, // 27	Sindicatos de trabalhadores ou patronais, associações, cooperativas
            $record->mantenedoraOng, // 28	Organização não governamental (ONG) - nacional ou internacional
            $record->mantenedoraInstituicoes, // 29	Instituição sem fins lucrativos
            $record->mantenedoraSistemaS, // 30	Sistema S (Sesi, Senai, Sesc, outros)
            $record->mantenedoraOscip, // 31	Organização da Sociedade Civil de Interesse Público (Oscip)
            $record->categoriaEscolaPrivada, // 32	Categoria da escola privada
            $record->poderPublicoConveniado ? (int) in_array(PoderPublicoConveniado::ESTADUAL, $record->poderPublicoConveniado) : '', // 33	Secretaria estadual
            $record->poderPublicoConveniado ? (int) in_array(PoderPublicoConveniado::MUNICIPAL, $record->poderPublicoConveniado) : '', // 34	Secretaria Municipal
            $record->poderPublicoConveniado ? (int) in_array(PoderPublicoConveniado::NAO_POSSUI, $record->poderPublicoConveniado) : '', // 35	Não possui parceria ou convênio
            $record->formasContratacaoPoderPublicoEstadual ? (int) in_array(FormasContratacaoPoderPublico::TERMO_COLABORACAO, $record->formasContratacaoPoderPublicoEstadual) : '', // 36	Termo de colaboração (Lei nº 13.019/2014)
            $record->formasContratacaoPoderPublicoEstadual ? (int) in_array(FormasContratacaoPoderPublico::TERMO_FOMENTO, $record->formasContratacaoPoderPublicoEstadual) : '', // 37	Termo de fomento (Lei nº 13.019/2014)
            $record->formasContratacaoPoderPublicoEstadual ? (int) in_array(FormasContratacaoPoderPublico::ACORDO_COOPERACAO, $record->formasContratacaoPoderPublicoEstadual) : '', // 38	Acordo de cooperação (Lei nº 13.019/2014)
            $record->formasContratacaoPoderPublicoEstadual ? (int) in_array(FormasContratacaoPoderPublico::CONTRATO_PRESTACAO_SERVICO, $record->formasContratacaoPoderPublicoEstadual) : '', // 39	Contrato de prestação de serviço
            $record->formasContratacaoPoderPublicoEstadual ? (int) in_array(FormasContratacaoPoderPublico::TERMO_COOPERACAO_TECNICA, $record->formasContratacaoPoderPublicoEstadual) : '', // 40	Termo de cooperação técnica e financeira
            $record->formasContratacaoPoderPublicoEstadual ? (int) in_array(FormasContratacaoPoderPublico::CONTRATO_CONSORCIO, $record->formasContratacaoPoderPublicoEstadual) : '', // 41	Contrato de consórcio público/Convênio de cooperação
            $record->formasContratacaoPoderPublicoMunicipal ? (int) in_array(FormasContratacaoPoderPublico::TERMO_COLABORACAO, $record->formasContratacaoPoderPublicoMunicipal) : '', // 42	Termo de colaboração (Lei nº 13.019/2014)
            $record->formasContratacaoPoderPublicoMunicipal ? (int) in_array(FormasContratacaoPoderPublico::TERMO_FOMENTO, $record->formasContratacaoPoderPublicoMunicipal) : '', // 43	Termo de fomento (Lei nº 13.019/2014)
            $record->formasContratacaoPoderPublicoMunicipal ? (int) in_array(FormasContratacaoPoderPublico::ACORDO_COOPERACAO, $record->formasContratacaoPoderPublicoMunicipal) : '', // 44	Acordo de cooperação (Lei nº 13.019/2014)
            $record->formasContratacaoPoderPublicoMunicipal ? (int) in_array(FormasContratacaoPoderPublico::CONTRATO_PRESTACAO_SERVICO, $record->formasContratacaoPoderPublicoMunicipal) : '', // 45 Contrato de prestação de serviço
            $record->formasContratacaoPoderPublicoMunicipal ? (int) in_array(FormasContratacaoPoderPublico::TERMO_COOPERACAO_TECNICA, $record->formasContratacaoPoderPublicoMunicipal) : '', // 46 Termo de cooperação técnica e financeira
            $record->formasContratacaoPoderPublicoMunicipal ? (int) in_array(FormasContratacaoPoderPublico::CONTRATO_CONSORCIO, $record->formasContratacaoPoderPublicoMunicipal) : '', // 47 Contrato de consórcio público/Convênio de cooperação
            $record->cnpjMantenedoraPrincipal, // 48	CNPJ da mantenedora principal da escola privada
            $record->cnpjEscolaPrivada, // 49	Número do CNPJ da escola privada
            $record->regulamentacao, // 50	Regulamentação/autorização no conselho ou órgão municipal, estadual ou federal de educaçãof
            $record->esferaFederal, // 51	Federal
            $record->esferaEstadual, // 52	Estadual
            $record->esferaMunicipal, // 53	Municipal
            $record->unidadeVinculada, // 54	Unidade vinculada à escola de educação básica ou unidade ofertante de educação superior
            $record->inepEscolaSede, // 55	Código da Escola Sede
            $record->codigoIes, // 56	Código da IES
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
        $data->poderPublicoConveniado = Portabilis_Utils_Database::pgArrayToArray($data->poderPublicoConveniado);
        $data->formasContratacaoPoderPublicoEstadual = Portabilis_Utils_Database::pgArrayToArray($data->formasContratacaoPoderPublicoEstadual);
        $data->formasContratacaoPoderPublicoMunicipal = Portabilis_Utils_Database::pgArrayToArray($data->formasContratacaoPoderPublicoMunicipal);

        return $data;
    }

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
