<?php

require_once 'include/clsCadastro.inc.php';
require_once "include/clsBanco.inc.php";
require_once 'Portabilis/Controller/Page/EditController.php';
require_once 'Configuracao/Model/ConfiguracaoMovimentoGeralDataMapper.php';
require_once 'lib/Portabilis/View/Helper/Application.php';
require_once 'lib/Portabilis/View/Helper/Inputs.php';

class ConfiguracaoMovimentoGeralController extends Portabilis_Controller_Page_EditController
{
    protected $_dataMapper        = 'ConfiguracaoMovimentoGeralDataMapper';
    protected $_titulo            = 'Configuração movimento mensal';
    protected $_processoAp        = 9998866;
    protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
    protected $_saveOption        = TRUE;
    protected $_deleteOption      = FALSE;
    protected $_formMap    = array(
        'serie-0' => array(
            'label' => 'Educação infantil',
            'help'  => ''
        ),
        'serie-1' => array(
            'label' => '1° ano',
            'help'  => ''
        ),
        'serie-2' => array(
            'label' => '2° ano',
            'help'  => ''
        ),
        'serie-3' => array(
            'label' => '3° ano',
            'help'  => ''
        ),
        'serie-4' => array(
            'label' => '4° ano',
            'help'  => ''
        ),
        'serie-5' => array(
            'label' => '5° ano',
            'help'  => ''
        ),
        'serie-6' => array(
            'label' => '6° ano',
            'help'  => ''
        ),
        'serie-7' => array(
            'label' => '7° ano',
            'help'  => ''
        ),
        'serie-8' => array(
            'label' => '8° ano',
            'help'  => ''
        ),
        'serie-9' => array(
            'label' => '9° ano',
            'help'  => ''
        )
    );

    protected function _preRender()
    {
        parent::_preRender();

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
            $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
            "educar_configuracoes_index.php"    => "Configurações",
            ""                                  => "Configuração movimento geral"
        ));
        $this->enviaLocalizacao($localizacao->montar());
    }

    public function Gerar() {

        foreach ($this->_formMap as $key => $value){
            $this->inputsHelper()->multipleSearchSerie($key, array('label' => $this->_getLabel($key), 'required' => false));
        }


    }

}

?>
