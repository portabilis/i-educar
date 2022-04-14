<?php

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->titulo = 'Configuração movimento geral';
        $this->processoAp = 9998867;
    }
}

class ConfiguracaoMovimentoGeralController extends clsCadastro
{
    private $configDataMapper;
    protected $_formMap    = [
        'serie-0' => [
            'label' => 'Educação infantil',
            'coluna'=> 0,
            'value' => [],
            'help'  => ''
        ],
        'serie-1' => [
            'label' => '1° ano',
            'coluna'=> 1,
            'value' => []
        ],
        'serie-2' => [
            'label' => '2° ano',
            'coluna'=> 2,
            'value' => [],
            'help'  => ''
        ],
        'serie-3' => [
            'label' => '3° ano',
            'coluna'=> 3,
            'value' => [],
            'help'  => ''
        ],
        'serie-4' => [
            'label' => '4° ano',
            'coluna'=> 4,
            'value' => [],
            'help'  => ''
        ],
        'serie-5' => [
            'label' => '5° ano',
            'coluna'=> 5,
            'value' => [],
            'help'  => ''
        ],
        'serie-6' => [
            'label' => '6° ano',
            'coluna'=> 6,
            'value' => [],
            'help'  => ''
        ],
        'serie-7' => [
            'label' => '7° ano',
            'coluna'=> 7,
            'value' => [],
            'help'  => ''
        ],
        'serie-8' => [
            'label' => '8° ano',
            'coluna'=> 8,
            'value' => [],
            'help'  => ''
        ],
        'serie-9' => [
            'label' => '9° ano',
            'coluna'=> 9,
            'value' => [],
            'help'  => ''
        ]
    ];

    public $_titulo = 'Configuração movimento geral';
    public $_processoAp = 9998867;

    public function Inicializar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(9998867, $this->pessoa_logada, 1, 'educar_index.php');

        $this->breadcrumb('Configuração movimento geral', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        return 'Editar';
    }

    public function Gerar()
    {
        $this->loadConfig();
        foreach ($this->_formMap as $key => $value) {
            $this->inputsHelper()->multipleSearchSerie($key, ['label' => $value['label'], 'required' => false, 'values' => $value['value'], 'coluna' => $value['coluna']], '');
        }
    }

    public function loadConfig()
    {
        $this->configDataMapper = new ConfiguracaoMovimentoGeralDataMapper();
        foreach ($this->configDataMapper->findAll() as $config) {
            $config;
            $series = $this->_formMap['serie-'.$config->get('coluna')]['value'];

            $series[] = $config->get('serie');

            $this->_formMap['serie-'.$config->get('coluna')]['value'] = $series;
        }
    }

    public function Editar()
    {
        $this->configDataMapper = new ConfiguracaoMovimentoGeralDataMapper();
        $salvou = true;
        $this->deleteAllConfigs();
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'multiple_search_serie_serie-') === 0) {
                $series = $value;
                $coluna = str_replace('multiple_search_serie_serie-', '', $key);
                foreach ($series as $serie) {
                    if (!empty($serie)) {
                        $this->configDataMapper->save($this->configDataMapper->createNewEntityInstance(['coluna' => $coluna, 'serie' => $serie]));
                    }
                }
            }
        }
        $this->mensagem .= 'Edição efetuada com sucesso.<br>';

        return $salvou;
    }

    public function deleteAllConfigs()
    {
        $this->configDataMapper = new ConfiguracaoMovimentoGeralDataMapper();
        foreach ($this->configDataMapper->findAll() as $config) {
            $this->configDataMapper->delete($config);
        }
    }
}
