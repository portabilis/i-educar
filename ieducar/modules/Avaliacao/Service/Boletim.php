<?php

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGrade;
use App\Models\LegacyInstitution;
use App\Services\StageScoreCalculationService;
use iEducar\Modules\Enrollments\Exceptions\StudentNotEnrolledInSchoolClass;
use iEducar\Modules\EvaluationRules\Exceptions\EvaluationRuleNotDefinedInLevel;
use iEducar\Modules\Stages\Exceptions\MissingStagesException;
use iEducar\Modules\Stages\Exceptions\StagesNotInformedByCoordinatorException;
use iEducar\Modules\Stages\Exceptions\StagesNotInformedByTeacherException;

require_once 'CoreExt/Configurable.php';
require_once 'CoreExt/Entity.php';
require_once 'App/Model/IedFinder.php';
require_once 'App/Model/Matricula.php';
require_once 'App/Model/MatriculaSituacao.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'ComponenteCurricular/Model/TipoNotaComponenteSerie.php';
require_once 'Avaliacao/Service/Boletim/Acessores.php';

class Avaliacao_Service_Boletim implements CoreExt_Configurable
{
    use Avaliacao_Service_Boletim_Acessores;

    private $exemptedStages = [];

    /**
     * Prioridade da situação da matrícula, usado para definir a situação
     * das notas e faltas.
     *
     * @var array
     */
    protected $_situacaoPrioridade = [
        App_Model_MatriculaSituacao::EM_ANDAMENTO => 1,
        App_Model_MatriculaSituacao::EM_EXAME => 2,
        App_Model_MatriculaSituacao::REPROVADO => 3,
        App_Model_MatriculaSituacao::APROVADO_APOS_EXAME => 4,
        App_Model_MatriculaSituacao::APROVADO => 5
    ];

    /**
     * Construtor.
     *
     * Opções de configuração:
     * - matricula (int), obrigatória
     * - ComponenteDataMapper (Componente_Model_ComponenteDataMapper), opcional
     * - RegraDataMapper (Regra_Model_RegraDataMapper), opcional
     * - NotaAlunoDataMapper (Avaliacao_Model_NotaAlunoDataMapper), opcional
     *
     * @param array $options
     *
     * @throws App_Model_Exception
     * @throws CoreExt_Service_Exception
     * @throws EvaluationRuleNotDefinedInLevel
     * @throws StudentNotEnrolledInSchoolClass
     * @throws Exception
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
        $this->_setMatriculaInfo();
        $this->_loadNotas();
        $this->_loadFalta();
        $this->_loadParecerDescritivo();
    }

    /**
     * @param $enrollmentId
     * @param $disciplineId
     *
     * @return array
     *
     * @throws Exception
     */
    private function getExemptedStages($enrollmentId, $disciplineId)
    {
        if (!isset($this->exemptedStages[$enrollmentId])) {
            $this->exemptedStages[$enrollmentId] = App_Model_IedFinder::getExemptedStages($enrollmentId, $disciplineId);
        }

        return $this->exemptedStages[$enrollmentId][$disciplineId] ?? [];
    }

    /**
     * Retorna uma instância de Avaliacao_Model_NotaComponente.
     *
     * @param int $id    O identificador de ComponenteCurricular_Model_Componente
     * @param int $etapa A etapa para o qual a nota foi lançada
     *
     * @return Avaliacao_Model_NotaComponente|NULL
     */
    public function getNotaComponente($id, $etapa = 1)
    {
        $componentes = $this->getNotasComponentes();

        if (!isset($componentes[$id])) {
            return null;
        }

        $notasComponente = $componentes[$id];

        foreach ($notasComponente as $nota) {
            if ($nota->etapa == $etapa) {
                return $nota;
            }
        }

        return null;
    }

    /**
     * @param $id
     *
     * @return Array|null
     */
    public function getMediaComponente($id)
    {
        $componentes = $this->getMediasComponentes();

        if (!isset($componentes[$id])) {
            return null;
        }

        $mediaComponente = $componentes[$id];

        return $mediaComponente[0];
    }

    /**
     * Retorna uma instância de Avaliacao_Model_NotaGeral.
     *
     * @param int $id    O identificador de ComponenteCurricular_Model_Componente
     * @param int $etapa A etapa para o qual a nota foi lançada
     *
     * @return Avaliacao_Model_NotaComponente|NULL
     */
    public function getNotaGeral($etapa = 1)
    {
        $notasGerais = $this->getNotasGerais();

        foreach ($notasGerais as $nota) {
            if ($nota->etapa == $etapa) {
                return $nota;
            }
        }

        return null;
    }

    /**
     * Retorna uma instância de Avaliacao_Model_FaltaAbstract.
     *
     * @param int $etapa A etapa para o qual a falta foi lançada
     * @param int $id    O identificador de ComponenteCurricular_Model_Componente
     *
     * @return Avaliacao_Model_FaltaAbstract|NULL
     */
    public function getFalta($etapa = 1, $id = null)
    {
        if ($this->getRegraAvaliacaoTipoPresenca() == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $faltas = $this->getFaltasComponentes();

            if (!isset($faltas[$id])) {
                return null;
            }

            $faltas = $faltas[$id];
        } else {
            $faltas = $this->getFaltasGerais();
        }

        foreach ($faltas as $falta) {
            if ($falta->etapa == $etapa) {
                return $falta;
            }
        }

        return null;
    }

    /**
     * Retorna uma instância de Avaliacao_Model_ParecerDescritivoAbstract.
     *
     * @param int $etapa A etapa para o qual o parecer foi lançado
     * @param int $id    O identificador de ComponenteCurricular_Model_Componente
     *
     * @return Avaliacao_Model_ParecerAbstract|NULL
     */
    public function getParecerDescritivo($etapa = 1, $id = null)
    {
        $parecerDescritivo = $this->getRegraAvaliacaoTipoParecerDescritivo();

        $gerais = [
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL
        ];

        $componentes = [
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
        ];

        $pareceres = [];

        if (in_array($parecerDescritivo, $gerais)) {
            $pareceres = $this->getPareceresGerais();
        } elseif (in_array($parecerDescritivo, $componentes)) {
            $pareceres = $this->getPareceresComponentes();

            if (!isset($pareceres[$id])) {
                return null;
            }

            $pareceres = $pareceres[$id];
        }

        foreach ($pareceres as $parecer) {
            if ($parecer->etapa == $etapa) {
                return $parecer;
            }
        }

        return null;
    }

    /**
     * @return $this
     *
     * @throws App_Model_Exception
     * @throws StudentNotEnrolledInSchoolClass
     * @throws EvaluationRuleNotDefinedInLevel
     */
    protected function _setMatriculaInfo()
    {
        $codMatricula = $this->getOption('matricula');
        $matricula = App_Model_IedFinder::getMatricula($codMatricula);
        $etapas = App_Model_IedFinder::getQuantidadeDeModulosMatricula($codMatricula, $matricula);
        $maiorEtapaUtilizada = $etapas;

        // Foi preciso adicionar esta validação pois é possível filtrar sem
        // selecionar um componente curricular, neste caso um erro SQL era gerado.
        if ($componenteCurricularId = $this->getComponenteCurricularId()) {
            $ultimaEtapaEspecifica = App_Model_IedFinder::getUltimaEtapaComponente(
                $matricula['ref_cod_turma'],
                $componenteCurricularId
            );

            if ($ultimaEtapaEspecifica) {
                $maiorEtapaUtilizada = $ultimaEtapaEspecifica;
            }
        }

        $etapaAtual = ($_GET['etapa'] ?? null) == 'Rc' ? $maiorEtapaUtilizada : ($_GET['etapa'] ?? null);

        $this->_setRegra(App_Model_IedFinder::getRegraAvaliacaoPorMatricula(
            $codMatricula,
            $this->getRegraDataMapper(),
            $matricula
        ));

        $this->_setComponentes(App_Model_IedFinder::getComponentesPorMatricula($codMatricula, $this->getComponenteDataMapper(), $this->getComponenteTurmaDataMapper(), $componenteCurricularId, $etapaAtual, null, $matricula));

        $this->setOption('matriculaData', $matricula);
        $this->setOption('aprovado', $matricula['aprovado']);
        $this->setOption('cursoHoraFalta', $matricula['curso_hora_falta']);
        $this->setOption('cursoCargaHoraria', $matricula['curso_carga_horaria']);
        $this->setOption('serieCargaHoraria', $matricula['serie_carga_horaria']);
        $this->setOption('serieDiasLetivos', $matricula['serie_dias_letivos']);
        $this->setOption('ref_cod_turma', $matricula['ref_cod_turma']);
        $this->setOption('etapas', $etapas);
        $this->setOption('etapaAtual', $etapaAtual);

        return $this;
    }

    /**
     * Carrega todas as notas e médias já lançadas para a matrícula atual.
     *
     * @param bool $loadMedias FALSE caso não seja necessário carregar as médias
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function _loadNotas($loadMedias = true)
    {
        // Cria uma entrada no boletim caso o aluno/matrícula não a tenha
        if (!$this->hasNotaAluno()) {
            $this->_createNotaAluno();
        }

        // Se não tiver, vai criar
        $notaAluno = $this->_getNotaAluno();

        $notas = $this->getNotaComponenteDataMapper()->findAll(
            [],
            ['notaAluno' => $notaAluno->id],
            ['etapa' => 'ASC']
        );

        // Separa cada nota em um array indexado pelo identity do componente
        $notasComponentes = [];
        foreach ($notas as $nota) {
            $notasComponentes[$nota->get('componenteCurricular')][] = $nota;
        }

        //Carrega as notas indexadas pela etapa
        $notasGerais = [];
        $notas = $this->getNotaGeralDataMapper()->findAll(
            [],
            ['notaAluno' => $notaAluno->id],
            ['etapa' => 'ASC']
        );

        foreach ($notas as $nota) {
            $notasGerais[$nota->get('etapa')] = $nota;
        }

        $this->setNotasComponentes($notasComponentes);
        $this->setNotasGerais($notasGerais);

        if (false == $loadMedias) {
            return $this;
        }

        return $this->_loadMedias();
    }

    /**
     * Carrega as médias dos componentes curriculares já lançadas.
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function _loadMedias()
    {
        $notaAluno = $this->_getNotaAluno();

        $medias = $this->getNotaComponenteMediaDataMapper()->findAll(
            [],
            ['notaAluno' => $notaAluno->id]
        );

        $mediasComponentes = [];
        foreach ($medias as $media) {
            $mediasComponentes[$media->get('componenteCurricular')][] = $media;
        }

        $mediasGerais = [];

        $mediasGerais = $this->getMediaGeralDataMapper()->findAll(
            [],
            ['notaAluno' => $notaAluno->id]
        );

        foreach ($mediasGerais as $mediaGeral) {
            $mediasGerais = $mediaGeral;
        }

        $this->setMediasComponentes($mediasComponentes);
        $this->setMediaGeral($mediasGerais);

        return $this;
    }

    /**
     * Carrega as faltas do aluno, sejam gerais ou por componente.
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function _loadFalta()
    {
        // Cria uma entrada no boletim caso o aluno/matrícula não a tenha
        if (!$this->hasFaltaAluno()) {
            $this->_createFaltaAluno();
        }

        // Senão tiver, vai criar
        $faltaAluno = $this->_getFaltaAluno();

        // Carrega as faltas já lançadas
        $faltas = $this->getFaltaAbstractDataMapper()->findAll(
            [],
            ['faltaAluno' => $faltaAluno->id],
            ['etapa' => 'ASC']
        );

        // Se a falta for do tipo geral, popula um array indexado pela etapa
        if ($faltaAluno->get('tipoFalta') == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            $faltasGerais = [];

            foreach ($faltas as $falta) {
                $faltasGerais[$falta->etapa] = $falta;
            }

            $this->setFaltasGerais($faltasGerais);
        } elseif ($faltaAluno->get('tipoFalta') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $faltasComponentes = [];
            // Separa cada nota em um array indexado pelo identity field do componente
            foreach ($faltas as $falta) {
                $faltasComponentes[$falta->get('componenteCurricular')][] = $falta;
            }

            $this->setFaltasComponentes($faltasComponentes);
        }

        return $this;
    }

    /**
     * Carrega os pareceres do aluno, sejam gerais ou por componentes.
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function _loadParecerDescritivo()
    {
        if ($this->getRegraAvaliacaoTipoParecerDescritivo() == RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM) {
            return $this;
        }

        if (!$this->hasParecerDescritivoAluno()) {
            $this->_createParecerDescritivoAluno();
        }

        $parecerDescritivoAluno = $this->_getParecerDescritivoAluno();

        $pareceres = $this->getParecerDescritivoAbstractDataMapper()->findAll(
            [],
            ['parecerDescritivoAluno' => $parecerDescritivoAluno->id],
            ['etapa' => 'ASC']
        );

        $gerais = [
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL
        ];

        $componentes = [
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE
        ];

        $parecerDescritivo = $this->getRegraAvaliacaoTipoParecerDescritivo();
        if (in_array($parecerDescritivo, $gerais)) {
            $pareceresGerais = [];

            foreach ($pareceres as $parecer) {
                $pareceresGerais[$parecer->etapa] = $parecer;
            }

            $this->setPareceresGerais($pareceresGerais);
        } elseif (in_array($parecerDescritivo, $componentes)) {
            $pareceresComponentes = [];

            foreach ($pareceres as $parecer) {
                $pareceresComponentes[$parecer->get('componenteCurricular')][] = $parecer;
            }

            $this->setPareceresComponentes($pareceresComponentes);
        }

        return $this;
    }

    /**
     * Verifica se o aluno tem notas lançadas.
     *
     * @return bool
     */
    public function hasNotaAluno()
    {
        if (!is_null($this->_getNotaAluno())) {
            return true;
        }

        return false;
    }

    public function getQtdComponentes($ignorarDispensasParciais = false)
    {
        $enrollment = $this->getOption('matriculaData');

        $total = count(App_Model_IedFinder::getComponentesPorMatricula(
            $enrollment['cod_matricula'],
            $this->getComponenteDataMapper(),
            $this->getComponenteTurmaDataMapper(),
            null,
            null,
            null,
            $enrollment,
            true,
            $ignorarDispensasParciais
        ));

        $disciplinesWithoutStage = Portabilis_Utils_Database::fetchPreparedQuery('
            SELECT COUNT(*) AS count
            FROM pmieducar.escola_serie_disciplina
            WHERE TRUE
            AND ref_ref_cod_serie = $1
            AND ref_ref_cod_escola = $2
            AND ativo = 1
            AND $3 = ANY (anos_letivos)
            AND etapas_especificas = 1
            AND etapas_utilizadas LIKE \'\';
        ', [
            'params' => [
                $enrollment['ref_ref_cod_serie'],
                $enrollment['ref_ref_cod_escola'],
                $enrollment['ano'],
            ]
        ]);

        return $total - (int)$disciplinesWithoutStage[0]['count'];
    }

    /**
     * @param $flagSituacaoNota
     * @param $flagSituacaoFalta
     *
     * @return stdClass
     */
    public function getSituacaoNotaFalta($flagSituacaoNota, $flagSituacaoFalta)
    {
        $situacao = new stdClass();
        $situacao->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
        $situacao->aprovado = true;
        $situacao->andamento = false;
        $situacao->recuperacao = false;
        $situacao->aprovadoComDependencia = false;
        $situacao->retidoFalta = false;

        switch ($flagSituacaoNota) {
            case App_Model_MatriculaSituacao::EM_ANDAMENTO:
                $situacao->aprovado = false;
                $situacao->andamento = true;
                break;
            case App_Model_MatriculaSituacao::APROVADO_APOS_EXAME:
                $situacao->andamento = false;
                $situacao->recuperacao = true;
                break;
            case App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA:
                $situacao->aprovadoComDependencia = true;
                break;
            case App_Model_MatriculaSituacao::EM_EXAME:
                $situacao->aprovado = false;
                $situacao->andamento = true;
                $situacao->recuperacao = true;
                break;
            case App_Model_MatriculaSituacao::REPROVADO:
                $situacao->aprovado = false;
                break;
        }

        switch ($flagSituacaoFalta) {
            case App_Model_MatriculaSituacao::EM_ANDAMENTO:
                $situacao->aprovado = false;
                $situacao->andamento = true;
                break;
            case App_Model_MatriculaSituacao::REPROVADO:
                $situacao->retidoFalta = true;
                $andamento = false;

                // Permite o lançamento de nota de exame final, mesmo que o aluno
                // esteja retido por falta, apenas quando a regra de avaliação possuir
                // uma média para recuperação (exame final).

                if ($this->hasRegraAvaliacaoMediaRecuperacao()) {
                    if ($this->getRegraAvaliacaoTipoNota() != RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
                        // Mesmo se reprovado por falta, só da a situação final após o lançamento de todas as notas
                        $situacoesFinais = App_Model_MatriculaSituacao::getSituacoesFinais();
                        $andamento = in_array($flagSituacaoNota, $situacoesFinais) === false;
                    }

                    if ($flagSituacaoNota == App_Model_MatriculaSituacao::EM_EXAME) {
                        $andamento = true;
                    }
                }

                $situacao->andamento = $andamento;
                break;

            case App_Model_MatriculaSituacao::APROVADO:
                $situacao->retidoFalta = false;
                break;
            default:
                $situacao->andamento = true;
        }

        // seta situacao geral
        if ($situacao->andamento and $situacao->recuperacao) {
            $situacao->situacao = App_Model_MatriculaSituacao::EM_EXAME;
        } elseif (!$situacao->andamento and (!$situacao->aprovado || $situacao->retidoFalta)) {
            $situacao->situacao = App_Model_MatriculaSituacao::REPROVADO;
        } elseif (!$situacao->andamento and $situacao->aprovado and $situacao->recuperacao) {
            $situacao->situacao = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;
        } elseif (!$situacao->andamento and $situacao->aprovado and $situacao->aprovadoComDependencia) {
            $situacao->situacao = App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA;
        } elseif (!$situacao->andamento and $situacao->aprovado) {
            $situacao->situacao = App_Model_MatriculaSituacao::APROVADO;
        }

        return $situacao;
    }

    /**
     * Retorna a situação geral do aluno, levando em consideração as situações
     * das notas (médias) e faltas. O retorno é baseado em booleanos, indicando
     * se o aluno está aprovado, em andamento, em recuperação ou retido por falta.
     *
     * Retorna também a situação das notas e faltas tais quais retornadas pelos
     * métodos getSituacaoComponentesCurriculares() e getSituacaoFaltas().
     *
     * <code>
     * <?php
     * $situacao = new stdClass();
     * $situacao->aprovado    = TRUE;
     * $situacao->andamento   = FALSE;
     * $situacao->recuperacao = FALSE;
     * $situacao->retidoFalta = FALSE;
     * $situacao->nota        = $this->getSituacaoComponentesCurriculares();
     * $situacao->falta       = $this->getSituacaoFaltas();
     * </code>
     *
     * @return stdClass
     *
     * @see Avaliacao_Service_Boletim#getSituacaoComponentesCurriculares()
     * @see Avaliacao_Service_Boletim#getSituacaoFaltas()
     */
    public function getSituacaoAluno()
    {
        $situacaoNotas = $this->getSituacaoNotas(true);
        $situacaoFaltas = $this->getSituacaoFaltas();

        if ($this->allowsApproveWithDependence($situacaoNotas->situacao)) {
            $situacaoNotas->situacao = App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA;
        }

        $situacao = $this->getSituacaoNotaFalta($situacaoNotas->situacao, $situacaoFaltas->situacao);
        $situacao->nota = $situacaoNotas;
        $situacao->falta = $situacaoFaltas;

        return $situacao;
    }

    /**
     * @param $enrollmentId
     * @param $classroomId
     * @param $disciplineId
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function getLastStage($enrollmentId, $classroomId, $disciplineId)
    {
        $stages = range(1, $this->getOption('etapas'));
        $exemptedStages = $this->getExemptedStages($enrollmentId, $disciplineId);

        if ($this->getRegra()->get('definirComponentePorEtapa') == '1') {
            $stages = App_Model_IedFinder::getEtapasComponente($classroomId, $disciplineId) ?? $stages;
        }

        $stages = array_diff($stages, $exemptedStages);

        return max($stages);
    }

    /**
     * @param $notaAlunoId
     *
     * @throws Exception
     */
    private function deleteNotaComponenteCurricularMediaWithoutNotas($notaAlunoId)
    {
        Portabilis_Utils_Database::fetchPreparedQuery('
            DELETE FROM modules.nota_componente_curricular_media
            WHERE nota_aluno_id = $1
            AND NOT EXISTS(
                SELECT 1
                FROM modules.nota_componente_curricular
                WHERE nota_componente_curricular_media.nota_aluno_id = nota_componente_curricular.nota_aluno_id
                AND nota_componente_curricular_media.componente_curricular_id = nota_componente_curricular.componente_curricular_id
            )
        ', [
            'params' => [
                $notaAlunoId,
            ]
        ]);
    }

    /**
     * Retorna a situação das notas lançadas para os componentes curriculares cursados pelo aluno. Possui
     * uma flag "situacao" global, que indica a situação global do aluno, podendo
     * ser:
     *
     * - Em andamento
     * - Em exame
     * - Aprovado
     * - Reprovado
     *
     * Esses valores são definidos no enum App_Model_MatriculaSituacao.
     *
     * Para cada componente curricular, será indicado a situação do aluno no
     * componente.
     *
     * Esses resultados são retornados como um objeto stdClass que possui dois
     * atributos: "situacao" e "componentesCurriculares". O primeiro é um tipo
     * inteiro e o segundo um array indexado pelo id do componente e com um
     * atributo inteiro "situacao":
     *
     * <code>
     * <?php
     * $situacao = new stdClass();
     * $situacao->situacao = App_Model_MatriculaSituacao::APROVADO;
     * $situacao->componentesCurriculares = [;
     * $situacao->componentesCurriculares[1] = new stdClass();
     * $situacao->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::APROVADO;
     * </code>
     *
     * Esses valores são definidos SOMENTE através da verificação das médias dos
     * componentes curriculares já avaliados.
     *
     * Obs: Anteriormente este metódo se chamava getSituacaoComponentesCurriculares, porem na verdade não retornava a
     *      situação dos componentes curriculares (que seria a situação baseada nas notas e das faltas lançadas) e sim
     *      então foi renomeado este metodo para getSituacaoNotas, para que no metódo getSituacaoComponentesCurriculares
     *      fosse retornado a situação do baseada nas notas e faltas lançadas.
     *
     * Obs2: A opção $calcularSituacaoAluno é passada apenas ao calcular a situação geral da matrícula, pois desabilita
     * algumas verificações que não são necessárias para essa validação, mas que podem ser úteis em outras situações, como
     * ao calcular a situação de um componente específico
     *
     * @return stdClass|NULL Retorna NULL caso não
     *
     * @see App_Model_MatriculaSituacao
     */
    public function getSituacaoNotas($calcularSituacaoAluno = false)
    {
        $situacao = new stdClass();
        $situacao->situacao = 0;
        $situacao->componentesCurriculares = [];

        $infosMatricula = $this->getOption('matriculaData');
        $matriculaId = $infosMatricula['cod_matricula'];

        // Carrega as médias pois este método pode ser chamado após a chamada a saveNotas()
        $mediasComponentes = $this->_loadMedias()->getMediasComponentes();

        // Mantém apenas lançamentos para componentes da matrícula
        $componentesMatricula = App_Model_IedFinder::getComponentesPorMatricula($matriculaId, null, null, null, $this->getOption('etapaAtual'), $this->getOption('ref_cod_turma'), null, true, true);
        $mediasComponentes = array_intersect_key($mediasComponentes, $componentesMatricula);

        $mediasComponenentesTotal = $mediasComponentes;

        if (!$calcularSituacaoAluno) {
            $componentes = $this->getComponentes();
            $calculaComponenteAgrupado = !empty(array_intersect_key(array_flip($this->codigoDisciplinasAglutinadas()), $componentes));
            if (!$calculaComponenteAgrupado) {
                $mediasComponentes = array_intersect_key($mediasComponentes, $componentes);
            }
        }

        $disciplinaDispensadaTurma = clsPmieducarTurma::getDisciplinaDispensada($this->getOption('ref_cod_turma'));

        // A situação é "aprovado" por padrão
        $situacaoGeral = App_Model_MatriculaSituacao::APROVADO;

        if ($this->getRegraAvaliacaoTipoNota() == RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
            $situacao->situacao = App_Model_MatriculaSituacao::APROVADO;

            return $situacao;
        }

        if ($this->getRegraAvaliacaoNotaGeralPorEtapa() == '1') {
            $mediaGeral = $this->getMediaGeral();

            if ($this->getRegraAvaliacaoTipoNota() == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA) {
                $media = $mediaGeral->mediaArredondada;
            } else {
                $media = $mediaGeral->media;
            }

            $etapa = $mediaGeral->etapa;

            if ($etapa == $this->getOption('etapas') && $media < $this->getRegraAvaliacaoMedia() && $this->hasRegraAvaliacaoFormulaRecuperacao()) {
                $situacaoGeral = App_Model_MatriculaSituacao::EM_EXAME;
            } elseif ($etapa == $this->getOption('etapas') && $media < $this->getRegraAvaliacaoMedia()) {
                $situacaoGeral = App_Model_MatriculaSituacao::REPROVADO;
            } elseif ($etapa == 'Rc' && $media < $this->getRegraAvaliacaoMediaRecuperacao()) {
                $situacaoGeral = App_Model_MatriculaSituacao::REPROVADO;
            } elseif ($etapa == 'Rc' && $media >= $this->getRegraAvaliacaoMediaRecuperacao() && $this->hasRegraAvaliacaoFormulaRecuperacao()) {
                $situacaoGeral = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;
            } elseif ($etapa < $this->getOption('etapas') && $etapa != 'Rc') {
                $situacaoGeral = App_Model_MatriculaSituacao::EM_ANDAMENTO;
            } else {
                $situacaoGeral = App_Model_MatriculaSituacao::APROVADO;
            }

            foreach ($mediasComponentes as $id => $mediaComponente) {
                $situacao->componentesCurriculares[$id] = new stdClass();
                $situacao->componentesCurriculares[$id]->situacao = $situacaoGeral;
            }

            $situacao->situacao = $situacaoGeral;

            return $situacao;
        }

        if (is_numeric($disciplinaDispensadaTurma)) {
            if (is_array($componentes)) {
                unset($componentes[$disciplinaDispensadaTurma]);
            }

            unset($mediasComponentes[$disciplinaDispensadaTurma]);
        }

        $totalComponentes = $this->getQtdComponentes($calcularSituacaoAluno);

        if (!$calcularSituacaoAluno) {
            // Se não tiver nenhuma média ou a quantidade for diferente dos componentes
            // curriculares da matrícula, ainda está em andamento
            if ((0 == count($mediasComponentes) || count($mediasComponentes) != count($componentes))
                && $this->getRegraAvaliacaoDefinirComponentePorEtapa() != '1') {
                $situacaoGeral = App_Model_MatriculaSituacao::EM_ANDAMENTO;
            }
        } elseif ($calcularSituacaoAluno && count($mediasComponenentesTotal) < $totalComponentes) {
            $situacaoGeral = App_Model_MatriculaSituacao::EM_ANDAMENTO;
        }

        if ((0 == count($mediasComponentes) || count($mediasComponenentesTotal) < $totalComponentes)
            && $this->getRegraAvaliacaoDefinirComponentePorEtapa() == '1') {
            $situacaoGeral = App_Model_MatriculaSituacao::EM_ANDAMENTO;
        }

        $qtdComponenteReprovado = 0;
        $qtdComponentes = 0;
        $somaMedias = 0;
        $media = 0;
        $turmaId = $this->getOption('ref_cod_turma');
        $codigosAglutinados = $this->codigoDisciplinasAglutinadas();

        foreach ($mediasComponentes as $id => $mediaComponente) {
            $mediaComponente = $mediaComponente[0];
            $etapa = $mediaComponente->etapa;
            $qtdComponentes++;
            $media = $this->valorMediaSituacao($mediaComponente);
            $somaMedias += $media;

            $lastStage = $this->getLastStage($matriculaId, $turmaId, $id);

            if ($this->getRegraAvaliacaoTipoProgressao() == RegraAvaliacao_Model_TipoProgressao::CONTINUADA) {
                $getCountNotaCC = App_Model_IedFinder::verificaSeExisteNotasComponenteCurricular($matriculaId, $id);

                if ($getCountNotaCC[0]['cc'] == 0) {
                    $etapa = 0;
                }

                if ($etapa < $lastStage && (string)$etapa != 'Rc') {
                    $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
                    $situacaoGeral = App_Model_MatriculaSituacao::EM_ANDAMENTO;
                } else {
                    $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::APROVADO;
                    $situacaoGeral = App_Model_MatriculaSituacao::APROVADO;
                }

                continue;
            }

            $situacaoAtualComponente = $mediaComponente->situacao;
            $permiteSituacaoEmExame = true;

            if ($situacaoAtualComponente == App_Model_MatriculaSituacao::REPROVADO ||
                $situacaoAtualComponente == App_Model_MatriculaSituacao::APROVADO) {
                $permiteSituacaoEmExame = false;
            }

            if ($etapa == $lastStage && $media < $this->getRegraAvaliacaoMedia() && $this->hasRegraAvaliacaoFormulaRecuperacao() && $permiteSituacaoEmExame) {
                // lets make some changes here >:)
                $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

                if ($this->hasRegraAvaliacaoReprovacaoAutomatica()) {
                    $previsaoRecuperacao = $this->preverNotaRecuperacao($id);
                    if (is_numeric($previsaoRecuperacao) && ($previsaoRecuperacao == '+' . $this->getRegraAvaliacaoNotaMaximaExameFinal())) {
                        $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::REPROVADO;
                        if ($this->exibeSituacao($id)) {
                            $qtdComponenteReprovado++;
                        }
                    }
                }
            } elseif ($etapa == $lastStage && $media < $this->getRegraAvaliacaoMedia()) {
                if ($this->exibeSituacao($id)) {
                    $qtdComponenteReprovado++;
                }
                $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::REPROVADO;
            } elseif ((string)$etapa == 'Rc' && $media < $this->getRegraAvaliacaoMediaRecuperacao()) {
                if ($this->exibeSituacao($id)) {
                    $qtdComponenteReprovado++;
                }
                $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::REPROVADO;
            } elseif ((string)$etapa == 'Rc' && $media >= $this->getRegraAvaliacaoMediaRecuperacao() && $this->hasRegraAvaliacaoFormulaRecuperacao()) {
                $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;
            } elseif ($etapa < $lastStage && (string)$etapa != 'Rc') {
                $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
            } else {
                $situacao->componentesCurriculares[$id]->situacao = App_Model_MatriculaSituacao::APROVADO;
            }

            if ($this->_situacaoPrioritaria(
                $situacao->componentesCurriculares[$id]->situacao,
                $situacaoGeral
            )) {
                $situacaoGeral = $situacao->componentesCurriculares[$id]->situacao;
            }
        }

        // Copia situação da primeira disciplina para o restante
        foreach ($codigosAglutinados as $id) {
            $situacao->componentesCurriculares[$id]->situacao = $situacao->componentesCurriculares[$codigosAglutinados[0]]->situacao;
        }

        if ($situacaoGeral == App_Model_MatriculaSituacao::REPROVADO
            && $this->hasRegraAvaliacaoAprovaMediaDisciplina()
            && ($somaMedias / $qtdComponentes) >= $this->getRegraAvaliacaoMediaRecuperacao()) {
            $situacaoGeral = App_Model_MatriculaSituacao::APROVADO;
        }

        // Situação geral
        $situacao->situacao = $situacaoGeral;

        return $situacao;
    }

    /**
     * Retorna a situação das faltas do aluno, sejam por componentes curriculares
     * ou gerais. A situação pode ser:
     *
     * - Em andamento
     * - Aprovado
     * - Reprovado
     *
     * Retorna outros dados interessantes, a maioria informacional para exibição
     * ao usuário, como a carga horária (geral e por componente), a porcentagem
     * de presença (geral e por componente), a porcentagem de falta (geral e
     * por componente), a hora/falta usada para o cálculo das porcentagens e o
     * total de faltas geral.
     *
     * Esses resultados são retornados como um objeto stdClass que possui os
     * seguintes atributos:
     *
     * <code>
     * <?php
     * $presenca = new stdClass();
     * $presenca->situacao                 = 0;
     * $presenca->tipoFalta                = 0;
     * $presenca->cargaHoraria             = 0;
     * $presenca->cursoHoraFalta           = 0;
     * $presenca->totalFaltas              = 0;
     * $presenca->horasFaltas              = 0;
     * $presenca->porcentagemFalta         = 0;
     * $presenca->porcentagemPresenca      = 0;
     * $presenca->porcentagemPresencaRegra = 0;
     *
     * $presenca->componentesCurriculares  = [;
     * $presenca->componentesCurriculares[1] = new stdClass();
     * $presenca->componentesCurriculares[1]->situacao            = 0;
     * $presenca->componentesCurriculares[1]->horasFaltas         = 0;
     * $presenca->componentesCurriculares[1]->porcentagemFalta    = 0;
     * $presenca->componentesCurriculares[1]->porcentagemPresenca = 0;
     * </code>
     *
     * Esses valores são calculados SOMENTE através das faltas já lançadas.
     *
     * @return stdClass
     *
     * @todo Verificação de situação geral nos moldes dos componentes curriculares
     *   para falta por componente (se 0 ou diferente de componentes matrícula)
     */
    public function getSituacaoFaltas()
    {
        $presenca                           = new stdClass();
        $presenca->totalFaltas              = 0;
        $presenca->horasFaltas              = 0;
        $presenca->porcentagemFalta         = 0;
        $presenca->porcentagemPresenca      = 0;
        $presenca->porcentagemPresencaRegra = $this->getRegraAvaliacaoPorcentagemPresenca();

        $presenca->tipoFalta                = $this->getRegraAvaliacaoTipoPresenca();
        $presenca->cargaHoraria             = $this->getOption('serieCargaHoraria');
        $presenca->diasLetivos              = $this->getOption('serieDiasLetivos');

        $presenca->cursoHoraFalta           = $this->getOption('cursoHoraFalta');
        $presenca->componentesCurriculares  = [];
        $presenca->situacao                 = App_Model_MatriculaSituacao::EM_ANDAMENTO;

        $etapa                              = 0;
        $faltasComponentes                  = [];

        $enrollmentData = $this->getOption('matriculaData');
        $enrollmentId = $enrollmentData['cod_matricula'];
        $classroomId = $this->getOption('ref_cod_turma');

        $componentes = $this->getComponentes();

        $disciplinaDispensadaTurma = clsPmieducarTurma::getDisciplinaDispensada($classroomId);

        if (is_numeric($disciplinaDispensadaTurma)) {
            unset($componentes[$disciplinaDispensadaTurma]);
        }

        // Carrega faltas lançadas (persistidas)
        $this->_loadFalta();

        $tipoFaltaGeral         = $presenca->tipoFalta == RegraAvaliacao_Model_TipoPresenca::GERAL;
        $tipoFaltaPorComponente = $presenca->tipoFalta == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE;

        if ($tipoFaltaGeral) {
            $faltas = $this->getFaltasGerais();

            if (0 == count($faltas)) {
                $total = 0;
                $etapa = 0;
            } else {
                $total = array_sum(CoreExt_Entity::entityFilterAttr($faltas, 'id', 'quantidade'));
                $etapa = array_pop($faltas)->etapa;
            }
        } elseif ($tipoFaltaPorComponente) {
            $faltas = $this->getFaltasComponentes();
            $faltas = array_intersect_key($faltas, $componentes);
            $total  = 0;
            $etapasComponentes = [];
            $faltasComponentes = [];

            foreach ($faltas as $key => $falta) {
                // Total de faltas do componente
                $componenteTotal = array_sum(CoreExt_Entity::entityFilterAttr(
                    $falta,
                    'id',
                    'quantidade'
                ));

                // Pega o id de ComponenteCurricular_Model_Componente da última etapa do array
                $componenteEtapa = array_pop($falta);
                $id              = $componenteEtapa->get('componenteCurricular');
                $etapa           = $componenteEtapa->etapa;

                // Usa stdClass como interface de acesso
                $faltasComponentes[$id] = new stdClass();
                $faltasComponentes[$id]->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
                $faltasComponentes[$id]->horasFaltas = null;
                $faltasComponentes[$id]->porcentagemFalta = null;
                $faltasComponentes[$id]->porcentagemPresenca = null;
                $faltasComponentes[$id]->total = $componenteTotal;

                // Calcula a quantidade de horas/faltas no componente
                $faltasComponentes[$id]->horasFaltas =
                    $this->_calculateHoraFalta($componenteTotal, $presenca->cursoHoraFalta);

                // Calcula a porcentagem de falta no componente
                $faltasComponentes[$id]->porcentagemFalta =
                    $this->_calculatePorcentagem(
                        $componentes[$id]->cargaHoraria,
                        $faltasComponentes[$id]->horasFaltas,
                        false
                    );

                // Calcula a porcentagem de presença no componente
                $faltasComponentes[$id]->porcentagemPresenca =
                    100 - $faltasComponentes[$id]->porcentagemFalta;

                // Na última etapa seta situação presença como aprovado ou reprovado.
                $lastStage = $this->getLastStage($enrollmentId, $classroomId, $id);
                if ($etapa == $lastStage || $etapa == 'Rc') {
                    $aprovado = ($faltasComponentes[$id]->porcentagemPresenca >= $this->getRegraAvaliacaoPorcentagemPresenca());
                    $faltasComponentes[$id]->situacao = $aprovado ? App_Model_MatriculaSituacao::APROVADO :
                        App_Model_MatriculaSituacao::REPROVADO;
                    // Se etapa = quantidade de etapas dessa disciplina, vamos assumir que é a última etapa
                    // já que essa variável só tem o intuito de dizer que todas etapas da disciplina estão lançadas
                    $etapasComponentes[$this->getOption('etapas')] = $this->getOption('etapas');
                } else {
                    $etapasComponentes[$etapa] = $etapa;
                }

                // Adiciona a quantidade de falta do componente ao total geral de faltas
                $total += $componenteTotal;
            }

            if (0 == count($faltasComponentes) ||
                count($faltasComponentes) != count($componentes)) {
                $etapa = 1;
            } else {
                $etapa = min($etapasComponentes);
            }
        } // fim if por_componente

        $presenca->totalFaltas = $total;
        $presenca->horasFaltas = $this->_calculateHoraFalta($total, $presenca->cursoHoraFalta);

        if ($tipoFaltaGeral) {
            $presenca->porcentagemFalta = $this->_calculatePorcentagem(
                $presenca->diasLetivos,
                $presenca->totalFaltas,
                false
            );
        } elseif ($tipoFaltaPorComponente) {
            $presenca->porcentagemFalta = $this->_calculatePorcentagem(
                $presenca->cargaHoraria,
                $presenca->horasFaltas,
                false
            );
        }

        $presenca->porcentagemPresenca     = 100 - $presenca->porcentagemFalta;
        $presenca->componentesCurriculares = $faltasComponentes;

        // Na última etapa seta situação presença como aprovado ou reprovado.
        if ($etapa == $this->getOption('etapas') || $etapa === 'Rc') {
            $aprovado           = (
                $presenca->porcentagemPresenca >= $this->getRegraAvaliacaoPorcentagemPresenca() ||
                $this->getRegraAvaliacaoTipoProgressao() == RegraAvaliacao_Model_TipoProgressao::CONTINUADA
            );
            $presenca->situacao = $aprovado ? App_Model_MatriculaSituacao::APROVADO :
                App_Model_MatriculaSituacao::REPROVADO;
        }

        return $presenca;
    }

    /**
     * Retorna array de etapa => nota, considerando regra de aglutinaçãoo quando padronizado
     *
     * @return array
     */
    private function calculaEtapaNotasAglutinada(int $componenteCurricularId, array $notasComponentes) : array
    {
        $codigos = $this->codigoDisciplinasAglutinadas();
        if (empty($codigos) || !in_array($componenteCurricularId, $codigos)) {
            return CoreExt_Entity::entityFilterAttr($notasComponentes[$componenteCurricularId], 'etapa', 'nota');
        }

        $somaEtapaNotas = [];
        foreach ($codigos as $codigo) {
            if (!isset($notasComponentes[$codigo])) {
                continue;
            }

            $etapaNotas = CoreExt_Entity::entityFilterAttr($notasComponentes[$codigo], 'etapa', 'nota');
            foreach ($etapaNotas as $etapa => $nota) {
                $somaEtapaNotas[$etapa] = ($somaEtapaNotas[$etapa] ?? 0) + $nota;
            }
        }

        return $somaEtapaNotas;
    }

    public function exibeSituacao($componenteCurricularId) : bool
    {
        return $this->exibeNotaNecessariaExame($componenteCurricularId);
    }

    public function exibeNotaNecessariaExame($componenteCurricularId) : bool
    {
        $codigos = $this->codigoDisciplinasAglutinadas();

        return empty($codigos) || !in_array($componenteCurricularId, $codigos) || $componenteCurricularId == $this->codigoDisciplinasAglutinadas()[0];;
    }

    /**
     * Retorna o valor da média considerado para calculo de situação conforme regra
     *
     * @return float
     */
    private function valorMediaSituacao(Avaliacao_Model_NotaComponenteMedia $mediaComponente)
    {
        $regraNotaNumerica = $this->getRegraAvaliacaoTipoNota() == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA;

        return $regraNotaNumerica ? $mediaComponente->mediaArredondada : $mediaComponente->media;
    }

    /**
     * Retorna a situação dos componentes curriculares cursados pelo aluno. Possui
     * uma flag "situacao" global, que indica a situação global do aluno, podendo
     * ser:
     *
     * - Em andamento
     * - Em exame
     * - Aprovado
     * - Reprovado
     *
     * Esses valores são definidos no enum App_Model_MatriculaSituacao.
     *
     * Para cada componente curricular, será indicado a situação do aluno no
     * componente.
     *
     * Esses resultados são retornados como um objeto stdClass que possui dois
     * atributos: "situacao" e "componentesCurriculares". O primeiro é um tipo
     * inteiro e o segundo um array indexado pelo id do componente e com um
     * atributo inteiro "situacao":
     *
     * <code>
     * <?php
     * $situacao = new stdClass();
     * $situacao->situacao = App_Model_MatriculaSituacao::APROVADO;
     * $situacao->componentesCurriculares = [;
     * $situacao->componentesCurriculares[1] = new stdClass();
     * $situacao->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::APROVADO;
     * </code>
     *
     * Esses valores são definidos através da verificação das médias dos
     * componentes curriculares já avaliados e das faltas lançadas.
     *
     * Obs: Anteriormente este metódo SOMENTE verificava a situação baseando-se nas médias lançadas,
     *      porem o mesmo foi alterado para verificar a situação baseada nas notas e faltas lançadas.
     *
     *      A implementa antiga deste metodo esta contida no metodo getSituacaoNotas
     *
     * @return stdClass|NULL Retorna NULL caso não
     *
     * @see App_Model_MatriculaSituacao
     */
    public function getSituacaoComponentesCurriculares()
    {
        $situacao = new stdClass();
        $situacao->situacao = App_Model_MatriculaSituacao::APROVADO;
        $situacao->componentesCurriculares = [];

        $componentes = $this->getComponentes();
        $situacaoNotas = $this->getSituacaoNotas();
        $situacaoFaltas = $this->getSituacaofaltas();

        foreach ($componentes as $ccId => $componente) {
            // seta tipos nota, falta
            $tipoNotaNenhum = $this->getRegraAvaliacaoTipoNota() == RegraAvaliacao_Model_Nota_TipoValor::NENHUM;
            $tipoFaltaPorComponente = $this->getRegraAvaliacaoTipoPresenca() == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE;

            $situacaoNotaCc = $situacaoNotas->componentesCurriculares[$ccId] ?? null;

            // inicializa 0FaltaCc a ser usado caso tipoFaltaPorComponente
            $situacaoFaltaCc = new stdClass();
            $situacaoFaltaCc->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;

            // caso possua situacaoFalta para o componente substitui situacao inicializada
            if ($tipoFaltaPorComponente and isset($situacaoFaltas->componentesCurriculares[$ccId])) {
                $situacaoFaltaCc = $situacaoFaltas->componentesCurriculares[$ccId];
            }

            // pega situação nota geral ou do componente
            if ($tipoNotaNenhum) {
                $situacaoNota = $situacaoNotas->situacao;
            } else {
                $situacaoNota = $situacaoNotaCc->situacao;
            }

            // pega situacao da falta componente ou geral.
            if ($tipoFaltaPorComponente) {
                $situacaoFalta = $situacaoFaltas->componentesCurriculares[$ccId]->situacao;
            } else {
                $situacaoFalta = $situacaoFaltas->situacao;
            }

            if (is_null($situacaoNota)) {
                $situacaoNota = App_Model_MatriculaSituacao::EM_ANDAMENTO;
            }

            $situacao->componentesCurriculares[$ccId] = $this->getSituacaoNotaFalta($situacaoNota, $situacaoFalta);
        }

        return $situacao;
    }

    /**
     * Verifica se uma determinada situação tem prioridade sobre a outra.
     *
     * @param int $item1
     * @param int $item2
     *
     * @return bool
     */
    protected function _situacaoPrioritaria($item1, $item2)
    {
        return ($this->_situacaoPrioridade[$item1] <= $this->_situacaoPrioridade[$item2]);
    }

    /**
     * Cria e persiste uma instância de Avaliacao_Model_NotaAluno.
     *
     * @return bool
     */
    protected function _createNotaAluno()
    {
        $notaAluno = new Avaliacao_Model_NotaAluno();
        $notaAluno->matricula = $this->getOption('matricula');

        return $this->getNotaAlunoDataMapper()->save($notaAluno);
    }

    /**
     * Verifica se existe alguma instância de Avaliacao_Model_NotaComponente para
     * um determinado componente curricular já persistida.
     *
     * @param int $id
     *
     * @return bool
     */
    protected function _hasNotaComponente($id)
    {
        $notasComponentes = $this->getNotasComponentes();

        if (!isset($notasComponentes[$id])) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se existe uma nota geral lançada com aquele id
     *
     * @param int $id
     *
     * @return bool
     */
    protected function _hasNotaGeral($id)
    {
        $notasGerais = $this->getNotasGerais();

        if (!isset($notasGerais[$id])) {
            return false;
        }

        return true;
    }

    /**
     * Retorna o field identity de um componente curricular de uma instância de
     * Avaliacao_Model_NotaComponente já esteja persistida.
     *
     * @param Avaliacao_Model_NotaComponente $instance
     *
     * @return int|NULL Retorna NULL caso a instância não tenha sido lançada
     */
    protected function _getNotaIdEtapa(Avaliacao_Model_NotaComponente $instance)
    {
        $componenteCurricular = $instance->get('componenteCurricular');

        if (!$this->_hasNotaComponente($componenteCurricular)) {
            return null;
        }

        $notasComponentes = $this->getNotasComponentes();

        foreach ($notasComponentes[$componenteCurricular] as $notaComponente) {
            if ($instance->etapa == $notaComponente->etapa) {
                return $notaComponente->id;
            }
        }

        return null;
    }

    /**
     * Retorna o id de uma nota já lançada, retorna null caso não seja encontrada
     */
    protected function _getNotaGeralIdEtapa(Avaliacao_Model_NotaGeral $instance)
    {
        $notasGerais = $this->getNotasGerais();

        foreach ($notasGerais as $notaGeral) {
            if ($instance->etapa == $notaGeral->etapa) {
                return $notaGeral->id;
            }
        }

        return null;
    }

    /**
     * Verifica se o aluno tem faltas lançadas.
     *
     * @return bool
     */
    public function hasFaltaAluno()
    {
        if (!is_null($this->_getFaltaAluno())) {
            return true;
        }

        return false;
    }

    /**
     * Cria e persiste uma instância de Avaliacao_Model_NotaAluno.
     *
     * @return bool
     */
    protected function _createFaltaAluno()
    {
        $faltaAluno = new Avaliacao_Model_FaltaAluno();
        $faltaAluno->matricula = $this->getOption('matricula');
        $faltaAluno->tipoFalta = $this->getRegraAvaliacaoTipoPresenca();

        return $this->getFaltaAlunoDataMapper()->save($faltaAluno);
    }

    /**
     * Verifica se existe alguma instância de Avaliacao_Model_FaltaGeral já
     * persistida.
     *
     * @return bool
     */
    protected function _hasFaltaGeral()
    {
        $faltasGerais = $this->getFaltasGerais();

        if (0 == count($faltasGerais)) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se existe alguma instância de Avaliacao_Model_FaltaComponente para
     * um determinado componente curricular já persistida.
     *
     * @param int $id
     *
     * @return bool
     */
    protected function _hasFaltaComponente($id)
    {
        $faltasComponentes = $this->getFaltasComponentes();

        if (!isset($faltasComponentes[$id])) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se existe alguma instância de Avaliacao_Model_FaltaAbstract já
     * persistida em uma determinada etapa e retorna o field identity.
     *
     * @param Avaliacao_Model_FaltaAbstract $instance
     *
     * @return int|NULL
     */
    protected function _getFaltaIdEtapa(Avaliacao_Model_FaltaAbstract $instance)
    {
        $etapa = $instance->etapa;

        if (!is_null($instance) &&
            $this->_getFaltaAluno()->get('tipoFalta') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $componenteCurricular = $instance->get('componenteCurricular');

            if (!$this->_hasFaltaComponente($componenteCurricular)) {
                return null;
            }

            $faltasComponentes = $this->getFaltasComponentes();

            foreach ($faltasComponentes[$componenteCurricular] as $faltaComponente) {
                if ($etapa == $faltaComponente->etapa) {
                    return $faltaComponente->id;
                }
            }
        } elseif ($this->_getFaltaAluno()->get('tipoFalta') == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            if (!$this->_hasFaltaGeral()) {
                return null;
            }

            $faltasGerais = $this->getFaltasGerais();

            if (isset($faltasGerais[$etapa])) {
                return $faltasGerais[$etapa]->id;
            }
        }

        return null;
    }

    /**
     * Verifica se o aluno tem pareceres lançados.
     *
     * @return bool
     */
    public function hasParecerDescritivoAluno()
    {
        if (!is_null($this->_getParecerDescritivoAluno())) {
            return true;
        }

        return false;
    }

    /**
     * Cria e persiste uma instância de Avaliacao_Model_ParecerDescritivoAluno.
     *
     * @return bool
     */
    protected function _createParecerDescritivoAluno()
    {
        $parecerDescritivoAluno = new Avaliacao_Model_ParecerDescritivoAluno();
        $parecerDescritivoAluno->matricula = $this->getOption('matricula');
        $parecerDescritivoAluno->parecerDescritivo = $this->getRegraAvaliacaoTipoParecerDescritivo();

        return $this->getParecerDescritivoAlunoDataMapper()->save($parecerDescritivoAluno);
    }

    /**
     * Adiciona um array de instâncias Avaliacao_Model_NotaComponente.
     *
     * @param array $notas
     *
     * @return Avaliacao_Service_Boletim Provê interface fluída
     */
    public function addNotas(array $notas)
    {
        foreach ($notas as $nota) {
            $this->addNota($nota);
        }

        return $this;
    }

    /**
     * Verifica se existe alguma instância de Avaliacao_Model_ParecerDescritivoComponente
     * persistida para o aluno.
     *
     * @param int $id Field identity de ComponenteCurricular_Model_Componente
     *
     * @return bool
     */
    protected function _hasParecerComponente($id)
    {
        $pareceresComponentes = $this->getPareceresComponentes();

        if (!isset($pareceresComponentes[$id])) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se existe alguma instância de Avaliacao_Model_ParecerDescritivoGeral
     * persistida para o aluno.
     *
     * @return bool
     */
    protected function _hasParecerGeral()
    {
        if (0 == count($this->getPareceresGerais())) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se existe alguma instância de Avaliacao_Model_ParecerDescritivoAbstract
     * persistida em uma determinada etapa e retorna o field identity.
     *
     * @param Avaliacao_Model_ParecerDescritivoAbstract $instance
     *
     * @return int|NULL
     */
    protected function _getParecerIdEtapa(Avaliacao_Model_ParecerDescritivoAbstract $instance)
    {
        $gerais = [
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL
        ];

        $componentes = [
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
        ];

        $parecerDescritivo = $this->getRegraAvaliacaoTipoParecerDescritivo();

        if (in_array($parecerDescritivo, $gerais)) {
            if (!$this->_hasParecerGeral()) {
                return null;
            }

            $pareceres = $this->getPareceresGerais();
        } elseif (in_array($parecerDescritivo, $componentes)) {
            if (!$this->_hasParecerComponente($instance->get('componenteCurricular'))) {
                return null;
            }

            $pareceres = $this->getPareceresComponentes();
            $pareceres = $pareceres[$instance->get('componenteCurricular')];
        }

        foreach ($pareceres as $parecer) {
            if ($instance->etapa == $parecer->etapa) {
                return $parecer->id;
            }
        }
    }

    /**
     * Adiciona notas no boletim.
     *
     * @param Avaliacao_Model_NotaComponente $nota
     *
     * @return Avaliacao_Service_Boletim Provê interface fluída
     */
    public function addNota(Avaliacao_Model_NotaComponente $nota)
    {
        $this->setCurrentComponenteCurricular($nota->get('componenteCurricular'));
        $key = 'n_' . spl_object_hash($nota);
        $nota = $this->_addValidators($nota);
        $nota = $this->_updateEtapa($nota);
        $nota->notaArredondada = $this->arredondaNota($nota);
        $this->addNotaItem($key, $nota);

        return $this;
    }

    /**
     * @param Avaliacao_Model_NotaGeral $notaGeral
     *
     * @return $this
     */
    public function addNotaGeral(Avaliacao_Model_NotaGeral $notaGeral)
    {
        $key = 'ng_' . spl_object_hash($notaGeral);
        $notaGeral = $this->_addValidators($notaGeral);
        $notaGeral = $this->_updateEtapa($notaGeral);
        $notaGeral->notaArredondada = $this->arredondaNota($notaGeral);
        $this->addNotaItem($key, $notaGeral);

        return $this;
    }

    /**
     * Adiciona um array de instâncias Avaliacao_Model_FaltaAbstract no boletim.
     *
     * @param array $faltas
     *
     * @return Avaliacao_Service_Boletim Provê interface fluída
     */
    public function addFaltas(array $faltas)
    {
        foreach ($faltas as $falta) {
            $this->addFalta($falta);
        }

        return $this;
    }

    /**
     * Adiciona faltas no boletim.
     *
     * @param Avaliacao_Model_FaltaAbstract $falta
     *
     * @return Avaliacao_Service_Boletim Provê interface fluída
     */
    public function addFalta(Avaliacao_Model_FaltaAbstract $falta)
    {
        $this->setCurrentComponenteCurricular($this->_componenteCurricularId);
        $key = 'f_' . spl_object_hash($falta);
        $falta = $this->_addValidators($falta);
        $falta = $this->_updateEtapa($falta);
        $this->addFaltaKey($key, $falta);

        return $this;
    }

    /**
     * Adiciona uma array de instâncias de Avaliacao_Model_ParecerDescritivoAbstract
     * no boletim.
     *
     * @param array $pareceres
     *
     * @return Avaliacao_Service_Boletim Provê interface fluída
     */
    public function addPareceres(array $pareceres)
    {
        foreach ($pareceres as $parecer) {
            $this->addParecer($parecer);
        }

        return $this;
    }

    /**
     * Adiciona uma instância de Avaliacao_Model_ParecerDescritivoAbstract no
     * boletim.
     *
     * @param Avaliacao_Model_ParecerDescritivoAbstract $parecer
     *
     * @return Avaliacao_Service_Boletim Provê interface fluída
     */
    public function addParecer(Avaliacao_Model_ParecerDescritivoAbstract $parecer)
    {
        $key = 'p_' . spl_object_hash($parecer);
        $this->addParecerKey($key, $parecer);
        $this->_updateParecerEtapa($parecer);
        $this->_addParecerValidators($parecer);

        return $this;
    }

    /**
     * Atualiza as opções de validação de uma instância de
     * CoreExt_Validate_Validatable, com os valores permitidos para os atributos
     * 'componenteCurricular' e 'etapa'.
     *
     * @param CoreExt_Validate_Validatable $nota
     *
     * @return CoreExt_Validate_Validatable
     *
     * @todo Substituir variável estática por uma de instância {@see _updateParecerEtapa()}
     */
    protected function _addValidators(CoreExt_Validate_Validatable $validatable)
    {
        $validators = [];

        // Como os componentes serão os mesmos, fazemos cache do validador
        if (is_null($this->getValidators())) {
            $componentes = $this->getComponentes();
            $componentes = CoreExt_Entity::entityFilterAttr($componentes, 'id', 'id');

            // Só pode adicionar uma nota/falta para os componentes cursados
            $validators['componenteCurricular'] = new CoreExt_Validate_Choice([
                'choices' => $componentes
            ]);

            // Pode informar uma nota para as etapas
            $etapas = $this->getOption('etapas');
            $etapas = array_merge(range(1, $etapas, 1), ['Rc']);

            $validators['etapa'] = new CoreExt_Validate_Choice([
                'choices' => $etapas
            ]);

            $this->setValidators($validators);
        }

        $validators = $this->getValidators();

        if ($validatable instanceof Avaliacao_Model_NotaComponente || $this->getRegraAvaliacaoTipoPresenca() == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $validatable->setValidator('componenteCurricular', $validators['componenteCurricular']);
        }
        $validatable->setValidator('etapa', $validators['etapa']);

        return $validatable;
    }

    /**
     * Atualiza as opções de validação de uma instância de
     * Avaliacao_Model_ParecerDescritivoAbstract, com os valores permitidos
     * para os atributos 'componenteCurricular' e 'etapa'.
     *
     * @param Avaliacao_Model_ParecerDescritivoAbstract $instance
     *
     * @return Avaliacao_Model_ParecerDescritivoAbstract
     */
    protected function _addParecerValidators(Avaliacao_Model_ParecerDescritivoAbstract $instance)
    {
        if (is_null($this->getParecerValidators())) {
            $validators = [];

            $anuais = [
                RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
                RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE
            ];

            $etapas = [
                RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
                RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
            ];

            $parecerDescritivo = $this->getRegraAvaliacaoTipoParecerDescritivo();

            if (in_array($parecerDescritivo, $anuais)) {
                $validators['etapa'] = new CoreExt_Validate_Choice([
                    'choices' => ['An']
                ]);
            } elseif (in_array($parecerDescritivo, $etapas)) {
                $etapas = $this->getOption('etapas');
                $etapas = array_merge(range(1, $etapas, 1), ['Rc']);

                $validators['etapa'] = new CoreExt_Validate_Choice([
                    'choices' => $etapas
                ]);
            }

            if ($instance instanceof Avaliacao_Model_ParecerDescritivoComponente) {
                $componentes = $this->getComponentes();
                $componentes = CoreExt_Entity::entityFilterAttr($componentes, 'id', 'id');

                $validators['componenteCurricular'] = new CoreExt_Validate_Choice([
                    'choices' => $componentes
                ]);
            }

            // Armazena os validadores na instância
            $this->setParecerValidators($validators);
        }

        $validators = $this->getParecerValidators();

        // Etapas
        $instance->setValidator('etapa', $validators['etapa']);

        // Componentes curriculares
        if ($instance instanceof Avaliacao_Model_ParecerDescritivoComponente) {
            $instance->setValidator('componenteCurricular', $validators['componenteCurricular']);
        }

        return $instance;
    }

    /**
     * Atualiza a etapa de uma instância de Avaliacao_Model_Etapa.
     *
     * @param Avaliacao_Model_Etapa $nota
     *
     * @return Avaliacao_Model_Etapa
     */
    protected function _updateEtapa(Avaliacao_Model_Etapa $instance)
    {
        if (!is_null($instance->etapa)) {
            if ($instance->isValid('etapa')) {
                return $instance;
            } else {
                throw new CoreExt_Exception_InvalidArgumentException('A etapa informada é inválida.');
            }
        }

        $proximaEtapa = 1;

        // Se for falta e do tipo geral, verifica qual foi a última etapa
        if ($instance instanceof Avaliacao_Model_FaltaGeral) {
            if (0 < count($this->getFaltasGerais())) {
                $etapas = CoreExt_Entity::entityFilterAttr($this->getFaltasGerais(), 'id', 'etapa');
                $proximaEtapa = max($etapas) + 1;
            }
        } else {
            // Se for nota ou falta por componente, verifica no conjunto qual a última etapa
            if ($instance instanceof Avaliacao_Model_NotaComponente) {
                $search = '_notasComponentes';
            } elseif ($instance instanceof Avaliacao_Model_FaltaComponente) {
                $search = '_faltasComponentes';
            }

            if (isset($this->{$search}[$instance->get('componenteCurricular')])) {
                $etapas = CoreExt_Entity::entityFilterAttr(
                    $this->{$search}[$instance->get('componenteCurricular')],
                    'id',
                    'etapa'
                );

                $proximaEtapa = max($etapas) + 1;
            }
        }

        // Se ainda estiver dentro dos limites, ok
        if ($proximaEtapa <= $this->getOption('etapas')) {
            $instance->etapa = $proximaEtapa;
        } else {
            // Se for maior, verifica se tem recuperação e atribui etapa como 'Rc'
            if ($proximaEtapa > $this->getOption('etapas')
                && $this->hasRegraAvaliacaoFormulaRecuperacao()) {
                $instance->etapa = 'Rc';
            }
        }

        return $instance;
    }

    /**
     * Atualiza a etapa de uma instância de Avaliacao_Model_ParecerDescritivoAbstract
     * para a última etapa possível.
     *
     * @param Avaliacao_Model_ParecerDescritivoAbstract $instance
     *
     * @return Avaliacao_Model_ParecerDescritivoAbstract
     */
    protected function _updateParecerEtapa(Avaliacao_Model_ParecerDescritivoAbstract $instance)
    {
        if (!is_null($instance->etapa)) {
            if ($instance->isValid('etapa')) {
                return $instance;
            } else {
                throw new CoreExt_Exception_InvalidArgumentException('A etapa informada é inválida.');
            }
        }

        $proximaEtapa = 1;

        $anuais = [
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE
        ];

        $etapas = [
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
        ];

        $componentes = [
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
        ];

        $gerais = [
            RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL
        ];

        $parecerDescritivo = $this->getRegraAvaliacaoTipoParecerDescritivo();
        if (in_array($parecerDescritivo, $anuais)) {
            $instance->etapa = 'An';

            return $instance;
        } else {
            if (in_array($parecerDescritivo, $etapas)) {
                $attrValues = [];

                if (in_array($parecerDescritivo, $gerais)) {
                    $attrValues = $this->getPareceresGerais();
                } else {
                    if (in_array($parecerDescritivo, $componentes)) {
                        $pareceresComponentes = $this->getPareceresComponentes();

                        if (isset($pareceresComponentes[$instance->get('componenteCurricular')])) {
                            $attrValues = $pareceresComponentes[$instance->get('componenteCurricular')];
                        }
                    }
                }

                if (0 < count($attrValues)) {
                    $etapas = CoreExt_Entity::entityFilterAttr(
                        $attrValues,
                        'id',
                        'etapa'
                    );
                    $proximaEtapa = max($etapas) + 1;
                }
            }
        }

        if ($proximaEtapa <= $this->getOption('etapas')) {
            $instance->etapa = $proximaEtapa;
        } else {
            if ($this->hasRegraAvaliacaoFormulaRecuperacao()) {
                $instance->etapa = 'Rc';
            }
        }

        return $instance;
    }

    /**
     * Arredonda uma nota através da tabela de arredondamento da regra de avaliação.
     *
     * @param Avaliacao_Model_NotaComponente|int $nota
     *
     * @return mixed
     *
     * @throws CoreExt_Exception_InvalidArgumentException
     */
    public function arredondaNota($nota)
    {
        $componenteId = $nota->get('componenteCurricular');

        if (($nota instanceof Avaliacao_Model_NotaComponente) || ($nota instanceof Avaliacao_Model_NotaGeral)) {
            $nota = $nota->nota;
        }

        if (!is_numeric($nota)) {
            require_once 'CoreExt/Exception/InvalidArgumentException.php';
            throw new CoreExt_Exception_InvalidArgumentException(sprintf(
                'O parâmetro $nota ("%s") não é um valor numérico.',
                $nota
            ));
        }

        if ($this->usaTabelaArredondamentoConceitual($componenteId)) {
            return $this->getRegraAvaliacaoTabelaArredondamentoConceitual()->round($nota, 1);
        }

        return $this->getRegraAvaliacaoTabelaArredondamento()->round($nota, 1);
    }

    /**
     * @return bool
     */
    public function regraUsaTipoNotaNumericaConceitual()
    {
        if ($this->getRegraAvaliacaoTipoNota() == RegraAvaliacao_Model_Nota_TipoValor::NUMERICACONCEITUAL) {
            return true;
        }

        return false;
    }

    /**
     * @param $componenteId
     *
     * @return bool
     */
    public function componenteUsaNotaConceitual($componenteId)
    {
        $serieId = $this->_options['matriculaData']['ref_ref_cod_serie'];
        $tipoNota = App_Model_IedFinder::getTipoNotaComponenteSerie($componenteId, $serieId);

        if ($tipoNota == ComponenteSerie_Model_TipoNota::CONCEITUAL) {
            return true;
        }

        return false;
    }

    /**
     * @param $componenteId
     *
     * @return bool
     */
    public function usaTabelaArredondamentoConceitual($componenteId)
    {
        return $this->regraUsaTipoNotaNumericaConceitual() && $this->componenteUsaNotaConceitual($componenteId);
    }

    /**
     * Arredonda uma nota através da tabela de arredondamento da regra de avaliação.
     *
     * @param Avaliacao_Model_NotaComponente|int $nota
     *
     * @return mixed
     *
     * @throws CoreExt_Exception_InvalidArgumentException
     */
    public function arredondaMedia($media)
    {
        $componenteId = $this->getCurrentComponenteCurricular();

        if ($media instanceof Avaliacao_Model_NotaComponenteMedia) {
            $media = $media->nota;
        }

        if (!is_numeric($media)) {
            require_once 'CoreExt/Exception/InvalidArgumentException.php';
            throw new CoreExt_Exception_InvalidArgumentException(sprintf(
                'O parâmetro $media ("%s") não é um valor numérico.',
                $media
            ));
        }

        if ($this->usaTabelaArredondamentoConceitual($componenteId)) {
            return $this->getRegraAvaliacaoTabelaArredondamentoConceitual()->round($media, 2);
        }

        //Arredonda média para quantidade de casas decimais permitidas
        $media = round($media, $this->getRegraAvaliacaoQtdCasasDecimais());

        return $this->getRegraAvaliacaoTabelaArredondamento()->round($media, 2);
    }

    /**
     * Prevê a nota necessária para que o aluno seja aprovado após a recuperação
     * escolar.
     *
     * @param int $id
     *
     * @return int|NULL
     *
     * @see    TabelaArredondamento_Model_Tabela#predictValue()
     */
    public function preverNotaRecuperacao($id)
    {
        $turmaId = $this->getOption('ref_cod_turma');
        $infosMatricula = $this->getOption('matriculaData');
        $matriculaId = $infosMatricula['cod_matricula'];
        $serieId = $infosMatricula['ref_ref_cod_serie'];
        $escolaId = $infosMatricula['ref_ref_cod_escola'];

        $notasComponentes = $this->getNotasComponentes();

        if (is_null($this->getRegraAvaliacaoFormulaRecuperacao()) || !isset($notasComponentes[$id])) {
            return null;
        }

        $etapaNotas = $this->calculaEtapaNotasAglutinada($id, $notasComponentes);

        $qtdeEtapas = $this->getOption('etapas');

        if ($this->getRegraAvaliacaoDefinirComponentePorEtapa() == '1') {
            $qtdeEtapaEspecifica = App_Model_IedFinder::getQtdeEtapasComponente($turmaId, $id, $infosMatricula['ref_cod_aluno']);

            $qtdeEtapas = ($qtdeEtapaEspecifica ? $qtdeEtapaEspecifica : $qtdeEtapas);
        }
        $verificaDispensa = App_Model_IedFinder::validaDispensaPorMatricula($matriculaId, $serieId, $escolaId, $id);
        $consideraEtapas = [];

        for ($i = 1; $i <= $qtdeEtapas; $i++) {
            $consideraEtapas['C' . $i] = in_array($i, $verificaDispensa) ? 0 : 1;

            if (in_array($i, $verificaDispensa)) {
                $consideraEtapas['E' . $i] = 0;
            }
        }

        $somaEtapas = array_sum($etapaNotas);

        $data = [
            'Se' => $somaEtapas,
            'Et' => $this->getOption('etapas'),
            'Rc' => null
        ];

        $data = array_merge($data, $consideraEtapas);

        foreach ($etapaNotas as $etapa => $nota) {
            $data['E' . $etapa] = $nota;
        }

        $data = $this->_calculateNotasRecuperacoesEspecificas($id, $data);

        $increment = 0.1;
        $notaMax = $this->getRegraAvaliacaoNotaMaximaExameFinal();

        if ($this->getRegraAvaliacaoQtdCasasDecimais() == 0) {
            $increment = 1;
        }

        // Definida varíavel de incremento e nota máxima, vai testando notas de Recuperação até que o resultado
        // da média seja superior a média de aprovação de recuperação
        for ($i = $increment; $i <= $notaMax; $i = round($i + $increment, 1)) {
            $data['Rc'] = $i;

            if ($this->getRegraAvaliacaoFormulaRecuperacao()->execFormulaMedia($data) >= $this->getRegraAvaliacaoMediaRecuperacao()) {
                return $i;
            }
        }

        return "+{$notaMax}";
    }

    /**
     * Recupera notas e calcula variáveis relacionadas com as recuperações específicas
     *
     * @param int $id
     *
     * @return array $data
     */
    protected function _calculateNotasRecuperacoesEspecificas($id, $data = [])
    {
        // Verifica regras de recuperações (Recuperações específicas por etapa)
        $regrasRecuperacoes = $this->getRegrasRecuperacao();

        $cont = 0;

        if (count($regrasRecuperacoes)) {
            $data['Se'] = 0;
        }

        foreach ($regrasRecuperacoes as $key => $_regraRecuperacao) {
            $cont++;
            $notaRecuperacao = $this->getNotaComponente($id, $_regraRecuperacao->getLastEtapa());

            if ($notaRecuperacao && is_numeric($notaRecuperacao->notaRecuperacaoEspecifica)) {
                // Caso tenha nota de recuperação para regra atual, atribuí variável RE+N
                $substituiMenorNota = (bool)$_regraRecuperacao->substituiMenorNota;
                $data['RSP' . $cont] = $notaRecuperacao->notaRecuperacaoEspecifica;

                $somaEtapasRecuperacao = 0;
                $countEtapasRecuperacao = 0;

                foreach ($_regraRecuperacao->getEtapas() as $__etapa) {
                    $somaEtapasRecuperacao += $data['E' . $__etapa];
                    $countEtapasRecuperacao++;
                }

                $mediaEtapasRecuperacao = $somaEtapasRecuperacao / $countEtapasRecuperacao;
                $mediaEtapasRecuperacaoComRecuperacao = ($mediaEtapasRecuperacao + $notaRecuperacao->notaRecuperacaoEspecifica) / 2;

                if (!$substituiMenorNota) {
                    $data['Se'] += $data['RSP' . $cont] ?? $somaEtapasRecuperacao;
                } else {
                    $data['Se'] += $data['RSP' . $cont] > $mediaEtapasRecuperacao ? $data['RSP' . $cont] * $countEtapasRecuperacao : $somaEtapasRecuperacao;
                }

                // Caso média com recuperação seja maior que média das somas das etapas sem recuperação, atribuí variável MRE+N
                if (!$substituiMenorNota || $mediaEtapasRecuperacaoComRecuperacao > $mediaEtapasRecuperacao) {
                    $data['RSPM' . $cont] = $mediaEtapasRecuperacaoComRecuperacao;
                } else {
                    $data['RSPM' . $cont] = $mediaEtapasRecuperacao;
                }

                // Caso nota de recuperação seja maior que soma das etapas, atribuí variável SRE+N
                if (!$substituiMenorNota || $notaRecuperacao->notaRecuperacaoEspecifica > $somaEtapasRecuperacao) {
                    $data['RSPS' . $cont] = $notaRecuperacao->notaRecuperacaoEspecifica;
                } else {
                    $data['RSPS' . $cont] = $somaEtapasRecuperacao;
                }
            } else {
                // Caso tenha nota de recuperação para regra atual, atribuí variaveis RSPM+N E RSPS+N
                // considerando apenas soma das etapas
                $somaEtapasRecuperacao = 0;
                $countEtapasRecuperacao = 0;

                foreach ($_regraRecuperacao->getEtapas() as $__etapa) {
                    $somaEtapasRecuperacao += $data['E' . $__etapa];
                    $countEtapasRecuperacao++;
                }

                $data['Se'] += $somaEtapasRecuperacao;
                $data['RSPM' . $cont] = $somaEtapasRecuperacao / $countEtapasRecuperacao;
                $data['RSPS' . $cont] = $somaEtapasRecuperacao;
            }
        }

        return $data;
    }

    /**
     * @param numeric $falta
     * @param numeric $horaFalta
     *
     * @return numeric
     */
    protected function _calculateHoraFalta($falta, $horaFalta)
    {
        return ($falta * $horaFalta);
    }

    /**
     * Calcula a proporção de $num2 para $num1.
     *
     * @param numeric $num1
     * @param numeric $num2
     * @param bool    $decimal Opcional. Se o resultado é retornado como decimal
     *                         ou percentual. O padrão é TRUE.
     *
     * @return float
     */
    protected function _calculatePorcentagem($num1, $num2, $decimal = true)
    {
        $num1 = floatval($num1);
        $num2 = floatval($num2);

        if ($num1 == 0) {
            return 0;
        }

        $perc = $num2 / $num1;

        return ($decimal == true ? $perc : ($perc * 100));
    }

    /**
     * Calcula uma média de acordo com uma fórmula de FormulaMedia_Model_Media
     * da regra de avaliação da série/matrícula do aluno.
     *
     * @param array $values
     *
     * @return float
     */
    protected function _calculaMedia(array $values)
    {
        if (isset($values['Rc']) && $this->hasRegraAvaliacaoFormulaRecuperacao()) {
            $media = $this->getRegraAvaliacaoFormulaRecuperacao()->execFormulaMedia($values);
        } else {
            $media = $this->getRegraAvaliacaoFormulaMedia()->execFormulaMedia($values);
        }

        return $media;
    }

    /**
     * Insere ou atualiza as notas e/ou faltas que foram adicionadas ao service
     * e atualiza a matricula do aluno de acordo com a sua performance,
     * promovendo-o ou retendo-o caso o tipo de progressão da regra de avaliação
     * seja automática (e que a situação do aluno não esteja em "andamento").
     *
     * @see Avaliacao_Service_Boletim#getSituacaoAluno()
     *
     * @throws CoreExt_Service_Exception|Exception
     */
    public function save()
    {
        try {
            $this->saveNotas();
            $this->saveFaltas();
            $this->savePareceres();
            $this->promover();
        } catch (CoreExt_Service_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Insere ou atualiza as notas no boletim do aluno.
     *
     * @return Avaliacao_Service_Boletim Provê interface fluída
     */
    public function saveNotas()
    {
        if ($this->getRegraAvaliacaoTipoNota() == RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
            return $this;
        }

        $notaAluno = $this->_getNotaAluno();
        $notas = $this->getNotas();

        foreach ($notas as $nota) {
            $nota->notaAluno = $notaAluno;

            if ($nota instanceof Avaliacao_Model_NotaComponente) {
                $nota->id = $this->_getNotaIdEtapa($nota);
                $this->getNotaComponenteDataMapper()->save($nota);
            } else {
                if ($nota instanceof Avaliacao_Model_NotaGeral) {
                    $nota->id = $this->_getNotaGeralIdEtapa($nota);
                    $this->getNotaGeralDataMapper()->save($nota);
                }
            }
        }

        // Atualiza as médias
        $this->_updateNotaComponenteMedia();

        return $this;
    }

    /**
     * Insere ou atualiza as faltas no boletim.
     *
     * @param bool $updateAverage
     * @return Avaliacao_Service_Boletim Provê interface fluída
     * @throws CoreExt_DataMapper_Exception
     */
    public function saveFaltas($updateAverage = false)
    {
        $faltaAluno = $this->_getFaltaAluno();
        $faltas = $this->getFaltas();

        foreach ($faltas as $falta) {
            $falta->faltaAluno = $faltaAluno;
            $falta->id = $this->_getFaltaIdEtapa($falta);
            $this->getFaltaAbstractDataMapper()->save($falta);
        }

        if ($this->getRegraAvaliacaoTipoNota() == RegraAvaliacao_Model_Nota_TipoValor::NENHUM) {
            return $this;
        }

        if ($updateAverage) {
            // Atualiza as médias
            $this->_updateNotaComponenteMedia();
        }

        return $this;
    }

    /**
     * Insere ou atualiza os pareceres no boletim.
     *
     * @return Avaliacao_Service_Boletim Provê interface fluída
     */
    public function savePareceres()
    {
        $parecerAluno = $this->_getParecerDescritivoAluno();
        $pareceres = $this->getPareceres();

        foreach ($pareceres as $parecer) {
            $parecer->parecerDescritivoAluno = $parecerAluno->id;
            $parecer->id = $this->_getParecerIdEtapa($parecer);
            $this->getParecerDescritivoAbstractDataMapper()->save($parecer);
        }

        return $this;
    }

    /**
     * @throws App_Model_Exception
     */
    protected function reloadComponentes()
    {
        $this->_setComponentes(
            App_Model_IedFinder::getComponentesPorMatricula(
                $this->getOption('matricula'),
                $this->getComponenteDataMapper(),
                $this->getComponenteTurmaDataMapper(),
                null,
                $this->getOption('etapaAtual'),
                null,
                $this->getOption('matriculaData')
            )
        );
    }

    /**
     * Promove o aluno de etapa escolar caso esteja aprovado de acordo com o
     * necessário estabelecido por tipoProgressao de
     * RegraAvaliacao_Model_Regra.
     *
     * @param bool $ok Caso a progressão não seja automática, é necessário uma
     *                 confirmação externa para a promoção do aluno.
     *
     * @return bool
     */
    public function promover($novaSituacaoMatricula = null)
    {
        // Essa função é necessária para promoção pois precisamos considerar a
        // situação de todas as disciplinas e não só da que está sendo lançada
        $this->reloadComponentes();
        $tipoProgressao = $this->getRegraAvaliacaoTipoProgressao();
        $situacaoMatricula = $this->getOption('aprovado');
        $situacaoBoletim = $this->getSituacaoAluno();
        $exceptionMsg = '';

        if ($situacaoMatricula == App_Model_MatriculaSituacao::TRANSFERIDO) {
            $novaSituacaoMatricula = App_Model_MatriculaSituacao::TRANSFERIDO;
        } else {
            if ($situacaoBoletim->andamento) {
                $novaSituacaoMatricula = App_Model_MatriculaSituacao::EM_ANDAMENTO;
            } else {
                switch ($tipoProgressao) {
                    case RegraAvaliacao_Model_TipoProgressao::CONTINUADA:
                        $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO;
                        break;
                    case RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MEDIA_PRESENCA:
                        if ($situacaoBoletim->aprovado && !$situacaoBoletim->retidoFalta && $situacaoBoletim->aprovadoComDependencia) {
                            $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA;
                        } else {
                            if ($situacaoBoletim->aprovado && !$situacaoBoletim->retidoFalta) {
                                $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO;
                            } else {
                                if ($situacaoBoletim->retidoFalta) {
                                    if (!$situacaoBoletim->aprovado) {
                                        $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO;
                                    } else {
                                        $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS;
                                    }
                                } else {
                                    $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO;
                                }
                            }
                        }
                        break;

                    case RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_SOMENTE_MEDIA || RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL:
                        if ($situacaoBoletim->aprovado && $situacaoBoletim->aprovadoComDependencia && !$situacaoBoletim->retidoFalta) {
                            $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA;
                        } else {
                            if ($situacaoBoletim->retidoFalta) {
                                if (!$situacaoBoletim->aprovado) {
                                    $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO;
                                } else {
                                    $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS;
                                }
                            } else {
                                if (!$situacaoBoletim->aprovado) {
                                    $novaSituacaoMatricula = App_Model_MatriculaSituacao::REPROVADO;
                                } else {
                                    $novaSituacaoMatricula = App_Model_MatriculaSituacao::APROVADO;
                                }
                            }
                        }

                        break;

                    case is_null($novaSituacaoMatricula):
                        $tipoProgressaoInstance = RegraAvaliacao_Model_TipoProgressao::getInstance();
                        $exceptionMsg = sprintf(
                            'Para atualizar a matrícula em uma regra %s é '
                            . 'necessário passar o valor do argumento "$novaSituacaoMatricula".',
                            $tipoProgressaoInstance->getValue($tipoProgressao)
                        );
                        break;
                }
            }
        }

        if ($novaSituacaoMatricula == $situacaoMatricula) {
            $exceptionMsg = "Matrícula ({$this->getOption('matricula')}) não precisou ser promovida, " .
                "pois a nova situação continua a mesma da anterior ($novaSituacaoMatricula)";
        }

        if ($exceptionMsg) {
            require_once 'CoreExt/Service/Exception.php';
            throw new CoreExt_Service_Exception($exceptionMsg);
        }

        return $this->_updateMatricula($this->getOption('matricula'), $this->getOption('usuario'), $novaSituacaoMatricula);
    }

    /**
     * @param $componente
     *
     * @return bool
     */
    public function unlockMediaComponente($componente)
    {
        try {
            $media = $this->getNotaComponenteMediaDataMapper()->find([
                'notaAluno' => $this->_getNotaAluno()->id,
                'componenteCurricular' => $componente
            ]);

            $media->bloqueada = 'f';
            $media->markOld();

            return $this->getNotaComponenteMediaDataMapper()->save($media);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $media
     * @param $componente
     * @param $etapa
     * @param bool $lock
     *
     * @throws CoreExt_DataMapper_Exception
     */
    public function updateMediaComponente($media, $componente, $etapa, bool $lock = false)
    {
        $lock = $lock === false ? 'f' : 't';

        try {
            $notaComponenteCurricularMedia = $this->getNotaComponenteMediaDataMapper()->find([
                'notaAluno' => $this->_getNotaAluno()->id,
                'componenteCurricular' => $componente
            ]);

            $notaComponenteCurricularMedia->media = $media;
            $notaComponenteCurricularMedia->mediaArredondada = $this->arredondaMedia($media);
            $notaComponenteCurricularMedia->bloqueada = $lock;
            $notaComponenteCurricularMedia->situacao = null;

            $notaComponenteCurricularMedia->markOld();
        } catch (Exeption $e) {
            $notaComponenteCurricularMedia = new Avaliacao_Model_NotaComponenteMedia([
                'notaAluno' => $this->_getNotaAluno()->id,
                'componenteCurricular' => $componente,
                'media' => $media,
                'mediaArredondada' => $this->arredondaMedia($media),
                'etapa' => $etapa,
                'bloqueada' => $lock
            ]);
        }

        // Salva a média
        $this->getNotaComponenteMediaDataMapper()->save($notaComponenteCurricularMedia);
        $notaComponenteCurricularMedia->situacao = $this->getSituacaoComponentesCurriculares()->componentesCurriculares[$componente]->situacao;

        try {
            // Atualiza situação matricula
            $this->promover();
        } catch (Exception $e) {
            // Evita que uma mensagem de erro apareça caso a situação na matrícula
            // não seja alterada.
        }

        //Atualiza a situação de acordo com o que foi inserido na média anteriormente
        $notaComponenteCurricularMedia->markOld();
        $this->getNotaComponenteMediaDataMapper()->save($notaComponenteCurricularMedia);
    }

    /**
     * @param $media
     * @param $etapa
     *
     * @throws CoreExt_DataMapper_Exception
     */
    public function updateMediaGeral($media, $etapa)
    {
        $mediaGeral = new Avaliacao_Model_MediaGeral([
            'notaAluno' => $this->_getNotaAluno()->id,
            'media' => $media,
            'mediaArredondada' => $this->arredondaMedia($media),
            'etapa' => $etapa,
        ]);

        try {
            // Se existir, marca como "old" para possibilitar a atualização
            $this->getMediaGeralDataMapper()->find([
                $mediaGeral->get('notaAluno')
            ]);
            $mediaGeral->markOld();
        } catch (Exception $e) {
            // Prossegue, sem problemas.
        }

        // Salva a média
        $this->getMediaGeralDataMapper()->save($mediaGeral);
    }

    /**
     * @param $novaSituacao
     * @param $matriculaId
     *
     * @return mixed
     */
    public function alterarSituacao($novaSituacao, $matriculaId)
    {
        return App_Model_Matricula::setNovaSituacao($matriculaId, $novaSituacao);
    }

    /**
     * Atualiza a média dos componentes curriculares.
     */
    protected function _updateNotaComponenteMedia()
    {
        require_once 'Avaliacao/Model/NotaComponenteMedia.php';
        $this->_loadNotas(false);

        $etapa = 1;

        if ($this->getRegraAvaliacaoNotaGeralPorEtapa() == '1') {
            $notasGerais = ['Se' => 0, 'Et' => $this->getOption('etapas')];

            foreach ($this->getNotasGerais() as $id => $notaGeral) {
                $etapasNotas = CoreExt_Entity::entityFilterAttr($notaGeral, 'etapa', 'nota');

                // Cria o array formatado para o cálculo da fórmula da média
                foreach ($etapasNotas as $etapa => $nota) {
                    if (is_numeric($etapa)) {
                        $notasGerais['E' . $etapa] = $nota;
                        $notasGerais['Se'] += $nota;
                        continue;
                    }
                    $notasGerais[$etapa] = $nota;
                }
            }

            //Calcula a média geral
            $mediaGeral = $this->_calculaMedia($notasGerais);

            // Cria uma nova instância de média, já com a nota arredondada e a etapa
            $mediaGeralEtapa = new Avaliacao_Model_MediaGeral([
                'notaAluno' => $this->_getNotaAluno()->id,
                'media' => $mediaGeral,
                'mediaArredondada' => $this->arredondaMedia($mediaGeral),
                'etapa' => $etapa
            ]);

            try {
                // Se existir, marca como "old" para possibilitar a atualização
                $this->getMediaGeralDataMapper()->find([
                    $mediaGeralEtapa->get('notaAluno')
                ]);

                $mediaGeralEtapa->markOld();
            } catch (Exception $e) {
                // Prossegue, sem problemas.
            }

            // Salva a média
            $this->getMediaGeralDataMapper()->save($mediaGeralEtapa);
        } else {
            $turmaId = $this->getOption('ref_cod_turma');
            $infosMatricula = $this->getOption('matriculaData');
            $matriculaId = $infosMatricula['cod_matricula'];
            $serieId = $infosMatricula['ref_ref_cod_serie'];
            $escolaId = $infosMatricula['ref_ref_cod_escola'];
            $notaAlunoId = $this->_getNotaAluno()->id;

            foreach ($this->getNotasComponentes() as $id => $notasComponentes) {
                //busca última nota lançada e somente atualiza a média e situação da nota do mesmo componente curricular
                //pois atualizar todas as médias de todos os componentes pode deixar o sistema com perda de performance e excesso de processamento

                $currentComponenteCurricular = $this->getCurrentComponenteCurricular();

                if (!isset($currentComponenteCurricular) || $currentComponenteCurricular == $id || (in_array($id, $this->codigoDisciplinasAglutinadas()) && in_array($currentComponenteCurricular, $this->codigoDisciplinasAglutinadas()))) {
                    // Cria um array onde o índice é a etapa
                    $etapasNotas = $this->calculaEtapaNotasAglutinada($id, $this->getNotasComponentes());

                    $qtdeEtapas = $this->getOption('etapas');

                    if ($this->getRegraAvaliacaoDefinirComponentePorEtapa() == '1') {
                        $qtdeEtapaEspecifica = App_Model_IedFinder::getQtdeEtapasComponente($turmaId, $id, $infosMatricula['ref_cod_aluno']);

                        $qtdeEtapas = ($qtdeEtapaEspecifica ? $qtdeEtapaEspecifica : $qtdeEtapas);
                    }

                    $verificaDispensa = App_Model_IedFinder::validaDispensaPorMatricula($matriculaId, $serieId, $escolaId, $id);
                    $consideraEtapas = [];

                    for ($i = 1; $i <= $qtdeEtapas; $i++) {
                        $consideraEtapas['C' . $i] = in_array($i, $verificaDispensa) ? 0 : 1;

                        if (in_array($i, $verificaDispensa)) {
                            $consideraEtapas['E' . $i] = 0;
                        }
                    }

                    if ($verificaDispensa) {
                        $qtdeEtapas = $qtdeEtapas - count($verificaDispensa);
                    }

                    $notas = array_merge(['Se' => 0, 'Et' => $qtdeEtapas], $consideraEtapas);

                    // Cria o array formatado para o cálculo da fórmula da média
                    foreach ($etapasNotas as $etapa => $nota) {
                        if (is_numeric($etapa)) {
                            $notas['E' . $etapa] = $nota;
                            $notas['Se'] += $nota;
                            continue;
                        }
                        $notas[$etapa] = $nota;
                    }

                    $notas = $this->_calculateNotasRecuperacoesEspecificas($id, $notas);

                    // Calcula a média por componente curricular
                    $media = $this->_calculaMedia($notas);
                    $locked = false;

                    try {
                        $notaComponenteCurricularMedia = $this->getNotaComponenteMediaDataMapper()->find([
                            'notaAluno' => $this->_getNotaAluno()->id,
                            'componenteCurricular' => $id,
                        ]);

                        $locked = (bool)$notaComponenteCurricularMedia->bloqueada;

                        // A média pode estar bloqueada caso tenha sido alterada manualmente.
                        // Neste caso não acontece a atualização da mesma por aqui e é necessário
                        // desbloqueá-la antes.
                        if (!$locked) {
                            $notaComponenteCurricularMedia->media = $media;
                            $notaComponenteCurricularMedia->mediaArredondada = $this->arredondaMedia($media);
                        }

                        $notaComponenteCurricularMedia->etapa = $etapa;
                        $notaComponenteCurricularMedia->situacao = null;

                        $notaComponenteCurricularMedia->markOld();
                    } catch (Exception $e) {
                        $notaComponenteCurricularMedia = new Avaliacao_Model_NotaComponenteMedia([
                            'notaAluno' => $this->_getNotaAluno()->id,
                            'componenteCurricular' => $id,
                            'media' => $media,
                            'mediaArredondada' => $this->arredondaMedia($media),
                            'etapa' => $etapa,
                            'bloqueada' => 'f',
                        ]);
                    }

                    // Salva a média
                    $this->getNotaComponenteMediaDataMapper()->save($notaComponenteCurricularMedia);

                    //Atualiza a situação de acordo com o que foi inserido na média anteriormente
                    $notaComponenteCurricularMedia->markOld();
                    $notaComponenteCurricularMedia->situacao = $this->getSituacaoComponentesCurriculares()->componentesCurriculares[$id]->situacao;

                    $this->getNotaComponenteMediaDataMapper()->save($notaComponenteCurricularMedia);
                }
            }

            $this->deleteNotaComponenteCurricularMediaWithoutNotas($notaAlunoId);
        }
    }

    /**
     * Atualiza os dados da matrícula do aluno.
     *
     * @param int  $matricula
     * @param int  $usuario
     * @param bool $promover
     *
     * @return bool
     *
     * @see App_Model_Matricula#atualizaMatricula($matricula, $usuario, $promover)
     */
    protected function _updateMatricula($matricula, $usuario, $promover)
    {
        return App_Model_Matricula::atualizaMatricula($matricula, $usuario, $promover);
    }

    /**
     * @param $etapa
     * @param $ComponenteCurricularId
     *
     * @return $this
     *
     * @throws Exception
     */
    public function deleteNota($etapa, $ComponenteCurricularId)
    {
        $this->setCurrentComponenteCurricular($ComponenteCurricularId);
        $nota = $this->getNotaComponente($ComponenteCurricularId, $etapa);
        $this->getNotaComponenteDataMapper()->delete($nota);

        try {
            $this->save();
        } catch (Exception $e) {
            error_log('Excessao ignorada ao zerar nota a ser removida: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * @param $etapa
     * @param $ComponenteCurricularId
     *
     * @return $this
     *
     * @throws Exception
     */
    public function deleteFalta($etapa, $ComponenteCurricularId)
    {
        $nota = $this->getFalta($etapa, $ComponenteCurricularId);
        $this->getFaltaAbstractDataMapper()->delete($nota);

        return $this;
    }

    /**
     * @param $etapa
     * @param $ComponenteCurricularId
     *
     * @return $this
     *
     * @throws Exception
     */
    public function deleteParecer($etapa, $ComponenteCurricularId)
    {
        $parecer = $this->getParecerDescritivo($etapa, $ComponenteCurricularId);
        $this->getParecerDescritivoAbstractDataMapper()->delete($parecer);

        return $this;
    }

    /**
     * @param $etapa
     *
     * @return $this
     *
     * @throws Exception
     */
    public function deleteNotaGeral($etapa)
    {
        $notaGeral = $this->getNotaGeral($etapa);
        if (!is_null($notaGeral)) {
            $this->getNotaGeralAbstractDataMapper()->delete($notaGeral);
        }

        return $this;
    }

    /**
     * Verifica se as notas das etapas anteriores foram lançadas para o
     * componente curricular. Lança uma exceção caso contrário.
     *
     * @param int|string $etapaId
     * @param int        $componenteCurricularId
     *
     * @return bool
     *
     * @throws MissingStagesException
     * @throws Exception
     */
    public function verificaNotasLancadasNasEtapasAnteriores($etapaId, $componenteCurricularId)
    {
        $temEtapasAnterioresLancadas = true;
        $etapasSemNotas = [];
        $matriculaId = $this->getOption('matricula');
        $serieId = $this->getOption('ref_cod_serie');
        $escolaId = $this->getOption('ref_cod_escola');
        $instituicao = App_Model_IedFinder::getInstituicao($this->getRegraAvaliacaoInstituicao());

        // Pelo que eu entendi, caso a opção `definirComponentePorEtapa` é
        // possível lançar notas para etapas futuras.

        if ($this->getRegraAvaliacaoDefinirComponentePorEtapa() == '1') {
            return true;
        }

        $etapasDispensadas = (array)App_Model_IedFinder::validaDispensaPorMatricula(
            $matriculaId,
            $serieId,
            $escolaId,
            $componenteCurricularId
        );

        $informacoesMatricula = (array)App_Model_IedFinder::getMatricula(
            $matriculaId
        );

        $informacoesEtapas = (array)App_Model_IedFinder::getEtapasDaTurma(
            $informacoesMatricula['ref_cod_turma']
        );

        $etapasAntesDaEnturmacao = array_filter($informacoesEtapas, function ($etapa) use ($informacoesMatricula) {
            return $informacoesMatricula['data_enturmacao'] > $etapa['data_fim'];
        });

        $etapasAntesDaEnturmacao = array_map(function ($etapa) {
            return $etapa['sequencial'];
        }, $etapasAntesDaEnturmacao);

        $exigirLancamentosAnteriores = dbBool($instituicao['exigir_lancamentos_anteriores']);

        if ($etapaId == 'Rc') {
            $etapaId = $this->getOption('etapas');
        }

        $secretarioDeveLancarNota = false;

        for ($etapa = 1; $etapa <= $etapaId; $etapa++) {

            // Etapas com dispensa não terão notas, então não devem ser
            // consideradas como bloqueantes.

            if (in_array($etapa, $etapasDispensadas)) {
                continue;
            }

            // Se o o parâmetro da instituição "exigir_lancamentos_anteriores"
            // não estiver ativo e o aluno foi enturmado em uma data posterior
            // ao fim de uma etapa, o lançamento da nota da etapa anterior não
            // será considerado bloqueante.

            if (!$exigirLancamentosAnteriores && in_array($etapa, $etapasAntesDaEnturmacao)) {
                continue;
            }

            if (in_array($etapa, $etapasAntesDaEnturmacao)) {
                $secretarioDeveLancarNota = true;
            }

            $nota = $this->getNotaAtual($etapa, $componenteCurricularId);

            $etapaDiferenteOuRecuperacao = $etapa != $etapaId || $etapaId == 'Rc';

            if (
                $etapaDiferenteOuRecuperacao
                && empty($nota)
                && !is_numeric($nota)
            ) {
                $temEtapasAnterioresLancadas = false;
                $etapasSemNotas[] = $etapa;
            }
        }

        if ($temEtapasAnterioresLancadas) {
            return true;
        }

        $nomeDaEtapa = 'Etapa';

        if (count($informacoesEtapas)) {
            $etapa = App_Model_IedFinder::getEtapa($informacoesEtapas[0]['cod_modulo']);

            if ($etapa) {
                $nomeDaEtapa = $etapa['nm_tipo'];
            }
        }

        if ($secretarioDeveLancarNota) {
            throw new StagesNotInformedByCoordinatorException($etapasSemNotas, $nomeDaEtapa);
        }

        throw new StagesNotInformedByTeacherException($etapasSemNotas, $nomeDaEtapa);
    }

    /**
     * Verifica se as faltas das etapas anteriores foram lançadas para o
     * componente curricular. Lança uma exceção caso contrário.
     *
     * @param int|string $etapaId
     * @param int        $componenteCurricularId
     *
     * @return bool
     *
     * @throws Exception
     */
    public function verificaFaltasLancadasNasEtapasAnteriores($etapaId, $componenteCurricularId)
    {
        $temEtapasAnterioresLancadas = true;
        $etapasSemFaltas = [];
        $matriculaId = $this->getOption('matricula');
        $serieId = $this->getOption('ref_cod_serie');
        $escolaId = $this->getOption('ref_cod_escola');

        $existeEtapaDispensada = (array)App_Model_IedFinder::validaDispensaPorMatricula($matriculaId, $serieId, $escolaId, $componenteCurricularId);

        for ($etapa = 1; $etapa <= $etapaId; $etapa++) {
            $faltas = $this->getFaltaAtual($etapa, $componenteCurricularId);

            if (in_array($etapa, $existeEtapaDispensada)) {
                continue;
            }

            if ($etapa != $etapaId && empty($faltas) && !is_numeric($faltas)) {
                $temEtapasAnterioresLancadas = false;
                $etapasSemFaltas[] = $etapa;
            }
        }

        if ($temEtapasAnterioresLancadas) {
            return true;
        }

        $mensagem = 'Falta somente pode ser lançada após lançar faltas nas '
            . 'etapas anteriores: ' . join(', ', $etapasSemFaltas);

        if ($this->getRegraAvaliacaoTipoPresenca() == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $mensagem .= ' deste componente curricular.';
        }

        throw new Exception($mensagem);
    }

    /**
     * Retorna a nota lançada na etapa para o componente curricular.
     *
     * @param int|string $etapa
     * @param int        $componenteCurricularId
     *
     * @return int|string
     */
    public function getNotaAtual($etapa, $componenteCurricularId)
    {
        // FIXME não entendi o motivo deste urldecode
        $nota = urldecode($this->getNotaComponente($componenteCurricularId, $etapa)->nota);

        return str_replace(',', '.', $nota);
    }

    /**
     * Retorna o número de faltas lançadas na etapa para o componente
     * curricular. Caso não exista, retorna null.
     *
     * @param int|string $etapa
     * @param int        $componenteCurricularId
     *
     * @return int|null
     */
    public function getFaltaAtual($etapa, $componenteCurricularId)
    {
        $faltas = null;
        $tipoPresenca = $this->getRegraAvaliacaoTipoPresenca();

        if ($tipoPresenca == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
            $faltas = $this->getFalta($etapa, $componenteCurricularId)->quantidade ?? null;
        }

        if ($tipoPresenca == RegraAvaliacao_Model_TipoPresenca::GERAL) {
            $faltas = $this->getFalta($etapa)->quantidade ?? null;
        }

        return $faltas;
    }

    /**
     * Retorna as regras de avaliação.
     *
     * @return array
     *
     * @see RegraAvaliacao_Model_Regra::findRegraRecuperacao()
     * @deprecated
     *
     */
    public function getRegrasRecuperacao()
    {
        return $this->getRegraAvaliacao()->findRegraRecuperacao();
    }

    /**
     * @return LegacyEvaluationRule
     */
    public function getEvaluationRule()
    {
        return LegacyEvaluationRule::findOrFail(
            $this->getRegra()->get('id')
        );
    }

    /**
     * @param int|string $stage
     * @param float      $score
     * @param float      $remedial
     *
     * @return float
     */
    public function calculateStageScore($stage, $score, $remedial)
    {
        if ($stage === 'Rc') {
            return $score;
        }

        $evaluationRule = $this->getEvaluationRule();

        if ($evaluationRule->isSpecificRetake()) {
            return $score;
        }

        $service = new StageScoreCalculationService();

        if ($evaluationRule->isAverageBetweenScoreAndRemedialCalculation()) {
            return $service->calculateAverageBetweenScoreAndRemedial($score, $remedial);
        }

        if ($evaluationRule->isSumScoreCalculation()) {
            return $service->calculateSumScore($score, $remedial);
        }

        return $service->calculateRemedial($score, $remedial);
    }

    /**
     * Verifica se a matrícula pode ficar aprovada com dependência
     *
     * @param integer $status
     * @return bool
     * @throws App_Model_Exception
     * @throws Exception
     */
    private function allowsApproveWithDependence($newRegistrationStatus)
    {
        $matricula = $this->getOption('matriculaData');
        $serie = LegacyGrade::find($matricula['ref_ref_cod_serie']);
        $instituicao = app(LegacyInstitution::class);

        $concluding = ($serie->concluinte == 2);
        $reprovesConcluding = $instituicao->reprova_dependencia_ano_concluinte;

        if ($concluding && $reprovesConcluding) {
            return false;
        }

        if ($matricula['dependencia']) {
            return false;
        }

        if ($newRegistrationStatus != App_Model_MatriculaSituacao::REPROVADO) {
            return false;
        }

        $disciplineAverages = $this->_loadMedias()->getMediasComponentes();

        $amountReproved = 0;
        foreach ($disciplineAverages as $disciplineId => $disciplineAverage) {
            $disciplineAverage = $disciplineAverage[0];

            if (!$this->exibeSituacao($disciplineId)) {
                continue;
            }

            if ($disciplineAverage->situacao == App_Model_MatriculaSituacao::REPROVADO) {
                $amountReproved++;
            }

        }

        return $amountReproved <= $this->getRegraAvaliacaoQtdDisciplinasDependencia();
    }
}
