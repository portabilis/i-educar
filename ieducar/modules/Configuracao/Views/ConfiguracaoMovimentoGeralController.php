<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'Configuracao/Model/ConfiguracaoMovimentoGeralDataMapper.php';

class clsIndexBase extends clsBase
{

    function Formular() {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Configuração movimento geral');
        $this->processoAp = 9998866;
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsCadastro
{
    private $configDataMapper;
    protected $_formMap    = array(
        'serie-0' => array(
            'label' => 'Educação infantil',
            'coluna'=> 0,
            'value' => '',
            'help'  => ''
        ),
        'serie-1' => array(
            'label' => '1° ano',
            'coluna'=> 1,
            'value' => ''
        ),
        'serie-2' => array(
            'label' => '2° ano',
            'coluna'=> 2,
            'value' => '',
            'help'  => ''
        ),
        'serie-3' => array(
            'label' => '3° ano',
            'coluna'=> 3,
            'value' => '',
            'help'  => ''
        ),
        'serie-4' => array(
            'label' => '4° ano',
            'coluna'=> 4,
            'value' => '',
            'help'  => ''
        ),
        'serie-5' => array(
            'label' => '5° ano',
            'coluna'=> 5,
            'value' => '',
            'help'  => ''
        ),
        'serie-6' => array(
            'label' => '6° ano',
            'coluna'=> 6,
            'value' => '',
            'help'  => ''
        ),
        'serie-7' => array(
            'label' => '7° ano',
            'coluna'=> 7,
            'value' => '',
            'help'  => ''
        ),
        'serie-8' => array(
            'label' => '8° ano',
            'coluna'=> 8,
            'value' => '',
            'help'  => ''
        ),
        'serie-9' => array(
            'label' => '9° ano',
            'coluna'=> 9,
            'value' => '',
            'help'  => ''
        )
    );

    function Inicializar()
    {
        $this->loadConfig();
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(9998866, $_SESSION['id_pessoa'], 1,
            'educar_index.php');
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
        foreach ($this->_formMap as $key => $value){
            $this->inputsHelper()->multipleSearchSerie($key, array('label' => $value['label'], 'required' => false));
        }
    }

    function loadConfig() {
        $this->configDataMapper = new ConfiguracaoMovimentoGeralDataMapper();
        foreach ($this->configDataMapper->findAll() as $config){
            $config;
            $series = explode(',', $this->_formMap['serie-'.$config->get('coluna')]['value']);
            if (empty($series[0])){
                $series = array();
            }
            $series[] = $config->get('serie');

            $this->_formMap['serie-'.$config->get('coluna')]['value'] = implode(',',$series);
        }
    }

    function Editar()
    {

        $this->configDataMapper = new ConfiguracaoMovimentoGeralDataMapper();
        $salvou = true;
        $this->deleteAllConfigs();
        foreach ($_POST as $key => $value){
            if (strpos($key,'multiple_search_serie_serie-') === 0) {
                $series = array();
                if (!empty($value)){
                    $series = explode(',', $value);
                }
                $coluna = str_replace('multiple_search_serie_serie-', '', $key);
                foreach ($series as $serie) {
                    if (isset($serie)){
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

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();

?>
