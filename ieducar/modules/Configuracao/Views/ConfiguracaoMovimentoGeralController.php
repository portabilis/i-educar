<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'Configuracao/Model/ConfiguracaoMovimentoGeralDataMapper.php';

class clsIndexBase extends clsBase
{

    function Formular() {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Configuração movimento geral');
        $this->processoAp = 9998867;
    }
}

class ConfiguracaoMovimentoGeralController extends clsCadastro
{
    private $configDataMapper;
    protected $_formMap    = array(
        'serie-0' => array(
            'label' => 'Educação infantil',
            'coluna'=> 0,
            'value' => array(),
            'help'  => ''
        ),
        'serie-1' => array(
            'label' => '1° ano',
            'coluna'=> 1,
            'value' => array()
        ),
        'serie-2' => array(
            'label' => '2° ano',
            'coluna'=> 2,
            'value' => array(),
            'help'  => ''
        ),
        'serie-3' => array(
            'label' => '3° ano',
            'coluna'=> 3,
            'value' => array(),
            'help'  => ''
        ),
        'serie-4' => array(
            'label' => '4° ano',
            'coluna'=> 4,
            'value' => array(),
            'help'  => ''
        ),
        'serie-5' => array(
            'label' => '5° ano',
            'coluna'=> 5,
            'value' => array(),
            'help'  => ''
        ),
        'serie-6' => array(
            'label' => '6° ano',
            'coluna'=> 6,
            'value' => array(),
            'help'  => ''
        ),
        'serie-7' => array(
            'label' => '7° ano',
            'coluna'=> 7,
            'value' => array(),
            'help'  => ''
        ),
        'serie-8' => array(
            'label' => '8° ano',
            'coluna'=> 8,
            'value' => array(),
            'help'  => ''
        ),
        'serie-9' => array(
            'label' => '9° ano',
            'coluna'=> 9,
            'value' => array(),
            'help'  => ''
        )
    );

    public $_titulo = 'Configuração movimento geral';
    public $_processoAp = 9998867;

    function Inicializar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(9998867, $this->pessoa_logada, 1, 'educar_index.php');
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
            $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
            "educar_configuracoes_index.php"    => "Configurações",
            ""                                  => "Configuração movimento geral"
        ));

        $this->enviaLocalizacao($localizacao->montar());
        return 'Editar';
    }

    public function Gerar() {
        $this->loadConfig();
        foreach ($this->_formMap as $key => $value){
            $this->inputsHelper()->multipleSearchSerie($key, array('label' => $value['label'], 'required' => false, 'values' => $value['value'], 'coluna' => $value['coluna']),'');
        }
    }

    function loadConfig() {
        $this->configDataMapper = new ConfiguracaoMovimentoGeralDataMapper();
        foreach ($this->configDataMapper->findAll() as $config){
            $config;
            $series = $this->_formMap['serie-'.$config->get('coluna')]['value'];

            $series[] = $config->get('serie');

            $this->_formMap['serie-'.$config->get('coluna')]['value'] = $series;
        }
    }

    function Editar()
    {

        $this->configDataMapper = new ConfiguracaoMovimentoGeralDataMapper();
        $salvou = true;
        $this->deleteAllConfigs();
        foreach ($_POST as $key => $value){
            if (strpos($key,'multiple_search_serie_serie-') === 0) {
                $series = $value;
                $coluna = str_replace('multiple_search_serie_serie-', '', $key);
                foreach ($series as $serie) {
                    if (!empty($serie)){
                        $this->configDataMapper->save($this->configDataMapper->createNewEntityInstance(array('coluna' => $coluna, 'serie' => $serie)));
                    }
                }
            }
        }
        $this->mensagem .= "Edição efetuada com sucesso.<br>";
        return $salvou;
    }

    function deleteAllConfigs() {
        $this->configDataMapper = new ConfiguracaoMovimentoGeralDataMapper();
        foreach ($this->configDataMapper->findAll() as $config){
            $this->configDataMapper->delete($config);
        }
    }

}
