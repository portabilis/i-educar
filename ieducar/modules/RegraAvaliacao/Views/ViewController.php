<?php

class RegraAvaliacao_Views_ViewController extends Core_Controller_Page_ViewController
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

    public function gerar()
    {
        parent::Gerar();
        foreach ($this->detalhe as $key => $detalhe) {
            if ($detalhe[0] === 'Tabela de arredondamento') {
                $link = sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    '/module/TabelaArredondamento/view?id=' . (int) $detalhe[1]->id,
                    $detalhe[1]->nome
                );

                $detalhe[1] = $link;
                $this->detalhe[$key] = $detalhe;
            }

            if ($detalhe[0] === 'Fórmula de cálculo de média final' || $detalhe[0] === 'Fórmula de cálculo de recuperação') {
                $link = sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    '/module/FormulaMedia/view?id=' . (int) $detalhe[1]->id,
                    $detalhe[1]->nome
                );

                $detalhe[1] = $link;
                $this->detalhe[$key] = $detalhe;
            }
        }
    }
}
