<?php

require_once 'Core/Controller/Page/ListController.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

class IndexController extends Core_Controller_Page_ListController
{

    protected $_dataMapper = 'RegraAvaliacao_Model_RegraDataMapper';
    protected $_titulo = 'Listagem de regras de avaliação';
    protected $_processoAp = 947;

    protected $_tableMap = [
        'Nome' => 'nome',
        'Sistema de nota' => 'tipoNota',
        'Progressão' => 'tipoProgressao',
        'Média aprovação' => 'media',
        'Média exame' => 'mediaRecuperacao',
        'Fórmula média' => 'formulaMedia',
        'Fórmula recuperação' => 'formulaRecuperacao',
        'Recuperação paralela' => 'tipoRecuperacaoParalela'
    ];

    protected function _preRender()
    {
        parent::_preRender();

        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            'educar_index.php' => 'Escola',
            '' => 'Listagem de regras de avalia&ccedil;&otilde;es'
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }
}
