<?php

require_once 'Core/Controller/Page/ViewController.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

class ViewController extends Core_Controller_Page_ViewController
{

    protected $_dataMapper = 'RegraAvaliacao_Model_RegraDataMapper';
    protected $_titulo = 'Detalhes da regra de avaliação';
    protected $_processoAp = 947;

    protected $_tableMap = [
        'Nome' => 'nome',
        'Sistema de nota' => 'tipoNota',
        'Tabela de arredondamento' => 'tabelaArredondamento',
        'Progressão' => 'tipoProgressao',
        'Média para promoção' => 'media',
        'Média exame para promoção' => 'mediaRecuperacao',
        'Fórmula de cálculo de média final' => 'formulaMedia',
        'Fórmula de cálculo de recuperação' => 'formulaRecuperacao',
        'Porcentagem presença' => 'porcentagemPresenca',
        'Parecer descritivo' => 'parecerDescritivo',
        'Tipo de presença' => 'tipoPresenca',
        'Regra diferenciada' => 'regraDiferenciada',
        'Recuperação paralela' => 'tipoRecuperacaoParalela',
        'Nota máxima' => 'notaMaximaGeral',
        'Nota mínima' => 'notaMinimaGeral',
        'Nota máxima para exame final' => 'notaMaximaExameFinal',
        'Número máximo de casas decimais' => 'qtdCasasDecimais',
    ];

    protected function _preRender()
    {
        Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            'educar_index.php' => 'Escola',
            '' => 'Detalhe da regra de avalia&ccedil;&otilde;o'
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }
}
