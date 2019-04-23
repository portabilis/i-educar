<?php

namespace iEducar\Support\View;

use iEducar\Modules\Educacenso\Model\LocalFuncionamento;
use App\Models\ManagerAccessCriteria;
use App\Models\ManagerLinkType;
use App\Models\ManagerRole;
use iEducar\Modules\Servidores\Model\FuncaoExercida;
use iEducar\Modules\Servidores\Model\TipoVinculo;
use iEducar\Modules\Transport\Period;
use iEducar\Modules\Educacenso\Model\SituacaoFuncionamento;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\EsferaAdministrativa;
use iEducar\Modules\Educacenso\Model\LocalizacaoDiferenciadaEscola;
use iEducar\Modules\Educacenso\Model\UnidadeVinculadaComOutraInstituicao;

class SelectOptions
{
    /**
     * Retorna a opção default para os selects.
     *
     * @return array
     */
    public static function getDefaultOption()
    {
        return ['' => 'Selecione'];
    }

    /**
     * Retorna as opções disponíveis de turno para o módulo de transporte.
     *
     * @return array
     */
    public static function transportPeriods()
    {
        return self::getDefaultOption() + Period::getDescriptiveValues();
    }

    /**
     * Retorna as opções disponíveis referentes à situação de funcionamento da escola
     *
     * @return array
     */
    public static function situacoesFuncionamentoEscola()
    {
        return self::getDefaultOption() + SituacaoFuncionamento::getDescriptiveValues();
    }

    /**
     * Retorna as opções disponíveis referentes às dependências administrativas da escola
     *
     * @return array
     */
    public static function dependenciasAdministrativasEscola()
    {
        return self::getDefaultOption() + DependenciaAdministrativaEscola::getDescriptiveValues();
    }

    /**
     * Retorna as opções disponíveis referentes às esferas administrativas da escola
     *
     * @return array
     */
    public static function esferasAdministrativasEscola()
    {
        return self::getDefaultOption() + EsferaAdministrativa::getDescriptiveValues();
    }

    /**
     * Retorna as opções disponíveis referentes à localização diferenciada da escola
     *
     * @return array
     */
    public static function localizacoesDiferenciadasEscola()
    {
        return self::getDefaultOption() + LocalizacaoDiferenciadaEscola::getDescriptiveValues();
    }

    /**
     * Retorna as opções disponíveis referentes às instituições quais a escola pode ser vinculada
     *
     * @return array
     */
    public static function unidadesVinculadasEscola()
    {
        return self::getDefaultOption() + UnidadeVinculadaComOutraInstituicao::getDescriptiveValues();
    }

    /**
     * Retorna as opções disponíveis referentes aos locais de funcionamento da escola
     *
     * @return array
     */
    public static function locaisFuncionamentoEscola()
    {
        return self::getDefaultOption() + LocalFuncionamento::getDescriptiveValues();
    }

    /**
     * @return array
     */
    public static function schoolManagerRoles()
    {
        return self::getDefaultOption() + ManagerRole::all()->getKeyValueArray('name');
    }

    /**
     * @return array
     */
    public static function schoolManagerAccessCriterias()
    {
        return self::getDefaultOption() + ManagerAccessCriteria::all()->getKeyValueArray('name');
    }

    /**
     * @return array
     */
    public static function schoolManagerLinkTypes()
    {
        return self::getDefaultOption() + ManagerLinkType::all()->getKeyValueArray('name');
    }

    /**
     * Retorna as opções disponíveis referentes às funções exercidas do servidor
     *
     * @return array
     */
    public static function funcoesExercidaServidor()
    {
        return self::getDefaultOption() + FuncaoExercida::getDescriptiveValues();
    }

    /**
     * Retorna as opções disponíveis referentes aos tipos de vínculo do servidor
     *
     * @return array
     */
    public static function tiposVinculoServidor()
    {
        return self::getDefaultOption() + TipoVinculo::getDescriptiveValues();
    }
}
