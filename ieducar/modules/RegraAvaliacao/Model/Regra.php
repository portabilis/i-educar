<?php

class RegraAvaliacao_Model_Regra extends CoreExt_Entity
{
    protected $_data = [
        'instituicao' => null,
        'nome' => null,
        'tipoNota' => null,
        'tipoProgressao' => null,
        'tabelaArredondamento' => null,
        'tabelaArredondamentoConceitual' => null,
        'media' => null,
        'formulaMedia' => null,
        'formulaRecuperacao' => null,
        'porcentagemPresenca' => null,
        'parecerDescritivo' => null,
        'tipoPresenca' => null,
        'mediaRecuperacao' => null,
        'tipoRecuperacaoParalela' => null,
        'mediaRecuperacaoParalela' => null,
        'notaMaximaGeral' => null,
        'notaMinimaGeral' => null,
        'faltaMaximaGeral' => null,
        'faltaMinimaGeral' => null,
        'notaMaximaExameFinal' => null,
        'qtdCasasDecimais' => null,
        'notaGeralPorEtapa' => null,
        'definirComponentePorEtapa' => null,
        'qtdDisciplinasDependencia' => null,
        'disciplinasAglutinadas' => null,
        'qtdMatriculasDependencia' => null,
        'aprovaMediaDisciplina' => null,
        'reprovacaoAutomatica' => null,
        'regraDiferenciada' => null,
        'calculaMediaRecParalela' => null,
        'tipoCalculoRecuperacaoParalela' => null,
        'desconsiderarLancamentoFrequencia' => null,
    ];

    protected $_dataTypes = [
        'media' => 'numeric',
        'porcentagemPresenca' => 'numeric',
        'mediaRecuperacao' => 'numeric',
        'tipoRecuperacaoParalela' => 'numeric',
        'notaMaximaGeral' => 'numeric',
        'notaMinimaGeral' => 'numeric',
        'faltaMaximaGeral' => 'numeric',
        'faltaMinimaGeral' => 'numeric',
        'notaMaximaExameFinal' => 'numeric',
        'qtdCasasDecimais' => 'numeric',
        'qtdDisciplinasDependencia' => 'numeric',
        'qtdMatriculasDependencia' => 'numeric',
    ];

    protected $_references = [
        'tipoNota' => [
            'value' => 1,
            'class' => 'RegraAvaliacao_Model_Nota_TipoValor',
            'file' => 'RegraAvaliacao/Model/Nota/TipoValor.php'
        ],
        'tabelaArredondamento' => [
            'value' => 1,
            'class' => 'TabelaArredondamento_Model_TabelaDataMapper',
            'file' => 'TabelaArredondamento/Model/TabelaDataMapper.php',
            'null' => true
        ],
        'tabelaArredondamentoConceitual' => [
            'value' => 1,
            'class' => 'TabelaArredondamento_Model_TabelaDataMapper',
            'file' => 'TabelaArredondamento/Model/TabelaDataMapper.php',
            'null' => true
        ],
        'tipoProgressao' => [
            'value' => 1,
            'class' => 'RegraAvaliacao_Model_TipoProgressao',
            'file' => 'RegraAvaliacao/Model/TipoProgressao.php'
        ],
        'parecerDescritivo' => [
            'value' => 0,
            'class' => 'RegraAvaliacao_Model_TipoParecerDescritivo',
            'file' => 'RegraAvaliacao/Model/TipoParecerDescritivo.php',
            'null' => true
        ],
        'tipoPresenca' => [
            'value' => 1,
            'class' => 'RegraAvaliacao_Model_TipoPresenca',
            'file' => 'RegraAvaliacao/Model/TipoPresenca.php'
        ],
        'formulaMedia' => [
            'value' => null,
            'class' => 'FormulaMedia_Model_FormulaDataMapper',
            'file' => 'FormulaMedia/Model/FormulaDataMapper.php',
            'null' => true
        ],
        'formulaRecuperacao' => [
            'value' => null,
            'class' => 'FormulaMedia_Model_FormulaDataMapper',
            'file' => 'FormulaMedia/Model/FormulaDataMapper.php',
            'null' => true
        ],
        'tipoRecuperacaoParalela' => [
            'value' => 0,
            'class' => 'RegraAvaliacao_Model_TipoRecuperacaoParalela',
            'file' => 'RegraAvaliacao/Model/TipoRecuperacaoParalela.php',
            'null' => true
        ],
        'regraDiferenciada' => [
            'value' => null,
            'class' => 'RegraAvaliacao_Model_RegraDataMapper',
            'file' => 'RegraAvaliacao/Model/RegraDataMapper.php',
            'null' => true
        ]
    ];

    /**
     * @var array
     */
    protected $_regraRecuperacoes = [];

    /**
     * @see CoreExt_Entity::getDataMapper()
     */
    public function getDataMapper()
    {
        if (is_null($this->_dataMapper)) {
            $this->setDataMapper(new RegraAvaliacao_Model_RegraDataMapper());
        }

        return parent::getDataMapper();
    }

    /**
     * @see CoreExt_Entity_Validatable::getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        // Enums
        $tipoNotaValor = RegraAvaliacao_Model_Nota_TipoValor::getInstance();
        $tipoProgressao = RegraAvaliacao_Model_TipoProgressao::getInstance();
        $tipoParecerDescritivo = RegraAvaliacao_Model_TipoParecerDescritivo::getInstance();
        $tipoPresenca = RegraAvaliacao_Model_TipoPresenca::getInstance();
        $tipoRecuperacaoParalela = RegraAvaliacao_Model_TipoRecuperacaoParalela::getInstance();

        // ids de fórmulas de média
        $formulaMedia = $this->getDataMapper()->findFormulaMediaFinal();
        $formulaMedia = CoreExt_Entity::entityFilterAttr($formulaMedia, 'id');

        // ids de fórmulas de recuperação
        $formulaRecuperacao = $this->getDataMapper()->findFormulaMediaRecuperacao();
        $formulaRecuperacao = CoreExt_Entity::entityFilterAttr($formulaRecuperacao, 'id');
        $formulaRecuperacao[0] = null;

        // ids de regras diferenciadas
        $regraDiferenciada = $this->getDataMapper()->findAll();
        $regraDiferenciada = CoreExt_Entity::entityFilterAttr($regraDiferenciada, 'id');
        $regraDiferenciada[0] = null;

        // ids de tabelas de arredondamento
        $tabelas = $this->getDataMapper()->findTabelaArredondamento($this);
        $tabelas = CoreExt_Entity::entityFilterAttr($tabelas, 'id');

        // Instituições
        $instituicoes = array_keys(App_Model_IedFinder::getInstituicoes());

        // Fórmula de média é obrigatória?
        $isFormulaMediaRequired = true;

        // Média é obrigatória?
        $isMediaRequired = true;

        if ($this->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
            $isFormulaMediaRequired = false;
            $isMediaRequired = false;

            // Aceita somente o valor NULL quando o tipo de nota é Nenhum.
            $formulaMedia = $formulaMedia + [null];
        }

        return [
            'instituicao' => new CoreExt_Validate_Choice([
                'choices' => $instituicoes
            ]),
            'nome' => new CoreExt_Validate_String([
                'min' => 5, 'max' => 50
            ]),
            'formulaMedia' => new CoreExt_Validate_Choice([
                'choices' => $formulaMedia,
                'required' => $isFormulaMediaRequired
            ]),
            'formulaRecuperacao' => new CoreExt_Validate_Choice([
                'choices' => $formulaRecuperacao,
                'required' => false
            ]),
            'regraDiferenciada' => new CoreExt_Validate_Choice([
                'choices' => $regraDiferenciada,
                'required' => false
            ]),
            'tipoNota' => new CoreExt_Validate_Choice([
                'choices' => $tipoNotaValor->getKeys()
            ]),
            'tipoProgressao' => new CoreExt_Validate_Choice([
                'choices' => $tipoProgressao->getKeys()
            ]),
            'tabelaArredondamento' => new CoreExt_Validate_Choice([
                'choices' => $tabelas,
                'choice_error' => 'A tabela de arredondamento selecionada não ' .
                    'corresponde ao sistema de nota escolhido.'
            ]),
            'tabelaArredondamentoConceitual' => new CoreExt_Validate_Choice([
                'choices' => $tabelas,
                'choice_error' => 'A tabela de arredondamento selecionada não ' .
                    'corresponde ao sistema de nota escolhido.'
            ]),
            'parecerDescritivo' => new CoreExt_Validate_Choice([
                'choices' => $tipoParecerDescritivo->getKeys()
            ]),
            'tipoPresenca' => new CoreExt_Validate_Choice([
                'choices' => $tipoPresenca->getKeys()
            ]),
            'media' => $this->validateIfEquals(
                'tipoProgressao',
                RegraAvaliacao_Model_TipoProgressao::CONTINUADA,
                'CoreExt_Validate_Numeric',
                ['required' => $isMediaRequired, 'min' => 1, 'max' => 10],
                ['required' => $isMediaRequired, 'min' => 0, 'max' => 10]
            ),
            'porcentagemPresenca' => new CoreExt_Validate_Numeric([
                'min' => 1, 'max' => 100
            ]),
            'mediaRecuperacao' => $this->validateIfEquals(
                'tipoProgressao',
                RegraAvaliacao_Model_TipoProgressao::CONTINUADA,
                'CoreExt_Validate_Numeric',
                ['required' => $isMediaRequired, 'min' => 1, 'max' => 14],
                ['required' => $isMediaRequired, 'min' => 0, 'max' => 14]
            ),
            'tipoRecuperacaoParalela' => new CoreExt_Validate_Choice([
                'choices' => $tipoRecuperacaoParalela->getKeys()
            ]),
            'mediaRecuperacaoParalela' => new CoreExt_Validate_String([
                'min' => 1, 'max' => 10
            ])
        ];
    }

    /**
     * Método finder para RegraAvaliacao_Model_RegraRecuperacao. Wrapper simples
     * para o mesmo método de RegraAvaliacao_Model_TabelaDataMapper.
     *
     * @return array
     */
    public function findRegraRecuperacao()
    {
        if (0 == count($this->_regraRecuperacoes)) {
            $this->_regraRecuperacoes = $this->getDataMapper()->findRegraRecuperacao($this);
        }

        return $this->_regraRecuperacoes;
    }

    public function getRegraRecuperacaoByEtapa($etapa)
    {
        foreach ($this->findRegraRecuperacao() as $key => $_regraRecuperacao) {
            if (in_array($etapa, $_regraRecuperacao->getEtapas())) {
                return $_regraRecuperacao;
            }
        }

        return null;
    }

    /**
     * Pega a nota máxima permitida para a recuperação
     *
     * @param $etapa
     *
     * @return float
     */
    public function getNotaMaximaRecuperacao($etapa)
    {
        $tipoRecuperacaoParalela = $this->get('tipoRecuperacaoParalela');

        if ($tipoRecuperacaoParalela != RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPAS_ESPECIFICAS) {
            return $this->notaMaximaGeral;
        }

        return $this->getRegraRecuperacaoByEtapa($etapa)->notaMaxima;
    }

    /**
     * @see CoreExt_Entity::__toString()
     */
    public function __toString()
    {
        return $this->nome;
    }
}
