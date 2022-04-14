<?php

class RegraAvaliacao_Model_RegraDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'RegraAvaliacao_Model_Regra';

    protected $_tableName = 'regra_avaliacao';

    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'id' => 'id',
        'instituicao' => 'instituicao_id',
        'tipoNota' => 'tipo_nota',
        'tipoProgressao' => 'tipo_progressao',
        'tabelaArredondamento' => 'tabela_arredondamento_id',
        'tabelaArredondamentoConceitual' => 'tabela_arredondamento_id_conceitual',
        'formulaMedia' => 'formula_media_id',
        'formulaRecuperacao' => 'formula_recuperacao_id',
        'porcentagemPresenca' => 'porcentagem_presenca',
        'desconsiderarLancamentoFrequencia' => 'desconsiderar_lancamento_frequencia',
        'parecerDescritivo' => 'parecer_descritivo',
        'tipoPresenca' => 'tipo_presenca',
        'mediaRecuperacao' => 'media_recuperacao',
        'tipoRecuperacaoParalela' => 'tipo_recuperacao_paralela',
        'tipoCalculoRecuperacaoParalela' => 'tipo_calculo_recuperacao_paralela',
        'mediaRecuperacaoParalela' => 'media_recuperacao_paralela',
        'calculaMediaRecParalela' => 'calcula_media_rec_paralela',
        'notaMaximaGeral' => 'nota_maxima_geral',
        'notaMinimaGeral' => 'nota_minima_geral',
        'faltaMaximaGeral' => 'falta_maxima_geral',
        'faltaMinimaGeral' => 'falta_minima_geral',
        'notaMaximaExameFinal' => 'nota_maxima_exame_final',
        'qtdCasasDecimais' => 'qtd_casas_decimais',
        'notaGeralPorEtapa' => 'nota_geral_por_etapa',
        'definirComponentePorEtapa' => 'definir_componente_etapa',
        'qtdDisciplinasDependencia' => 'qtd_disciplinas_dependencia',
        'disciplinasAglutinadas' => 'disciplinas_aglutinadas',
        'qtdMatriculasDependencia' => 'qtd_matriculas_dependencia',
        'aprovaMediaDisciplina' => 'aprova_media_disciplina',
        'reprovacaoAutomatica' => 'reprovacao_automatica',
        'regraDiferenciada' => 'regra_diferenciada_id',
    ];

    protected $_primaryKey = [
        'id' => 'id',
        'instituicao' => 'instituicao_id'
    ];

    /**
     * @var FormulaMedia_Model_FormulaDataMapper
     */
    protected $_formulaDataMapper = null;

    /**
     * @var TabelaArredondamento_Model_TabelaDataMapper
     */
    protected $_tabelaDataMapper = null;

    /**
     * Setter.
     *
     * @param FormulaMedia_Model_FormulaDataMapper $mapper
     *
     * @return RegraAvaliacao_Model_RegraDataMapper
     */
    public function setFormulaDataMapper(FormulaMedia_Model_FormulaDataMapper $mapper)
    {
        $this->_formulaDataMapper = $mapper;

        return $this;
    }

    /**
     * Getter.
     *
     * @return FormulaMedia_Model_FormulaDataMapper
     */
    public function getFormulaDataMapper()
    {
        if (is_null($this->_formulaDataMapper)) {
            $this->setFormulaDataMapper(new FormulaMedia_Model_FormulaDataMapper());
        }

        return $this->_formulaDataMapper;
    }

    /**
     * Setter.
     *
     * @param TabelaArredondamento_Model_TabelaDataMapper $mapper
     *
     * @return CoreExt_DataMapper Provê interface fluída
     */
    public function setTabelaDataMapper(TabelaArredondamento_Model_TabelaDataMapper $mapper)
    {
        $this->_tabelaDataMapper = $mapper;

        return $this;
    }

    /**
     * Getter.
     *
     * @return TabelaArredondamento_Model_TabelaDataMapper
     */
    public function getTabelaDataMapper()
    {
        if (is_null($this->_tabelaDataMapper)) {
            $this->setTabelaDataMapper(
                new TabelaArredondamento_Model_TabelaDataMapper()
            );
        }

        return $this->_tabelaDataMapper;
    }

    /**
     * Finder.
     *
     * @return array Array de objetos FormulaMedia_Model_Formula
     */
    public function findFormulaMediaFinal($where = [])
    {
        return $this->_findFormulaMedia(
            [$this->_getTableColumn('tipoFormula') =>
                FormulaMedia_Model_TipoFormula::MEDIA_FINAL]
        );
    }

    /**
     * Finder.
     *
     * @return array Array de objetos FormulaMedia_Model_Formula
     */
    public function findFormulaMediaRecuperacao($where = [])
    {
        return $this->_findFormulaMedia(
            [$this->_getTableColumn('tipoFormula')
                => FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO]
        );
    }

    /**
     * Finder genérico para FormulaMedia_Model_Formula.
     *
     * @param array $where
     *
     * @return array Array de objetos FormulaMedia_Model_Formula
     */
    protected function _findFormulaMedia(array $where = [])
    {
        return $this->getFormulaDataMapper()->findAll(['nome'], $where);
    }

    /**
     * Finder para instâncias de TabelaArredondamento_Model_Tabela. Utiliza
     * o valor de instituição por instâncias que referenciem a mesma instituição.
     *
     * @param RegraAvaliacao_Model_Regra $instance
     *
     * @return array
     */
    public function findTabelaArredondamento(RegraAvaliacao_Model_Regra $instance, array $where = [])
    {
        if (isset($instance->instituicao)) {
            $where['instituicao'] = $instance->instituicao;
        }

        return $this->getTabelaDataMapper()->findAll([], $where);
    }

    /**
     * @var RegraAvaliacao_Model_RegraRecuperacaoDataMapper
     */
    protected $_regraRecuperacaoDataMapper = null;

    /**
     * Setter.
     *
     * @param RegraAvaliacao_Model_RegraRecuperacaoDataMapper $mapper
     *
     * @return CoreExt_DataMapper Provê interface fluída
     */
    public function setRegraRecuperacaoDataMapper(RegraAvaliacao_Model_RegraRecuperacaoDataMapper $mapper)
    {
        $this->_regraRecuperacaoDataMapper = $mapper;

        return $this;
    }

    /**
     * Getter.
     *
     * @return RegraAvaliacao_Model_RegraRecuperacaoDataMappers
     */
    public function getRegraRecuperacaoDataMapper()
    {
        if (is_null($this->_regraRecuperacaoDataMapper)) {
            $this->setRegraRecuperacaoDataMapper(
                new RegraAvaliacao_Model_RegraRecuperacaoDataMapper()
            );
        }

        return $this->_regraRecuperacaoDataMapper;
    }

    /**
     * Finder para instâncias de RegraAvaliacao_Model_RegraRecuperacao que tenham
     * referências a instância RegraAvaliacao_Model_Regra passada como
     * parâmetro.
     *
     * @param RegraAvaliacao_Model_Regra $instance
     *
     * @return array Um array de instâncias RegraAvaliacao_Model_RegraRecuperacao
     */
    public function findRegraRecuperacao(RegraAvaliacao_Model_Regra $instance)
    {
        $where = [
      'regraAvaliacao' => $instance->id
    ];

        $orderby = [
      'etapasRecuperadas' => 'ASC'
    ];

        return $this->getRegraRecuperacaoDataMapper()->findAll(
            [],
            $where,
            $orderby
        );
    }
}
