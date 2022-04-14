<?php

class ViewController extends Core_Controller_Page_ViewController
{
    /**
     * @var string
     */
    protected $_dataMapper = 'RegraAvaliacao_Model_RegraDataMapper';

    /**
     * @var string
     */
    protected $_titulo = 'Detalhes da regra de avaliação';

    /**
     * @var int
     */
    protected $_processoAp = 947;

    /**
     * @var array
     */
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
        'Falta máxima' => 'faltaMaximaGeral',
        'Falta mínima' => 'faltaMinimaGeral',
        'Nota máxima para exame final' => 'notaMaximaExameFinal',
        'Número máximo de casas decimais' => 'qtdCasasDecimais',
    ];

    /**
     * {@inheritdoc}
     */
    protected function _preRender()
    {
        $this->breadcrumb('Detalhes da regra de avaliação', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->addBotao('Copiar regra', "/module/RegraAvaliacao/edit?id={$this->getRequest()->id}&copy=true");
    }
}
