<?php

use iEducar\Reports\BaseModifier;

abstract class Portabilis_Report_ReportCore
{
    /**
     * @var array
     */
    public $requiredArgs;

    /**
     * @var array
     */
    public $args;

    /**
     * @var array
     */
    public $modifiers = [];

    /**
     * Portabilis_Report_ReportCore constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->requiredArgs = [];
        $this->args = [];

        $this->requiredArgs();
    }

    /**
     * Wrapper para Portabilis_Array_Utils::merge.
     *
     * @see Portabilis_Array_Utils::merge()
     *
     * @param array $options
     * @param array $defaultOptions
     *
     * @return array
     */
    protected static function mergeOptions($options, $defaultOptions)
    {
        return Portabilis_Array_Utils::merge($options, $defaultOptions);
    }

    /**
     * Adiciona um parâmetro para ser passado ao renderizador.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function addArg($name, $value)
    {
        if ((string) $name === '') {
            return false;
        }

        $this->args[$name] = $value;
    }

    /**
     * Adiciona o nome de um parâmetro obrigatório.
     *
     * @param string $name
     *
     * @return void
     */
    public function addRequiredArg($name)
    {
        $this->requiredArgs[] = $name;
    }

    /**
     * Valida a existência de todos os parâmetros obrigatórios.
     *
     * @return void
     *
     * @throws Exception
     */
    public function validatesPresenseOfRequiredArgs()
    {
        foreach ($this->requiredArgs as $requiredArg) {
            if (!isset($this->args[$requiredArg]) || empty($this->args[$requiredArg])) {
                throw new Exception("The required arg '{$requiredArg}' wasn't set or is empty!");
            }
        }
    }

    /**
     * Renderiza o relatório.
     *
     * @param array $options
     *
     * @return mixed
     *
     * @throws CoreExt_Exception
     * @throws Exception
     */
    public function dumps($options = [])
    {
        $options = self::mergeOptions($options, [
            'report_factory' => null,
            'options' => []
        ]);

        $this->validatesPresenseOfRequiredArgs();

        $reportFactory = is_null($options['report_factory']) ? $this->reportFactory() : $options['report_factory'];

        return $reportFactory->dumps($this, $options['options']);
    }

    /**
     * Retorna uma fábrica de relatórios.
     *
     * @return Portabilis_Report_ReportFactory
     *
     * @throws CoreExt_Exception
     */
    public function reportFactory()
    {
        $factoryClassName = config('legacy.report.default_factory');
        $factoryClassPath = str_replace('_', '/', $factoryClassName) . '.php';

        if (!$factoryClassName) {
            throw new CoreExt_Exception('No report.default_factory defined in configurations!');
        }

        include_once $factoryClassPath;

        if (!class_exists($factoryClassName)) {
            throw new CoreExt_Exception("Class '$factoryClassName' not found in path '$factoryClassPath'");
        }

        return new $factoryClassName();
    }

    /**
     * Retorna o nome do template (arquivo .jrxml) que será utilizado como
     * template para a renderização.
     *
     * @return string
     */
    abstract public function templateName();

    /**
     * Adiciona os parâmetros obrigatórios a serem passados ao renderizador.
     *
     * @return void
     */
    abstract public function requiredArgs();

    /**
     * Indica se JSON será utilizado como fonte de dados para o relatório.
     *
     * @return bool
     */
    public function useJson()
    {
        return false;
    }

    /**
     * Retorna a query onde será encontrado os dados para o relatório
     * principal.
     *
     * @return string
     */
    public function getJsonQuery()
    {
        return '';
    }

    /**
     * Array com os dados que serão convertidos em JSON e enviados ao relatório
     * como fonte de dados.
     *
     * @return array
     */
    public function getJsonData()
    {
        return [];
    }

    /**
     * Realiza modificações nos dados que serão utilizados para a geração de um
     * relatório. Útil quando é necessário manipular os dados de uma query base
     * para gerar novos campos, formatações, etc.
     *
     * @param array $data
     *
     * @return array
     */
    public function modify($data)
    {
        foreach ($this->modifiers as $modifier) {
            if (!is_subclass_of($modifier, BaseModifier::class)) {
                continue;
            }

            $modifier = new $modifier($this->templateName(), $this->args);
            $data = $modifier->modify($data);
        }

        return $data;
    }
}
