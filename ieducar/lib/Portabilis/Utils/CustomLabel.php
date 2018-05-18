<?php

require_once 'include/pmieducar/clsPmieducarConfiguracoesGerais.inc.php';

class CustomLabel
{

    static protected $instance;

    protected $defaults;
    protected $custom;

    public function __construct($defaultsPath)
    {
        $raw = @file_get_contents($defaultsPath);

        if ($raw === false) {
            throw new Exception('Não foi possível encontrar o arquivo de chaves padrão no caminho "' . $defaultsPath . '"');
        }

        $this->defaults = json_decode($raw, true);
        $this->custom = $this->queryCustom();
    }

    public function customize($key)
    {
        if (!empty($this->custom[$key])) {
            return $this->custom[$key];
        }

        if (!empty($this->defaults[$key])) {
            return $this->defaults[$key];
        }

        return $key;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function getCustom()
    {
        return $this->custom;
    }

    protected function queryCustom()
    {
        $configs = new clsPmieducarConfiguracoesGerais();
        $detalhe = $configs->detalhe();

        return $detalhe['custom_labels'];
    }

    public static function getInstance($path)
    {
        if (is_null(self::$instance)) {
            self::$instance = new CustomLabel($path);
        }

        return self::$instance;
    }
}

function _cl($key)
{
    $path = PROJECT_ROOT . DS . 'configuration' . DS . 'custom_labels.json';

    return CustomLabel::getInstance($path)->customize($key);
}
