<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacySchoolStage;
use iEducar\Modules\AcademicYear\Exceptions\DisciplineNotLinkedToRegistrationException;
use iEducar\Modules\Enrollments\Exceptions\StudentNotEnrolledInSchoolClass;
use iEducar\Modules\EvaluationRules\Exceptions\EvaluationRuleNotDefinedInLevel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class App_Model_IedFinder extends CoreExt_Entity
{
    /**
     * Retorna todas as instituições cadastradas em pmieducar.instituicao.
     *
     * @return array
     */
    public static function getInstituicoes()
    {
        $instituicao = self::addClassToStorage(
            'clsPmieducarInstituicao',
            null,
            'include/pmieducar/clsPmieducarInstituicao.inc.php'
        );

        $instituicoes = [];
        foreach ($instituicao->lista() as $instituicao) {
            $instituicoes[$instituicao['cod_instituicao']] = mb_strtoupper($instituicao['nm_instituicao'], 'UTF-8');
        }

        return $instituicoes;
    }

    /**
     * Retorna um array com as informações de escola a partir de seu código.
     *
     * @param int $id
     *
     * @return array
     *
     * @throws App_Model_Exception
     */
    public static function getEscola($id)
    {
        $escola = self::addClassToStorage(
            'clsPmieducarEscola',
            null,
            'include/pmieducar/clsPmieducarEscola.inc.php'
        );

        $escola->cod_escola = $id;
        $escola = $escola->detalhe();

        if (false === $escola) {
            throw new App_Model_Exception(
                sprintf('Escola com o código "%d" não existe.', $id)
            );
        }

        return $escola;
    }

    /**
     * Retorna todas as escolas cadastradas na tabela pmieducar.escola, selecionando
     * opcionalmente pelo código da instituição.
     *
     * @param int $instituicaoId
     *
     * @return array
     */
    public static function getEscolas($instituicaoId = null)
    {
        $_escolas = self::addClassToStorage(
            'clsPmieducarEscola',
            null,
            'include/pmieducar/clsPmieducarEscola.inc.php'
        );

        $_escolas->setOrderby('nome');

        $escolas = [];

        foreach ($_escolas->lista(null, null, null, $instituicaoId) as $escola) {
            $escolas[$escola['cod_escola']] = mb_strtoupper($escola['nome']);
        }

        return $escolas;
    }

    /**
     * Retorna todas as escolas. Se o usuário logado for do nível escolar, pega somente as escolas
     * vinculadas a ele
     *
     * @param int $instituicaoId
     *
     * @return array
     */
    public static function getEscolasByUser($instituicaoId)
    {
        $query = LegacySchool::query()
            ->where('ativo', 1)
            ->whereHas('person')->where('ref_cod_instituicao', $instituicaoId);

        if (Auth::user()->isSchooling()) {
            $schools = Auth::user()->schools->pluck('cod_escola')->all();
            $query->whereIn('cod_escola', $schools);
        }

        return $query->get()->sortBy('name')->getKeyValueArray('name');
    }

    /**
     * Retorna um nome de curso, procurando pelo seu código.
     *
     * @param int $id
     *
     * @return string|FALSE
     */
    public static function getCurso($id)
    {
        $curso = self::addClassToStorage(
            'clsPmieducarCurso',
            null,
            'include/pmieducar/clsPmieducarCurso.inc.php'
        );

        $curso->cod_curso = $id;
        $curso = $curso->detalhe();

        return empty($curso['descricao']) ? $curso['nm_curso'] : "{$curso['nm_curso']} ({$curso['descricao']})";
    }

    /**
     * Retorna todos os cursos cadastradas na tabela pmieducar.escola_curso, selecionando
     * opcionalmente pelo código da escola.
     *
     * @param int $escolaId
     *
     * @return array
     */
    public static function getCursos($escolaId = null)
    {
        $escola_curso = self::addClassToStorage(
            'clsPmieducarEscolaCurso',
            null,
            'include/pmieducar/clsPmieducarEscolaCurso.inc.php'
        );

        // Carrega os cursos
        $escola_curso->setOrderby('ref_cod_escola ASC, cod_curso ASC');
        $escola_curso = $escola_curso->lista($escolaId);

        if (!$escola_curso) {
            return [];
        }

        $cursos = [];

        foreach ($escola_curso as $key => $val) {
            $nomeCurso = self::getCurso($val['ref_cod_curso']);
            $cursos[$val['ref_cod_curso']] = $nomeCurso;
        }

        return $cursos;
    }

    /**
     * Retorna um array com as informações da instituição a partir de seu código.
     *
     * @param int $codInstituicao
     *
     * @return array
     *
     * @throws App_Model_Exception
     */
    public static function getInstituicao($codInstituicao)
    {
        // Recupera clsPmieducarInstituicao do storage de classe estático
        $instituicao = self::addClassToStorage(
            'clsPmieducarInstituicao',
            null,
            'include/pmieducar/clsPmieducarInstituicao.inc.php'
        );

        // Usa o atributo público para depois chamar o método detalhe()
        $instituicao->cod_instituicao = $codInstituicao;
        $instituicao = $instituicao->detalhe();

        if (false === $instituicao) {
            throw new App_Model_Exception(
                sprintf('Série com o código "%d" não existe.', $codInstituicao)
            );
        }

        return $instituicao;
    }

    /**
     * Retorna um array com as informações da série a partir de seu código.
     *
     * @param int $codSerie
     *
     * @return array
     *
     * @throws App_Model_Exception
     */
    public static function getSerie($codSerie)
    {
        // Recupera clsPmieducarSerie do storage de classe estático
        $serie = self::addClassToStorage(
            'clsPmieducarSerie',
            null,
            'include/pmieducar/clsPmieducarSerie.inc.php'
        );

        // Usa o atributo público para depois chamar o método detalhe()
        $serie->cod_serie = $codSerie;
        $serie = $serie->detalhe();

        if (false === $serie) {
            throw new App_Model_Exception(
                sprintf('Série com o código "%d" não existe.', $codSerie)
            );
        }

        return $serie;
    }

    /**
     * Retorna todas as séries cadastradas na tabela pmieducar.serie, selecionando
     * opcionalmente pelo código da instituição, da escola ou do curso.
     *
     * @param int|null $instituicaoId
     * @param int|null $escolaId
     * @param int|null $cursoId
     * @param int|null $ano
     *
     * @return array
     */
    public static function getSeries($instituicaoId = null, $escolaId = null, $cursoId = null, $ano = null)
    {
        $orderBy = ' nm_serie ASC, ref_cod_curso ASC, cod_serie ASC, etapa_curso ASC';
        $series = app(clsPmieducarSerie::class);
        $series->setOrderby($orderBy);

        $series = $series->lista(
            null,
            null,
            null,
            $cursoId,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $instituicaoId,
            null,
            null,
            $escolaId,
            null,
            null,
            $ano
        );

        $result = [];
        foreach ($series as $serie) {
            $nomeSerie = empty($serie['descricao']) ? $serie['nm_serie'] : "{$serie['nm_serie']} ({$serie['descricao']})";
            $result[$serie['cod_serie']] = mb_strtoupper($nomeSerie, 'UTF-8');
        }

        return $result;
    }

    /**
     * Retorna um array com as informações da turma a partir de seu código.
     *
     * @param int $codTurma
     *
     * @return array
     *
     * @throws App_Model_Exception
     */
    public static function getTurma($codTurma)
    {
        // Recupera clsPmieducarTurma do storage de classe estático
        $turma = self::addClassToStorage(
            'clsPmieducarTurma',
            null,
            'include/pmieducar/clsPmieducarTurma.inc.php'
        );

        // Usa o atributo público para depois chamar o método detalhe()
        $turma->cod_turma = $codTurma;
        $turma = $turma->detalhe();

        if (false === $turma) {
            throw new App_Model_Exception(
                sprintf('Turma com o código "%d" não existe.', $codTurma)
            );
        }

        return $turma;
    }

    /**
     * Retorna as turmas de uma escola, selecionando opcionalmente pelo código da série.
     *
     * @param int|null $escolaId
     * @param int|null $serieId
     * @param int|null $ano
     * @param int|null $ativo
     *
     * @return array (cod_turma => nm_turma)
     */
    public static function getTurmas($escolaId, $serieId = null, $ano = null, $ativo = null)
    {
        $turma = self::addClassToStorage(
            'clsPmieducarTurma',
            null,
            'include/pmieducar/clsPmieducarTurma.inc.php'
        );

        // Carrega as turmas da escola
        $turma->setOrderBy('nm_turma ASC');
        $turmas = $turma->lista(
            null,
            null,
            null,
            $serieId,
            $escolaId,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $ativo,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $ano
        );

        $ret = [];

        foreach ($turmas as $turma) {
            $ret[$turma['cod_turma']] = $turma['nm_turma'] . ' - ' . ($turma['ano'] == null ? 'Sem ano' : $turma['ano']);
        }

        return $ret;
    }

    /**
     * Retorna as turmas de uma escola e ano para exportação do educacenso.
     *
     * @param int $escolaId
     * @param int $ano
     *
     * @return array (cod_turma => nm_turma)
     */
    public static function getTurmasEducacenso($escolaId, $ano = null)
    {
        $turma = self::addClassToStorage(
            'clsPmieducarTurma',
            null,
            'include/pmieducar/clsPmieducarTurma.inc.php'
        );

        // Carrega as turmas da escola
        $turma->setOrderBy('nm_turma ASC');
        $turma->listarNaoInformarEducacenso = false;

        $turmas = $turma->lista(
            null,
            null,
            null,
            null,
            $escolaId,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $ano
        );

        $ret = [];

        foreach ($turmas as $turma) {
            $ret[$turma['cod_turma']] = $turma['nm_turma'] . ' - ' . ($turma['ano'] == null ? 'Sem ano' : $turma['ano']);
        }

        return $ret;
    }

    public static function getAnosLetivosEscolaSerie($escolaId, $serieId)
    {
        $params = [$escolaId, $serieId];

        $sql = '
            SELECT array_to_json(escola_serie.anos_letivos) as anos_letivos
            FROM pmieducar.escola_serie
            WHERE escola_serie.ref_cod_escola = $1
            AND escola_serie.ref_cod_serie = $2
            AND escola_serie.ativo = 1
            LIMIT 1
        ';

        $anosLetivos = json_decode(Portabilis_Utils_Database::selectField($sql, $params) ?: '[]');

        return array_combine($anosLetivos, $anosLetivos);
    }

    /**
     * Retorna array com as referências de pmieducar.escola_serie_disciplina
     * a modules.componente_curricular ('ref_ref_cod_disciplina').
     *
     * @param int                                             $serieId        O código do ano escolar/série.
     * @param int                                             $escolaId
     * @param ComponenteCurricular_Model_ComponenteDataMapper $mapper
     * @param null                                            $disciplinaId
     * @param null                                            $etapa
     * @param bool                                            $trazerDetalhes
     * @param null                                            $ano
     *
     * @return array
     *
     * @throws App_Model_Exception
     */
    public static function getEscolaSerieDisciplina(
        $serieId,
        $escolaId,
        ComponenteCurricular_Model_ComponenteDataMapper $mapper = null,
        $disciplinaId = null,
        $etapa = null,
        $trazerDetalhes = true,
        $ano = null
    ) {
        if (is_null($serieId)) {
            throw new App_Model_Exception('O parametro serieId não pode ser nulo');
        }

        if (is_null($escolaId)) {
            throw new App_Model_Exception('O parametro escolaId não pode ser nulo');
        }

        $key = json_encode(compact('serieId', 'escolaId', 'disciplinaId', 'etapa', 'ano'));

        $disciplinas = Cache::store('array')->remember("getEscolaSerieDisciplina:{$key}", now()->addMinute(), function () use ($serieId, $escolaId, $disciplinaId, $etapa, $ano) {
            $escolaSerieDisciplina = self::addClassToStorage(
                'clsPmieducarEscolaSerieDisciplina',
                null,
                'include/pmieducar/clsPmieducarEscolaSerieDisciplina.inc.php'
            );

            $disciplinas = $escolaSerieDisciplina->lista($serieId, $escolaId, $disciplinaId, 1, false, $etapa, $ano);

            if (false === $disciplinas) {
                return [];
            }

            return $disciplinas;
        });

        if (empty($disciplinas)) {
            throw new DisciplineNotLinkedToRegistrationException($escolaId, $disciplinaId, $ano, $serieId);
        }

        $componentes = [];

        foreach ($disciplinas as $disciplina) {
            $componente = new stdClass();

            $componente->id = $disciplina['ref_cod_disciplina'];
            $componente->cargaHoraria = $disciplina['carga_horaria'];

            $componentes[] = $componente;
        }

        if ($trazerDetalhes) {
            return self::_hydrateComponentes($componentes, $serieId, $mapper);
        } else {
            return $componentes;
        }
    }

    /**
     * Retorna as instâncias de ComponenteCurricular_Model_Componente de uma turma.
     *
     * @param int                                             $serieId          O código do ano escolar/série da turma.
     * @param int                                             $escola           O código da escola da turma.
     * @param int                                             $turma            O código da turma.
     * @param ComponenteCurricular_Model_TurmaDataMapper      $mapper
     * @param ComponenteCurricular_Model_ComponenteDataMapper $componenteMapper
     *
     * @return array
     *
     * @throws App_Model_Exception
     */
    public static function getComponentesTurma(
        $serieId,
        $escola,
        $turma,
        ComponenteCurricular_Model_TurmaDataMapper $mapper = null,
        ComponenteCurricular_Model_ComponenteDataMapper $componenteMapper = null,
        $componenteCurricularId = null,
        $etapa = null,
        $trazerDetalhes = true,
        $ano = null
    ) {
        if (is_null($mapper)) {
            $mapper = new ComponenteCurricular_Model_TurmaDataMapper();
        }

        $where = ['turma' => $turma];

        if (is_numeric($componenteCurricularId)) {
            $where['componente_curricular_id'] = $componenteCurricularId;
        }

        $key = json_encode($where);

        $componentesTurma = Cache::store('array')->remember("getComponentesTurma:$key", now()->addMinute(), function () use ($mapper, $where) {
            return $mapper->findAll([], $where);
        });

        $disciplinaDispensada = self::disciplinaDispensadaDaTurma($turma);

        // Não existem componentes específicos para a turma
        if (0 == count($componentesTurma)) {
            $componentesTurma = self::getEscolaSerieDisciplina(
                $serieId,
                $escola,
                $componenteMapper,
                $componenteCurricularId,
                $etapa,
                $trazerDetalhes,
                $ano
            );

            foreach ($componentesTurma as $key => $componente) {
                if ($componente->id == $disciplinaDispensada) {
                    unset($componentesTurma[$key]);
                }
            }

            return $componentesTurma;
        }

        $componentes = [];
        foreach ($componentesTurma as $componenteTurma) {
            $componente = new stdClass();

            $componente->id = $componenteTurma->get('componenteCurricular');
            $componente->cargaHoraria = $componenteTurma->cargaHoraria;

            $disponivelEtapa = true;

            if ($componenteTurma->etapasEspecificas == 1) {
                $etapas = $componenteTurma->etapasUtilizadas;

                $disponivelEtapa = (strpos($etapas, $etapa) === false ? false : true);
            }

            if ($disponivelEtapa) {
                $componentes[] = $componente;
            }
        }

        if ($trazerDetalhes) {
            return self::_hydrateComponentes($componentes, $serieId, $componenteMapper);
        } else {
            return $componentes;
        }
    }

    public static function getTipoNotaComponenteSerie($componenteId, $serieId)
    {
        $key = json_encode(compact('componenteId', 'serieId'));

        $value = Cache::store('array')->remember("getTipoNotaComponenteSerie:{$key}", now()->addMinute(), function () use ($serieId, $componenteId) {
            $sql = '
                SELECT tipo_nota
                FROM modules.componente_curricular_ano_escolar
                WHERE ano_escolar_id = $1
                AND componente_curricular_id = $2
            ';

            $tipoNota = Portabilis_Utils_Database::fetchPreparedQuery($sql, ['params' => [$serieId, $componenteId], 'return_only' => 'first-row']);

            return $tipoNota['tipo_nota'] ?? 'null';
        });

        return $value === 'null' ? null : $value;
    }

    /**
     * Recupera instâncias persistidas de ComponenteCurricular_Model_Componente,
     * retornando-as com a carga horária padrão caso o componente identificado
     * em $componentes possua uma carga horária (atributo cargaHoraria) nula.
     *
     * @param array                                           $componentes
     * @param int                                             $anoEscolar
     * @param ComponenteCurricular_Model_ComponenteDataMapper $mapper
     *
     * @return array
     *
     * @throws Exception
     */
    protected static function _hydrateComponentes(
        array $componentes,
        $anoEscolar,
        ComponenteCurricular_Model_ComponenteDataMapper $mapper = null
    ) {
        $ids = array_map(function ($componente) {
            return intval($componente->id);
        }, $componentes);

        $key = json_encode(compact('anoEscolar', 'componentes'));
        $getCargaHoraria = function ($componentes, $id) {
            foreach ($componentes as $componente) {
                if ($componente->id == $id && $componente->cargaHoraria) {
                    return $componente->cargaHoraria;
                }
            }

            return null;
        };

        return Cache::store('array')->remember("_hydrateComponentes:{$key}", now()->addMinute(), function () use ($anoEscolar, $ids, $componentes, $getCargaHoraria) {
            $disciplinesAcademicYear = LegacyDisciplineAcademicYear::query()
                ->where('ano_escolar_id', $anoEscolar)
                ->whereIn('componente_curricular_id', $ids)
                ->pluck('carga_horaria', 'componente_curricular_id');

            $disciplines = LegacyDiscipline::query()
                ->whereIn('id', $ids)
                ->get()
                ->map(function (LegacyDiscipline $discipline) use ($disciplinesAcademicYear, $componentes, $getCargaHoraria) {
                    return new ComponenteCurricular_Model_Componente([
                        'id' => $discipline->id,
                        'instituicao' => $discipline->instituicao_id,
                        'nome' => $discipline->nome,
                        'abreviatura' => $discipline->abreviatura,
                        'tipo_base' => $discipline->tipo_base,
                        'area_conhecimento' => $discipline->area_conhecimento_id,
                        'cargaHoraria' => $getCargaHoraria($componentes, $discipline->id) ?? ($discipline->cargaHoraria ?? $disciplinesAcademicYear->get($discipline->id)),
                        'codigo_educacenso' => $discipline->codigo_educacenso,
                        'ordenamento' => $discipline->ordenamento,
                        'desconsidera_para_progressao' => $discipline->desconsidera_para_progressao,
                    ]);
                })->keyBy('id')->all();

            return $disciplines;
        });
    }

    /**
     * Retorna um array populado com os dados de uma matricula.
     *
     * @param int $codMatricula
     *
     * @return array
     *
     * @throws EvaluationRuleNotDefinedInLevel
     * @throws StudentNotEnrolledInSchoolClass
     */
    public static function getMatricula($codMatricula, $codTurma = null)
    {
        if (empty($codTurma)) {
            $codTurma = 0;
        }

        $sql = '
            SELECT
                m.cod_matricula,
                m.ref_cod_reserva_vaga,
                m.ref_ref_cod_escola,
                m.ref_ref_cod_serie,
                m.ref_usuario_exc,
                m.ref_usuario_cad,
                m.ref_cod_aluno,
                m.aprovado,
                m.data_cadastro,
                m.data_exclusao,
                m.ativo,
                m.ano,
                m.ultima_matricula,
                m.modulo,
                formando,
                descricao_reclassificacao,
                matricula_reclassificacao,
                m.ref_cod_curso,
                m.matricula_transferencia,
                m.semestre,
                m.data_matricula,
                m.data_cancel,
                m.ref_cod_abandono_tipo,
                m.turno_pre_matricula,
                m.dependencia,
                data_saida_escola,
                mt.turno_id,
                p.nome,
                (p.nome) AS nome_upper,
                e.ref_cod_instituicao,
                mt.ref_cod_turma,
                t.nm_turma,
                c.carga_horaria AS curso_carga_horaria,
                c.hora_falta AS curso_hora_falta,
                s.carga_horaria AS serie_carga_horaria,
                s.dias_letivos AS serie_dias_letivos,
                c.nm_curso AS curso_nome,
                c.padrao_ano_escolar,
                s.nm_serie AS serie_nome,
                s.concluinte AS serie_concluinte,
                rasa.regra_avaliacao_diferenciada_id as serie_regra_avaliacao_diferenciada_id,
                rasa.regra_avaliacao_id as serie_regra_avaliacao_id,
                e.utiliza_regra_diferenciada as escola_utiliza_regra_diferenciada,
                mt.data_enturmacao
            FROM pmieducar.matricula m
            JOIN pmieducar.aluno a
            ON a.cod_aluno = m.ref_cod_aluno
            JOIN cadastro.pessoa p
            ON p.idpes = a.ref_idpes
            JOIN pmieducar.escola e
            ON m.ref_ref_cod_escola = e.cod_escola
            JOIN pmieducar.instituicao
            ON instituicao.cod_instituicao = e.ref_cod_instituicao
            JOIN pmieducar.matricula_turma mt
            ON mt.ref_cod_matricula = m.cod_matricula
            JOIN pmieducar.turma t
            ON t.cod_turma = mt.ref_cod_turma
            JOIN pmieducar.curso c
            ON m.ref_cod_curso = c.cod_curso
            JOIN pmieducar.serie s
            ON m.ref_ref_cod_serie = s.cod_serie
            LEFT JOIN modules.regra_avaliacao_serie_ano rasa
            ON rasa.ano_letivo = m.ano
            AND rasa.serie_id = s.cod_serie
            WHERE m.cod_matricula = $1
            AND CASE WHEN $2 = 0 THEN true ELSE t.cod_turma = $2 END
            AND a.ativo = 1
            AND t.ativo = 1
            AND
            (
                mt.ativo = 1
                OR
                (
                    CASE
                        WHEN instituicao.data_base_remanejamento IS NOT NULL
                            THEN mt.data_exclusao::date > instituicao.data_base_remanejamento
                        ELSE true
                    END
                    AND (
                        mt.transferido
                        OR mt.remanejado
                        OR mt.reclassificado
                        OR mt.abandono
                        OR mt.falecido
                    )
                )
            )
            ORDER BY
                mt.ativo DESC,
                mt.sequencial DESC
            LIMIT 1
    ';

        $matricula = Portabilis_Utils_Database::selectRow($sql, ['params' => [$codMatricula, $codTurma]]);

        if (!$matricula) {
            throw new StudentNotEnrolledInSchoolClass($codMatricula);
        } elseif (empty($matricula['serie_regra_avaliacao_id'])) {
            throw new EvaluationRuleNotDefinedInLevel($matricula['ref_ref_cod_serie']);
        }

        return $matricula;
    }

    /**
     * Retorna uma instância de RegraAvaliacao_Model_Regra a partir dos dados
     * da matrícula.
     *
     * @param int                                  $codMatricula
     * @param RegraAvaliacao_Model_RegraDataMapper $mapper
     * @param array                                $matricula
     *
     * @return RegraAvaliacao_Model_Regra
     *
     * @throws App_Model_Exception
     */
    public static function getRegraAvaliacaoPorMatricula(
        $codMatricula,
        RegraAvaliacao_Model_RegraDataMapper $mapper = null,
        $matricula = null
    ) {
        if (empty($matricula)) {
            $matricula = self::getMatricula($codMatricula);
        }

        $possuiDeficiencia = self::verificaSePossuiDeficiencia($matricula['ref_cod_aluno']);

        if (is_null($mapper)) {
            $mapper = new RegraAvaliacao_Model_RegraDataMapper();
        }

        if (dbBool($matricula['escola_utiliza_regra_diferenciada']) && is_numeric($matricula['serie_regra_avaliacao_diferenciada_id'])) {
            $id = $matricula['serie_regra_avaliacao_diferenciada_id'];
        } else {
            $id = $matricula['serie_regra_avaliacao_id'];
        }

        $key = json_encode(compact('id'));

        $regra = Cache::store('array')->remember("getRegraAvaliacaoPorMatricula:{$key}", now()->addMinute(), function () use ($mapper, $id) {
            return $mapper->find($id);
        });

        if ($possuiDeficiencia && $regra->regraDiferenciada) {
            $regra = $regra->regraDiferenciada;
        }

        return $regra;
    }

    /**
     * Retorna uma instância de RegraAvaliacao_Model_Regra a partir dos dados
     * da turma.
     *
     * @param int                                  $turmaId
     * @param RegraAvaliacao_Model_RegraDataMapper $mapper
     *
     * @return RegraAvaliacao_Model_Regra
     *
     * @throws App_Model_Exception
     */
    public static function getRegraAvaliacaoPorTurma(
        $turmaId,
        RegraAvaliacao_Model_RegraDataMapper $mapper = null
    ) {
        $turma = self::getTurma($turmaId);
        $serie = self::getSerie($turma['ref_ref_cod_serie']);
        $escola = self::getEscola($turma['ref_ref_cod_escola']);

        if (is_null($mapper)) {
            $mapper = new RegraAvaliacao_Model_RegraDataMapper();
        }

        if (dbBool($escola['utiliza_regra_diferenciada']) && is_numeric($serie['regra_avaliacao_diferenciada_id'])) {
            $intRegra = $serie['regra_avaliacao_diferenciada_id'];
        } else {
            $intRegra = $serie['regra_avaliacao_id'];
        }

        return $mapper->find($intRegra);
    }

    /**
     * Retorna um array de instâncias ComponenteCurricular_Model_Componente ao
     * qual um aluno cursa através de sua matrícula.
     *
     * Exclui todas os componentes curriculares ao qual o aluno está dispensado
     * de cursar.
     *
     * @param int                                             $codMatricula
     * @param ComponenteCurricular_Model_ComponenteDataMapper $componenteMapper
     * @param ComponenteCurricular_Model_TurmaDataMapper      $turmaMapper
     * @param int|null                                        $componenteCurricularId
     * @param int|null                                        $etapa
     * @param int|null                                        $turma
     * @param int|null                                        $matricula
     * @param bool                                            $trazerDetalhes
     *
     * @return array
     *
     * @throws App_Model_Exception
     */
    public static function getComponentesPorMatricula(
        $codMatricula,
        ComponenteCurricular_Model_ComponenteDataMapper $componenteMapper = null,
        ComponenteCurricular_Model_TurmaDataMapper $turmaMapper = null,
        $componenteCurricularId = null,
        $etapa = null,
        $turma = null,
        $matricula = null,
        $trazerDetalhes = true,
        $ignorarDispensasParciais = false
    ) {
        if (empty($matricula)) {
            $matricula = self::getMatricula($codMatricula);
        }

        $codEscola = $matricula['ref_ref_cod_escola'];
        $codSerie = $matricula['ref_ref_cod_serie'];
        $ano = $matricula['ano'];

        if (!$turma) {
            $turma = $matricula['ref_cod_turma'];
        }

        $ret = [];

        if (is_numeric($turma) && is_numeric($codSerie) && is_numeric($codEscola)) {

            // Disciplinas da escola na série em que o aluno está matriculado
            $componentes = self::getComponentesTurma(
                $codSerie,
                $codEscola,
                $turma,
                $turmaMapper,
                $componenteMapper,
                $componenteCurricularId,
                $etapa,
                $trazerDetalhes,
                $ano
            );

            // Dispensas do aluno
            $disciplinasDispensa = self::getDisciplinasDispensadasPorMatricula(
                $codMatricula,
                $codSerie,
                $codEscola,
                $etapa,
                $ignorarDispensasParciais
            );

            if (dbBool($matricula['dependencia'])) {

                // Disciplinas dependência
                $disciplinasDependencia = self::getDisciplinasDependenciaPorMatricula(
                    $codMatricula,
                    $codSerie,
                    $codEscola
                );

                foreach ($componentes as $id => $componente) {
                    if (in_array($id, $disciplinasDispensa)) {
                        continue;
                    }

                    if (!in_array($id, $disciplinasDependencia)) {
                        continue;
                    }

                    $ret[$id] = $componente;
                }
            } else {
                foreach ($componentes as $id => $componente) {
                    if (in_array($id, $disciplinasDispensa)) {
                        continue;
                    }

                    $ret[$id] = $componente;
                }
            }
        }

        return $ret;
    }

    /**
     * Retorna array com as referências de pmieducar.dispensa_disciplina
     * a modules.componente_curricular ('ref_ref_cod_disciplina').
     *
     * @param int $codMatricula
     * @param int $codSerie
     * @param int $codEscola
     *
     * @return array
     */
    public static function getDisciplinasDispensadasPorMatricula(
        $codMatricula,
        $codSerie,
        $codEscola,
        $etapa,
        $ignorarDispensasParciais = false
    ) {
        $key = json_encode(compact('codMatricula', 'codSerie', 'codEscola', 'etapa', 'ignorarDispensasParciais'));

        $dispensas = Cache::store('array')->remember("getDisciplinasDispensadasPorMatricula:{$key}", now()->addMinute(), function () use ($codMatricula, $codSerie, $codEscola, $etapa, $ignorarDispensasParciais) {
            $dispensas = self::addClassToStorage(
                'clsPmieducarDispensaDisciplina',
                null,
                'include/pmieducar/clsPmieducarDispensaDisciplina.inc.php'
            );

            $dispensas = $dispensas->disciplinaDispensadaEtapa($codMatricula, $codSerie, $codEscola, $etapa, $ignorarDispensasParciais);

            if (false === $dispensas) {
                return [];
            }

            return $dispensas;
        });

        $disciplinasDispensa = [];

        foreach ($dispensas as $dispensa) {
            $disciplinasDispensa[] = $dispensa['ref_cod_disciplina'];
        }

        return $disciplinasDispensa;
    }

    public static function disciplinaDispensadaDaTurma($codTurma)
    {
        if (!$codTurma) {
            return;
        }

        $discipline = Cache::store('array')->remember("disciplinaDispensadaDaTurma:{$codTurma}", now()->addMinute(), function () use ($codTurma) {
            $discipline = LegacySchoolClass::query()->find($codTurma)->ref_cod_disciplina_dispensada ?? null;

            // Caso não exista a disciplina, armazena a string 'null'
            return $discipline ?: 'null';
        });

        // Se o retorno é uma string 'null', sabemos o que o valor real é null
        if ($discipline === 'null') {
            return null;
        }

        return $discipline;
    }

    public static function validaDispensaPorMatricula(
        $codMatricula,
        $codSerie,
        $codEscola,
        $disciplina
    ) {
        $dispensas = self::addClassToStorage(
            'clsPmieducarDispensaDisciplina',
            null,
            'include/pmieducar/clsPmieducarDispensaDisciplina.inc.php'
        );

        $dispensas = $dispensas->disciplinaDispensadaEtapa($codMatricula, $codSerie, $codEscola);

        $etapaDispensada = [];

        if (!$dispensas) {
            return [];
        }

        foreach ($dispensas as $dispensa) {
            if ($dispensa['ref_cod_disciplina'] == $disciplina) {
                $etapaDispensada[] = $dispensa['etapa'];
            }
        }

        return $etapaDispensada;
    }

    /**
     * Retorna array com as referências de pmieducar.disciplina_dependencia
     * a modules.componente_curricular ('ref_ref_cod_disciplina').
     *
     * @param int $codMatricula
     * @param int $codSerie
     * @param int $codEscola
     *
     * @return array
     */
    public static function getDisciplinasDependenciaPorMatricula(
        $codMatricula,
        $codSerie,
        $codEscola
    ) {
        $key = json_encode(compact('codMatricula', 'codSerie', 'codEscola'));

        $disciplinas = Cache::store('array')->remember("getDisciplinasDependenciaPorMatricula:{$key}", now()->addMinute(), function () use ($codMatricula, $codSerie, $codEscola) {
            $disciplinas = self::addClassToStorage(
                'clsPmieducarDisciplinaDependencia',
                null,
                'include/pmieducar/clsPmieducarDisciplinaDependencia.inc.php'
            );

            $disciplinas = $disciplinas->lista($codMatricula, $codSerie, $codEscola);

            if (false === $disciplinas) {
                return [];
            }

            return $disciplinas;
        });

        $disciplinasDependencia = [];

        foreach ($disciplinas as $disciplina) {
            $disciplinasDependencia[] = $disciplina['ref_cod_disciplina'];
        }

        return $disciplinasDependencia;
    }

    /**
     * Retorna a quantidade de módulos do ano letivo por uma dada matrícula.
     *
     * @param int   $codMatricula
     * @param array $matricula
     *
     * @return int
     *
     * @throws App_Model_Exception
     */
    public static function getQuantidadeDeModulosMatricula($codMatricula, $matricula = null)
    {
        if (empty($matricula)) {
            $matricula = self::getMatricula($codMatricula);
        }

        if ($matricula['padrao_ano_escolar'] ?? null) {
            $where = [
                'ref_ref_cod_escola' => $matricula['ref_ref_cod_escola'],
                'ref_ano' => $matricula['ano'],
            ];

            $key = json_encode($where);

            return Cache::store('array')->remember("getQuantidadeDeModulosMatricula:{$key}", now()->addMinute(), function () use ($where) {
                return LegacySchoolStage::query()->where($where)->count();
            });
        }

        $where = [
            'ref_cod_turma' => $matricula['ref_cod_turma'],
        ];

        $key = json_encode($where);

        return Cache::store('array')->remember("getQuantidadeDeModulosMatricula:{$key}", now()->addMinute(), function () use ($where) {
            return LegacySchoolClassStage::query()->where($where)->count();
        });
    }

    /**
     * Retorna um array com as informações de biblioteca a partir de seu código.
     *
     * @param int $id
     *
     * @return array
     *
     * @throws App_Model_Exception
     */
    public static function getBiblioteca($id)
    {
        $biblioteca = self::addClassToStorage(
            'clsPmieducarBiblioteca',
            null,
            'include/pmieducar/clsPmieducarBiblioteca.inc.php'
        );

        $biblioteca->cod_biblioteca = $id;
        $biblioteca = $biblioteca->detalhe();

        if (false === $biblioteca) {
            throw new App_Model_Exception(
                sprintf('Seu usuário não está vinculado a nenhuma biblioteca.', $id)
            );
        }

        return $biblioteca;
    }

    /**
     * Retorna todas as bibliotecas cadastradas na tabela pmieducar.biblioteca, selecionando
     * opcionalmente pelo código da instituição e/ ou escola.
     *
     * @param int $instituicaoId
     *
     * @return array
     */
    public static function getBibliotecas($instituicaoId = null, $escolaId = null)
    {
        $_bibliotecas = self::addClassToStorage(
            'clsPmieducarBiblioteca',
            null,
            'include/pmieducar/clsPmieducarBiblioteca.inc.php'
        );

        $bibliotecas = [];

        foreach ($_bibliotecas->lista(null, $instituicaoId, $escolaId) as $biblioteca) {
            $bibliotecas[$biblioteca['cod_biblioteca']] = $biblioteca['nm_biblioteca'];
        }

        return $bibliotecas;
    }

    /**
     * Retorna todas as situações cadastradas para as bibliotecas na tabela pmieducar.situacao, selecionando
     * opcionalmente pelo código da biblioteca.
     *
     * @param int $bibliotecaId
     *
     * @return array
     */
    public static function getBibliotecaSituacoes($bibliotecaId = null)
    {
        $_situacoes = self::addClassToStorage(
            'clsPmieducarSituacao',
            null,
            'include/pmieducar/clsPmieducarSituacao.inc.php'
        );

        $situacoes = [];

        foreach ($_situacoes->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, $bibliotecaId) as $situacao) {
            $situacoes[$situacao['cod_situacao']] = $situacao['nm_situacao'];
        }

        return $situacoes;
    }

    /**
     * Retorna todas as fontes cadastradas para as bibliotecas na tabela pmieducar.fonte, selecionando
     * opcionalmente pelo código da biblioteca.
     *
     * @param int $bibliotecaId
     *
     * @return array
     */
    public static function getBibliotecaFontes($bibliotecaId = null)
    {
        $_fontes = self::addClassToStorage(
            'clsPmieducarFonte',
            null,
            'include/pmieducar/clsPmieducarFonte.inc.php'
        );

        $fontes = [];

        foreach ($_fontes->lista(null, null, null, null, null, null, null, null, null, 1, $bibliotecaId) as $fonte) {
            $fontes[$fonte['cod_fonte']] = $fonte['nm_fonte'];
        }

        return $fontes;
    }

    /**
     * Retorna uma obra cadastrada para uma biblioteca na tabela pmieducar.acervo, selecionando
     * obrigatóriamente pelo código da biblioteca e opcionalmente pelo código da obra.
     *
     * @param int $bibliotecaId
     *
     * @return array
     *
     * @throws App_Model_Exception
     */
    public static function getBibliotecaObra($bibliotecaId, $id = null)
    {
        $obra = self::addClassToStorage(
            'clsPmieducarAcervo',
            null,
            'include/pmieducar/clsPmieducarAcervo.inc.php'
        );

        $obra->ref_cod_biblioteca = $$bibliotecaId;
        $obra->cod_acervo = $id;
        $obra = $obra->detalhe();

        if (false === $obra) {
            throw new App_Model_Exception(
                sprintf('Obra com o código "%d" não existe.', $id)
            );
        }

        return $obra;
    }

    /**
     * Retorna um aluno cadastrado para uma escola na tabela pmieducar.aluno, selecionando
     * obrigatóriamente pelo código da escola e opcionalmente pelo código do aluno.
     *
     * @param int $id
     *
     * @return array
     *
     * @throws App_Model_Exception
     */
    public static function getAluno($escolaId, $id)
    {
        $aluno = self::addClassToStorage(
            'clsPmieducarAluno',
            null,
            'include/pmieducar/clsPmieducarAluno.inc.php'
        );

        $aluno = $aluno->lista($id, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $escolaId);

        if (false === $aluno) {
            throw new App_Model_Exception(
                sprintf('Aluno com o código "%d" não existe.', $id)
            );
        }

        return $aluno[0];
    }

    /**
     * Retorna todos os tipos de cliente cadastrados para determinada biblioteca na tabela
     * pmieducar.cliente_tipo, selecionando obrigatoriamente pelo código da biblioteca.
     *
     * @param int $bibliotecaId
     *
     * @return array
     */
    public static function getBibliotecaTiposCliente($bibliotecaId)
    {
        $resources = self::addClassToStorage(
            'clsPmieducarClienteTipo',
            null,
            'include/pmieducar/clsPmieducarClienteTipo.inc.php'
        );

        $filtered_resources = [];

        foreach ($resources->lista(null, $bibliotecaId) as $resource) {
            $filtered_resources[$resource['cod_cliente_tipo']] = $resource['nm_tipo'];
        }

        return $filtered_resources;
    }

    /**
     *Retorna todas as áreas de conhecimento cadastradas para determinada instituição
     *
     * @param int $instituicaoId
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getAreasConhecimento($instituicaoId)
    {
        $resultado = [];

        $sql = '
            SELECT
                area_conhecimento.id AS id_teste,
                area_conhecimento.nome AS nome
            FROM modules.area_conhecimento
            WHERE instituicao_id = $1
            ORDER BY (lower(nome)) ASC
        ';

        $resultado = Portabilis_Utils_Database::fetchPreparedQuery($sql, ['params' => $instituicaoId]);

        return $resultado;
    }

    /**
     * Retorna todos os turnos
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getTurnos()
    {
        $sql = '
            SELECT id, nome
            FROM pmieducar.turma_turno
            WHERE ativo = 1
            ORDER BY (lower(nome)) ASC
        ';

        return Portabilis_Array_Utils::setAsIdValue(Portabilis_Utils_Database::fetchPreparedQuery($sql), 'id', 'nome');
    }

    /**
     * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        return [];
    }

    /**
     * Retorna um array com as etapas a partir da escola e ano letivo.
     *
     * @param int $ano
     * @param int $escola
     *
     * @return array
     */
    public static function getEtapasEscola($ano, $escola)
    {
        $etapas = self::addClassToStorage(
            'clsPmieducarAnoLetivoModulo',
            null,
            'include/pmieducar/clsPmieducarAnoLetivoModulo.inc.php'
        );

        $etapas->ref_ano = $ano;
        $etapas->ref_ref_cod_escola = $escola;

        $etapas = $etapas->getEtapas();

        $ret = [];

        foreach ($etapas as $etapa) {
            $ret[$etapa['id']] = $etapa['nome'];
        }

        return $ret;
    }

    /**
     * Retorna um array com as etapas definidas para o componente,
     * quando a regra "Permitir definir componentes em etapas específicas"
     * estiver sendo utilizada.
     *
     * @param int $turma
     * @param int $componente
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getEtapasComponente($turma, $componente)
    {
        $key = json_encode(compact('turma', 'componente'));

        return Cache::store('array')->remember("getEtapasComponente:{$key}", now()->addMinute(), function () use ($turma, $componente) {
            $sql = '
                SELECT componente_curricular_turma.etapas_utilizadas
                FROM modules.componente_curricular_turma
                WHERE componente_curricular_turma.turma_id = $1
                AND componente_curricular_turma.componente_curricular_id = $2
                AND componente_curricular_turma.etapas_especificas = 1
            ';

            $resultado = Portabilis_Utils_Database::fetchPreparedQuery($sql, ['params' => [$turma, $componente]]);

            if ($resultado) {
                return explode(',', $resultado[0]['etapas_utilizadas']);
            }

            $sql = '
            SELECT escola_serie_disciplina.etapas_utilizadas
            FROM pmieducar.escola_serie_disciplina
            INNER JOIN pmieducar.turma ON (turma.ref_ref_cod_serie = escola_serie_disciplina.ref_ref_cod_serie
            AND turma.ref_ref_cod_escola = escola_serie_disciplina.ref_ref_cod_escola)
            WHERE turma.cod_turma = $1
            AND escola_serie_disciplina.ref_cod_disciplina = $2
            AND escola_serie_disciplina.etapas_especificas = 1
        ';

            $resultado = Portabilis_Utils_Database::fetchPreparedQuery($sql, ['params' => [$turma, $componente]]);

            if ($resultado) {
                return explode(',', $resultado[0]['etapas_utilizadas']);
            }

            return [];
        });
    }

    /**
     * Retorna um array com as etapas dispensadas para o componente,
     *
     * @param int $enrollmentId
     * @param int $disciplineId
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getExemptedStages($enrollmentId, $disciplineId)
    {
        $stages = [];

        $sql = '
            SELECT distinct etapa, ref_cod_disciplina
            FROM pmieducar.dispensa_disciplina
            JOIN pmieducar.dispensa_etapa
            ON dispensa_disciplina.cod_dispensa = dispensa_etapa.ref_cod_dispensa
            WHERE ref_cod_matricula = $1
            order by etapa
        ';

        $query = Portabilis_Utils_Database::fetchPreparedQuery($sql, ['params' => [$enrollmentId]]);

        foreach ($query as $stage) {
            $stages[$stage['ref_cod_disciplina']][] = $stage['etapa'];
        }

        return $stages;
    }

    //Retorna a quantidade de etapas resgatadas na function getEtapasComponente
    public static function getQtdeEtapasComponente($turma, $componente)
    {
        $resultado = self::getEtapasComponente($turma, $componente);

        if ($resultado) {
            return count($resultado);
        }

        return null;
    }

    //Retorna a ultima etapa resgatada na function getEtapasComponente
    public static function getUltimaEtapaComponente($turma, $componente)
    {
        $resultado = self::getEtapasComponente($turma, $componente);

        if ($resultado) {
            return max($resultado);
        }

        return null;
    }

    public static function verificaSeExisteNotasComponenteCurricular($matricula, $componente)
    {
        $cc_nota = '
            SELECT count(ncc.componente_curricular_id) AS cc
            FROM modules.nota_aluno AS na
            INNER JOIN modules.nota_componente_curricular AS ncc ON (na.id = ncc.nota_aluno_id)
            WHERE na.matricula_id = $1
            AND ncc.componente_curricular_id = $2
        ';

        $resultado = Portabilis_Utils_Database::fetchPreparedQuery($cc_nota, ['params' => [$matricula, $componente]]);

        return $resultado;
    }

    public static function verificaSePossuiDeficiencia($alunoId)
    {
        $sql = '
            SELECT 1
            FROM cadastro.fisica_deficiencia fd
            JOIN PMIEDUCAR.ALUNO A
            ON fd.ref_idpes = a.ref_idpes
            JOIN cadastro.deficiencia d
            ON d.cod_deficiencia = fd.ref_cod_deficiencia
            WHERE a.cod_aluno = $1
            AND d.desconsidera_regra_diferenciada = false
            LIMIT 1
        ';

        return Portabilis_Utils_Database::selectField($sql, ['params' => [$alunoId]]) == 1;
    }

    public static function getNotasLancadasAluno($ref_cod_matricula, $ref_cod_disciplina, $etapa)
    {
        $notas_lancadas_aluno = '
            SELECT
                na.matricula_id,
                ncc.componente_curricular_id,
                ncc.nota,
                ncc.nota_recuperacao,
                ncc.nota_recuperacao_especifica,
                ncc.etapa
            FROM modules.nota_aluno AS na
            INNER JOIN modules.nota_componente_curricular AS ncc ON (na.id = ncc.nota_aluno_id)
            WHERE na.matricula_id = $1
            AND ncc.componente_curricular_id = $2
            AND ncc.etapa = $3
        ';

        $resultado = Portabilis_Utils_Database::fetchPreparedQuery($notas_lancadas_aluno, ['params' => [$ref_cod_matricula, $ref_cod_disciplina, $etapa]]);

        return $resultado;
    }

    public static function getFaltasLancadasAluno($ref_cod_matricula, $ref_cod_disciplina, $etapa)
    {
        $faltas_lancadas_aluno = '
            SELECT
                fa.matricula_id,
                fcc.componente_curricular_id,
                fcc.quantidade,
                fcc.etapa
            FROM modules.falta_aluno AS fa
            INNER JOIN modules.falta_componente_curricular AS fcc ON (fa.id = fcc.falta_aluno_id)
            WHERE fa.matricula_id = $1
            AND fcc.componente_curricular_id = $2
            AND fcc.etapa = $3
        ';

        $resultado = Portabilis_Utils_Database::fetchPreparedQuery($faltas_lancadas_aluno, ['params' => [$ref_cod_matricula, $ref_cod_disciplina, $etapa]]);

        return $resultado;
    }

    public static function getEscolasUser($cod_usuario)
    {
        $escolas_user = '
            SELECT
                escola_usuario.ref_cod_escola AS ref_cod_escola,
                coalesce(juridica.fantasia, escola_complemento.nm_escola) AS nome,
                escola.ref_cod_instituicao AS instituicao
            FROM pmieducar.escola_usuario
            INNER JOIN pmieducar.escola ON (escola.cod_escola = escola_usuario.ref_cod_escola)
            LEFT JOIN cadastro.juridica ON (juridica.idpes = escola.ref_idpes)
            LEFT JOIN pmieducar.escola_complemento ON (escola_complemento.ref_cod_escola = escola.cod_escola)
            WHERE escola_usuario.ref_cod_usuario = $1
        ';

        $resultado = Portabilis_Utils_Database::fetchPreparedQuery($escolas_user, ['params' => [$cod_usuario]]);

        return $resultado;
    }

    public static function usuarioNivelBibliotecaEscolar($codUsuario)
    {
        $permissao = new clsPermissoes();
        $nivel = $permissao->nivel_acesso($codUsuario);

        if ($nivel == App_Model_NivelTipoUsuario::ESCOLA ||
            $nivel == App_Model_NivelTipoUsuario::BIBLIOTECA) {
            return true;
        }

        return false;
    }

    /**
     * Retorna as etapas da turma.
     *
     * @param int $turma
     *
     * @return array|mixed
     *
     * @throws Exception
     */
    public static function getEtapasDaTurma($turma)
    {
        $sql = '
            select * from
            (
                select
                    t.cod_turma,
                    anm.sequencial,
                    anm.ref_cod_modulo as cod_modulo,
                    anm.data_inicio,
                    anm.data_fim,
                    anm.dias_letivos
                from pmieducar.turma as t
                inner join pmieducar.curso as c
                on t.ref_cod_curso = c.cod_curso
                inner join pmieducar.ano_letivo_modulo as anm
                on anm.ref_ref_cod_escola = t.ref_ref_cod_escola
                and anm.ref_ano = t.ano
                where c.padrao_ano_escolar = 1

                union all

                select
                    t.cod_turma,
                    tm.sequencial,
                    tm.ref_cod_modulo as cod_modulo,
                    tm.data_inicio,
                    tm.data_fim,
                    tm.dias_letivos
                from pmieducar.turma as t
                inner join pmieducar.curso as c
                on t.ref_cod_curso = c.cod_curso
                inner join pmieducar.turma_modulo as tm
                on tm.ref_cod_turma = t.cod_turma
                where c.padrao_ano_escolar = 0
            ) as etapas
            where cod_turma = $1;
        ';

        return Portabilis_Utils_Database::fetchPreparedQuery($sql, [
            'params' => [$turma]
        ]);
    }

    /**
     * @param int $etapaId
     *
     * @return array|null
     *
     * @throws Exception
     */
    public static function getEtapa($etapaId)
    {
        $sql = 'select * from pmieducar.modulo where cod_modulo = $1;';

        $query = Portabilis_Utils_Database::fetchPreparedQuery($sql, [
            'params' => [$etapaId]
        ]);

        if ($query && count($query)) {
            return array_shift($query);
        }

        return null;
    }
}
