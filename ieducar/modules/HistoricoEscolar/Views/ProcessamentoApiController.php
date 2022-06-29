<?php

use Illuminate\Support\Facades\DB;

// TODO migrar classe novo padrao api controller
class ProcessamentoApiController extends Core_Controller_Page_EditController
{
    protected $_dataMapper = 'Avaliacao_Model_NotaComponenteDataMapper';
    protected $_processoAp = 999613;
    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
    protected $_saveOption = false;
    protected $_deleteOption = false;
    protected $_titulo = '';
    public $DISCIPLINA_DISPENSADA = 'Disp';

    protected function validatesPresenceOf(
        &$value,
        $name,
        $raiseExceptionOnEmpty = false,
        $msg = '',
        $addMsgOnEmpty = true
    ) {
        if (!isset($value) || (empty($value) && !is_numeric($value))) {
            if ($addMsgOnEmpty) {
                $msg = empty($msg) ? "É necessário receber uma variavel '$name'" : $msg;
                $this->appendMsg($msg);
            }

            if ($raiseExceptionOnEmpty) {
                throw new Exception($msg);
            }

            return false;
        }

        return true;
    }

    protected function validatesValueIsNumeric(
        &$value,
        $name,
        $raiseExceptionOnError = false,
        $msg = '',
        $addMsgOnError = true
    ) {
        if (!is_numeric($value)) {
            if ($addMsgOnError) {
                $msg = empty($msg) ? "O valor recebido para variavel '$name' deve ser numerico" : $msg;
                $this->appendMsg($msg);
            }

            if ($raiseExceptionOnError) {
                throw new Exception($msg);
            }

            return false;
        }

        return true;
    }

    protected function validatesValueIsArray(
        &$value,
        $name,
        $raiseExceptionOnError = false,
        $msg = '',
        $addMsgOnError = true
    ) {
        if (!is_array($value)) {
            if ($addMsgOnError) {
                $msg = empty($msg) ? "Deve ser recebido uma lista de '$name'" : $msg;
                $this->appendMsg($msg);
            }

            if ($raiseExceptionOnError) {
                throw new Exception($msg);
            }

            return false;
        }

        return true;
    }

    protected function validatesValueInSetOf(
        &$value,
        $setExpectedValues,
        $name,
        $raiseExceptionOnError = false,
        $msg = ''
    ) {
        if (!in_array($value, $setExpectedValues)) {
            $msg = empty($msg) ? "Valor recebido na variavel '$name' é invalido" : $msg;
            $this->appendMsg($msg);

            if ($raiseExceptionOnError) {
                throw new Exception($msg);
            }

            return false;
        }

        return true;
    }

    protected function requiresLogin($raiseExceptionOnEmpty)
    {
        return $this->validatesPresenceOf(
            \Illuminate\Support\Facades\Auth::id(),
            '',
            $raiseExceptionOnEmpty,
            'Usuário deve estar logado'
        );
    }

    protected function validatesPresenceOfInstituicaoId($raiseExceptionOnEmpty)
    {
        return $this->validatesPresenceOf(
            $this->getRequest()->instituicao_id,
            'instituicao_id',
            $raiseExceptionOnEmpty
        );
    }

    protected function validatesPresenceOfEscolaId($raiseExceptionOnEmpty)
    {
        return $this->validatesPresenceOf($this->getRequest()->escola_id, 'escola_id', $raiseExceptionOnEmpty);
    }

    protected function validatesPresenceOfCursoId($raiseExceptionOnEmpty)
    {
        return $this->validatesPresenceOf($this->getRequest()->curso_id, 'curso_id', $raiseExceptionOnEmpty);
    }

    protected function validatesPresenceOfSerieId($raiseExceptionOnEmpty, $addMsgOnEmpty = true)
    {
        return $this->validatesPresenceOf(
            $this->getRequest()->serie_id,
            'serie_id',
            $raiseExceptionOnEmpty,
            '',
            $addMsgOnEmpty
        );
    }

    protected function validatesPresenceOfAno($raiseExceptionOnEmpty)
    {
        return $this->validatesPresenceOf($this->getRequest()->ano, 'ano', $raiseExceptionOnEmpty);
    }

    protected function validatesPresenceOfMatriculaId($raiseExceptionOnEmpty)
    {
        return $this->validatesPresenceOf($this->getRequest()->matricula_id, 'matricula_id', $raiseExceptionOnEmpty);
    }

    protected function validatesValueIsInBd($fieldName, &$value, $schemaName, $tableName, $raiseExceptionOnError = true)
    {
        $sql = "select 1 from $schemaName.$tableName where $fieldName = $1";
        $isValid = Portabilis_Utils_Database::selectField($sql, $value) == '1';

        if (!$isValid) {
            $msg = "O valor informado {$value} para $tableName, não esta presente no banco de dados.";
            $this->appendMsg($msg);

            if ($raiseExceptionOnError) {
                throw new Exception($msg);
            }

            return false;
        }

        return true;
    }

    protected function validatesPresenceAndValueInDbOfGradeCursoId($raiseExceptionOnError)
    {
        return $this->validatesPresenceOf(
            $this->getRequest()->grade_curso_id,
            'grade_curso_id',
            $raiseExceptionOnError
        ) &&
            $this->validatesValueIsInBd(
                'id',
                $this->getRequest()->grade_curso_id,
                'pmieducar',
                'historico_grade_curso',
                $raiseExceptionOnError
            );
    }

    protected function validatesPresenceOfDiasLetivos($raiseExceptionOnEmpty)
    {
        return $this->validatesPresenceOf($this->getRequest()->dias_letivos, 'dias_letivos', $raiseExceptionOnEmpty);
    }

    protected function validatesValueOfAttValueIsNumeric($raiseExceptionOnError)
    {
        return $this->validatesValueIsNumeric($this->getRequest()->att_value, 'att_value', $raiseExceptionOnError);
    }

    protected function validatesPresenceOfAttValue($raiseExceptionOnEmpty)
    {
        return $this->validatesPresenceOf($this->getRequest()->att_value, 'att_value', $raiseExceptionOnEmpty);
    }

    protected function validatesPresenceAndValueInSetOfAtt($raiseExceptionOnError)
    {
        $result = $this->validatesPresenceOf($this->getRequest()->att, 'att', $raiseExceptionOnError);

        if ($result) {
            $expectedAtts = ['matriculas', 'processamento', 'historico'];
            $result = $this->validatesValueInSetOf(
                $this->getRequest()->att,
                $expectedAtts,
                'att',
                $raiseExceptionOnError
            );
        }

        return $result;
    }

    protected function validatesPresenceAndValueInSetOfOper($raiseExceptionOnError)
    {
        $result = $this->validatesPresenceOf($this->getRequest()->oper, 'oper', $raiseExceptionOnError);

        if ($result) {
            $expectedOpers = ['post', 'get', 'delete'];
            $result = $this->validatesValueInSetOf(
                $this->getRequest()->oper,
                $expectedOpers,
                'oper',
                $raiseExceptionOnError
            );
        }

        return $result;
    }

    protected function validatesPresenceAndValueInSetOfExtraCurricular($raiseExceptionOnError)
    {
        $result = $this->validatesPresenceOf(
            $this->getRequest()->extra_curricular,
            'extra_curricular',
            $raiseExceptionOnError
        );

        if ($result) {
            $expectedOpers = [0, 1];
            $result = $this->validatesValueInSetOf(
                $this->getRequest()->extra_curricular,
                $expectedOpers,
                'extra_curricular',
                $raiseExceptionOnError
            );
        }

        return $result;
    }

    protected function validatesPresenceAndValueOfPercentualFrequencia($raiseExceptionOnError)
    {
        $name = 'percentual_frequencia';
        $isValid = $this->validatesPresenceOf(
            $this->getRequest()->percentual_frequencia,
            $name,
            $raiseExceptionOnError
        );

        if ($isValid && $this->getRequest()->percentual_frequencia != 'buscar-boletim') {
            $isValid = $this->validatesValueIsNumeric(
                $this->getRequest()->percentual_frequencia,
                $name,
                $raiseExceptionOnError
            );
        }

        return $isValid;
    }

    protected function validatesPresenceOfNotas($raiseExceptionOnError)
    {
        return $this->validatesPresenceOf($this->getRequest()->notas, 'notas', $raiseExceptionOnError);
    }

    protected function validatesPresenceAndValueOfFaltas($raiseExceptionOnError)
    {
        $name = 'faltas';
        $isValid = $this->validatesPresenceOf($this->getRequest()->faltas, $name, $raiseExceptionOnError);

        if ($isValid && $this->getRequest()->faltas != 'buscar-boletim') {
            $isValid = $this->validatesValueIsNumeric($this->getRequest()->faltas, $name, $raiseExceptionOnError);
        }

        return $isValid;
    }

    protected function validatesPresenceAndValueOfDisciplinas($raiseExceptionOnError)
    {
        $name = 'disciplinas';
        $isValid = $this->validatesPresenceOf($this->getRequest()->disciplinas, $name, $raiseExceptionOnError);

        if ($isValid && $this->getRequest()->disciplinas != 'buscar-boletim') {
            $isValid = $this->validatesValueIsArray(
                $this->getRequest()->disciplinas,
                'disciplinas',
                $raiseExceptionOnError
            );
            if ($isValid) {
                foreach ($this->getRequest()->disciplinas as $disciplina) {
                    $isValid = $this->validatesPresenceOf(
                        $disciplina['nome'],
                        'nome (para todas disciplinas)',
                        $raiseExceptionOnError
                    );

                    if ($isValid && isset($disciplina['falta']) && trim($disciplina['falta']) != '') {
                        $isValid = $this->validatesValueIsNumeric(
                            $disciplina['falta'],
                            'falta (para todas disciplinas)',
                            $raiseExceptionOnError
                        );
                    }
                }
            }
        }

        return $isValid;
    }

    protected function validatesPresenceAndValueInSetOfSituacao($raiseExceptionOnError)
    {
        $name = 'situacao';
        $isValid = $this->validatesPresenceOf($this->getRequest()->situacao, $name, $raiseExceptionOnError);

        if ($isValid) {
            $expectedOpers = ['buscar-matricula', 'aprovado', 'reprovado', 'em-andamento', 'transferido', 'reclassificado', 'abandono'];
            $isValid = $this->validatesValueInSetOf(
                $this->getRequest()->situacao,
                $expectedOpers,
                $name,
                $raiseExceptionOnError
            );
        }

        return $isValid;
    }

    /* esta funcao só pode ser chamada após setar $this->getService() */
    protected function validatesPresenceOfComponenteCurricularId($raiseExceptionOnEmpty, $addMsgOnEmpty = true)
    {
        return $this->validatesPresenceOf(
            $this->getRequest()->componente_curricular_id,
            'componente_curricular_id',
            $raiseExceptionOnEmpty,
            $msg = '',
            $addMsgOnEmpty
        );
    }

    protected function canAcceptRequest()
    {
        try {
            $this->requiresLogin(true);
            $this->validatesPresenceAndValueInSetOfAtt(true);
            $this->validatesPresenceAndValueInSetOfOper(true);
        } catch (Exception) {
            return false;
        }

        return true;
    }

    protected function canGetMatriculas()
    {
        return $this->validatesPresenceOfAno(false) &&
            $this->validatesPresenceOfInstituicaoId(false) &&
            $this->validatesPresenceOfEscolaId(false);
    }

    protected function canPostProcessamento()
    {
        $canPost = $this->validatesPresenceOfInstituicaoId(false) &&
            $this->validatesPresenceOfMatriculaId(false) &&
            $this->validatesPresenceOfDiasLetivos(false) &&
            $this->validatesPresenceAndValueInSetOfSituacao(false) &&
            $this->validatesPresenceAndValueInSetOfExtraCurricular(false) &&
            $this->validatesPresenceAndValueInDbOfGradeCursoId(false) &&
            $this->validatesPresenceAndValueOfPercentualFrequencia(false) &&
            $this->validatesPresenceAndValueOfDisciplinas(false);

        if ($canPost && $this->getRequest()->disciplinas == 'buscar-boletim') {
            $canPost = $this->validatesPresenceOfNotas(false) && $this->validatesPresenceAndValueOfFaltas(false);
        }

        if ($canPost) {
            $sql = 'select 1 from pmieducar.matricula where cod_matricula = $1 and ativo = 1';

            if (!Portabilis_Utils_Database::selectField($sql, $this->getRequest()->matricula_id)) {
                $this->appendMsg(
                    "A matricula {$this->getRequest()->matricula_id} não existe ou esta desativa",
                    'error'
                );
                $canPost = false;
            }
        }

        if ($canPost) {
            $sql = 'select 1 from pmieducar.matricula_turma where ref_cod_matricula = $1 and ativo = 1 limit 1';

            if (!Portabilis_Utils_Database::selectField($sql, $this->getRequest()->matricula_id)) {
                $this->appendMsg("A matricula {$this->getRequest()->matricula_id} não está enturmada.", 'error');
                $canPost = false;
            }
        }

        return $canPost && $this->setService();
    }

    protected function canDeleteHistorico()
    {
        return $this->validatesPresenceOfInstituicaoId(false) &&
            $this->validatesPresenceOfMatriculaId(false);
    }

    protected function deleteHistorico()
    {
        if ($this->canDeleteHistorico()) {
            $matriculaId = $this->getRequest()->matricula_id;
            $alunoId = $this->getAlunoIdByMatriculaId($matriculaId);
            $dadosMatricula = $this->getdadosMatricula($matriculaId);
            $ano = $dadosMatricula['ano'];

            if ($this->existsHistorico($alunoId, $ano, $matriculaId)) {
                $sequencial = $this->getSequencial($alunoId, $ano, $matriculaId);
                $this->deleteHistoricoDisplinas($alunoId, $sequencial);

                $historicoEscolar = new clsPmieducarHistoricoEscolar(
                    $ref_cod_aluno = $alunoId,
                    $sequencial,
                    $ref_usuario_exc = \Illuminate\Support\Facades\Auth::id(),
                    $ref_usuario_cad = null,
                    //TODO nm_curso
                    $nm_serie = null,
                    $ano = $ano,
                    $carga_horaria = null,
                    $dias_letivos = null,
                    $escola = null,
                    $escola_cidade = null,
                    $escola_uf = null,
                    $observacao = null,
                    $aprovado = null,
                    $data_cadastro = null,
                    $data_exclusao = date('Y-m-d'),
                    $ativo = 0
                );
                $historicoEscolar->edita();

                $this->appendMsg('Histórico escolar removido com sucesso', 'success');
            } else {
                $this->appendMsg("Histórico matricula $matriculaId inexistente ou já removido", 'notice');
            }

            $situacaoHistorico = $this->getSituacaoHistorico($alunoId, $ano, $matriculaId, $reload = true);

            $this->appendResponse('situacao_historico', $situacaoHistorico);
            $this->appendResponse('link_to_historico', '');
        }
    }

    protected function deleteHistoricoDisplinas($alunoId, $historicoSequencial)
    {
        $historicoDisciplinas = new clsPmieducarHistoricoDisciplinas();
        $historicoDisciplinas->excluirTodos($alunoId, $historicoSequencial);
    }

    protected function getdadosEscola($escolaId)
    {
        $sql = 'select
            (select pes.nome from pmieducar.escola esc, cadastro.pessoa pes
            where esc.ref_cod_instituicao = $1 and esc.cod_escola = $2
            and pes.idpes = esc.ref_idpes) as nome,

            (select municipio.nome from public.municipio,
            cadastro.endereco_pessoa, cadastro.juridica, public.bairro, pmieducar.escola
            where endereco_pessoa.idbai = bairro.idbai and bairro.idmun = municipio.idmun and
            juridica.idpes = endereco_pessoa.idpes and juridica.idpes = escola.ref_idpes and
            escola.cod_escola = $2) as cidade,

            (select municipio.sigla_uf from public.municipio,
            cadastro.endereco_pessoa, cadastro.juridica, public.bairro, pmieducar.escola
            where endereco_pessoa.idbai = bairro.idbai and bairro.idmun = municipio.idmun and
            juridica.idpes = endereco_pessoa.idpes and juridica.idpes = escola.ref_idpes and
            escola.cod_escola = $2) as uf';

        $params = ['params' => [$this->getrequest()->instituicao_id, $escolaId], 'return_only' => 'first-line'];

        return Portabilis_Utils_Database::fetchPreparedQuery($sql, $params);
    }

    protected function getNextHistoricoSequencial($alunoId)
    {
        //A consulta leva em consideração historicos inativos pois o sequencial é chave composta com ref_cod_aluno id
        $sql = 'select coalesce(max(sequencial), 0) + 1 from pmieducar.historico_escolar where ref_cod_aluno = $1';

        return Portabilis_Utils_Database::selectField($sql, $alunoId);
    }

    protected function getNextHistoricoDisciplinasSequencial($historicoSequencial, $alunoId)
    {
        $sql = 'select coalesce(max(sequencial), 0) + 1 from pmieducar.historico_disciplinas where
            ref_sequencial = $1 and ref_ref_cod_aluno = $2';

        return Portabilis_Utils_Database::selectField($sql, [$historicoSequencial, $alunoId]);
    }

    protected function getSituacaoMatricula($matriculaId = null)
    {
        if (!is_null($matriculaId)) {
            if (!is_null($this->getService(false, false))) {
                $situacao = $this->getService()->getOption('aprovado');
            } else {
                $sql = 'select aprovado from pmieducar.matricula where cod_matricula = $1';
                $situacao = Portabilis_Utils_Database::selectField($sql, $matriculaId);
            }
        } elseif ($this->getRequest()->situacao == 'buscar-matricula') {
            $situacao = $this->getService()->getOption('aprovado');
        } else {
            $situacoes = [
                'aprovado' => App_Model_MatriculaSituacao::APROVADO,
                'reprovado' => App_Model_MatriculaSituacao::REPROVADO,
                'em-andamento' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
                'transferido' => App_Model_MatriculaSituacao::TRANSFERIDO,
                'reclassificado' => App_Model_MatriculaSituacao::RECLASSIFICADO,
                'abandono' => App_Model_MatriculaSituacao::ABANDONO
            ];
            $situacao = $situacoes[$this->getRequest()->situacao];
        }

        return $situacao;
    }

    protected function getPercentualFrequencia()
    {
        if ($this->getRequest()->percentual_frequencia == 'buscar-boletim') {
            $percentual = round($this->getService()->getSituacaoFaltas(true)->porcentagemPresenca, 2);
        } else {
            $percentual = $this->getRequest()->percentual_frequencia;
        }

        return str_replace(',', '.', $percentual);
    }

    protected function getFaltaGlobalizada($defaultValue = null)
    {
        $faltaGeral = $this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL;

        if (!$faltaGeral) {
            return $defaultValue;
        }

        return $this->getFalta();
    }

    protected function postProcessamento()
    {
        if ($this->canPostProcessamento()) {
            $matriculaId = $this->getRequest()->matricula_id;
            try {
                DB::beginTransaction();

                $alunoId = $this->getAlunoIdByMatriculaId($matriculaId);
                $dadosMatricula = $this->getdadosMatricula($matriculaId);
                $dadosEscola = $this->getdadosEscola($dadosMatricula['escola_id']);
                $ano = $dadosMatricula['ano'];
                $isNewHistorico = !$this->existsHistorico($alunoId, $ano, $matriculaId);

                if ($isNewHistorico) {
                    $sequencial = $this->getNextHistoricoSequencial($alunoId);

                    $historicoEscolar = new clsPmieducarHistoricoEscolar(
                        $alunoId,
                        $sequencial,
                        $ref_usuario_exc = null,
                        $ref_usuario_cad = \Illuminate\Support\Facades\Auth::id(),
                        $dadosMatricula['nome_serie'],
                        $ano,
                        $this->getCargaHorariaDisciplinas($alunoId),
                        $this->getRequest()->dias_letivos,
                        mb_strtoupper($dadosEscola['nome']),
                        mb_strtoupper($dadosEscola['cidade']),
                        $dadosEscola['uf'],
                        $this->getRequest()->observacao,
                        $this->getSituacaoMatricula(),
                        $data_cadastro = date('Y-m-d'),
                        $data_exclusao = null,
                        $ativo = 1,
                        $this->getFaltaGlobalizada($defaultValue = 'null'),
                        $dadosMatricula['instituicao_id'],
                        $origem = '', //TODO
                        $this->getRequest()->extra_curricular,
                        $matriculaId,
                        $this->getPercentualFrequencia(),
                        $this->getRequest()->registro,
                        $this->getRequest()->livro,
                        $this->getRequest()->folha,
                        $dadosMatricula['nome_curso'],
                        $this->getRequest()->grade_curso_id,
                        null,
                        $dadosMatricula['escola_id'],
                        $this->getRequest()->dependencia,
                        $this->getRequest()->posicao
                    );

                    $historicoEscolar->cadastra();
                    $this->recreateHistoricoDisciplinas($sequencial, $alunoId, $dadosMatricula['turma_id']);

                    $this->appendMsg('Histórico processado com sucesso', 'success');
                } else {
                    $sequencial = $this->getSequencial($alunoId, $ano, $matriculaId);

                    $historicoEscolar = new clsPmieducarHistoricoEscolar(
                        $alunoId,
                        $sequencial,
                        \Illuminate\Support\Facades\Auth::id(),
                        $ref_usuario_cad = null,
                        $dadosMatricula['nome_serie'],
                        $ano,
                        $this->getCargaHorariaDisciplinas($alunoId),
                        $this->getRequest()->dias_letivos,
                        mb_strtoupper($dadosEscola['nome']),
                        mb_strtoupper($dadosEscola['cidade']),
                        $dadosEscola['uf'],
                        $this->getRequest()->observacao,
                        $this->getSituacaoMatricula(),
                        $data_cadastro = null,
                        $data_exclusao = null,
                        $ativo = 1,
                        $this->getFaltaGlobalizada($defaultValue = 'null'),
                        $dadosMatricula['instituicao_id'],
                        $origem = '', //TODO
                        $this->getRequest()->extra_curricular,
                        $matriculaId,
                        $this->getPercentualFrequencia(),
                        $this->getRequest()->registro,
                        $this->getRequest()->livro,
                        $this->getRequest()->folha,
                        $dadosMatricula['nome_curso'],
                        $this->getRequest()->grade_curso_id,
                        null,
                        $dadosMatricula['escola_id'],
                        $this->getRequest()->dependencia,
                        $this->getRequest()->posicao
                    );

                    $historicoEscolar->edita();
                    $this->recreateHistoricoDisciplinas($sequencial, $alunoId, $dadosMatricula['turma_id']);
                    $this->appendMsg('Histórico reprocessado com sucesso', 'success');
                }
            } catch (Exception $e) {
                DB::rollBack();
                $this->appendMsg('Erro ao processar histórico, detalhes:' . $e->getMessage(), 'error', true);
            }

            $situacaoHistorico = $this->getSituacaoHistorico($alunoId, $ano, $matriculaId, $reload = true);
            $linkToHistorico = $this->getLinkToHistorico($alunoId, $ano, $matriculaId);

            $this->appendResponse('situacao_historico', $situacaoHistorico);
            $this->appendResponse('link_to_historico', $linkToHistorico);
            DB::commit();
        }
    }

    protected function _createHistoricoDisciplinas($fields)
    {
        $historicoDisciplina = new clsPmieducarHistoricoDisciplinas(
            $fields['sequencial'],
            $fields['alunoId'],
            $fields['historicoSequencial'],
            $fields['nome'],
            $fields['nota'],
            $fields['falta'],
            $fields['ordenamento'],
            $fields['carga_horaria_disciplina'],
            $fields['dependencia'],
            $fields['tipo_base']
        );
        $historicoDisciplina->cadastra();
    }

    protected function shouldProcessAreaConhecimento($areaConhecimento)
    {
        if (!empty($this->getRequest()->area_conhecimento)) {
            return in_array($areaConhecimento, $this->getRequest()->area_conhecimento);
        }

        return true;
    }

    protected function getCargaHorariaDisciplinas($alunoId)
    {
        $checked = $this->getQueryString('emitir_carga_disciplinas');
        if (empty($checked)) {
            return $this->getService()->getOption('serieCargaHoraria');
        }

        $carga_horaria_disciplinas = 0;

        foreach ($this->getService()->getComponentes() as $componenteCurricular) {
            if (!$this->shouldProcessAreaConhecimento($componenteCurricular->get('area_conhecimento'))) {
                continue;
            }
            $carga_horaria_disciplinas += $componenteCurricular->cargaHoraria;
        }

        return $carga_horaria_disciplinas;
    }

    protected function recreateHistoricoDisciplinas($historicoSequencial, $alunoId, $turmaId = null)
    {
        $this->deleteHistoricoDisplinas($alunoId, $historicoSequencial);

        if ($this->getRequest()->disciplinas === 'buscar-boletim') {
            $tpNota = $this->getService()->getRegra()->get('tipoNota');
            $situacaoFaltasCc = $this->getService()->getSituacaoFaltas()->componentesCurriculares;
            $mediasCc = $this->getService()->getMediasComponentes();
            $turmaId = $this->getRequest()->turma_id ?: $turmaId;
            $mediaAreaConhecimento = $this->getRequest()->media_area_conhecimento;
            $processarMediaGeral = $this->getRequest()->processar_media_geral;
            $casasDecimais = $this->getService()->getRegra()->get('qtdCasasDecimais');
            $aprovadoDependencia = $this->getSituacaoMatricula() == 12;

            $isGlobalScoreForStage = $this->getService()->getEvaluationRule()->isGlobalScore();

            foreach ($this->getService()->getComponentes() as $componenteCurricular) {
                if (!$this->shouldProcessAreaConhecimento($componenteCurricular->get('area_conhecimento'))) {
                    continue;
                }
                $ccId = $componenteCurricular->get('id');
                $reprovado = $mediasCc[$ccId][0]->situacao == 2;
                $disciplinaDependencia = ($aprovadoDependencia && $reprovado);
                $nome = $componenteCurricular->nome;
                $ordenamento = $componenteCurricular->ordenamento;
                $tipo_base = $componenteCurricular->get('tipo_base');
                $carga_horaria_disciplina = $componenteCurricular->cargaHoraria;
                $sequencial = $this->getNextHistoricoDisciplinasSequencial($historicoSequencial, $alunoId);
                $nota = '';
                $notaConceitualNumerica = '';

                if (clsPmieducarTurma::verificaDisciplinaDispensada($turmaId, $ccId)) {
                    $nota = $this->DISCIPLINA_DISPENSADA;
                } elseif ($this->getRequest()->notas == 'buscar-boletim') {
                    if ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL) {
                        if (config('legacy.app.processar_historicos_conceituais') == '1') {
                            $nota = (string)$mediasCc[$ccId][0]->mediaArredondada;
                            $notaConceitualNumerica = (string)$mediasCc[$ccId][0]->media;
                        }
                    } elseif ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA) {
                        $nota = (string)$mediasCc[$ccId][0]->mediaArredondada;
                    } elseif ($tpNota == RegraAvaliacao_Model_Nota_TipoValor::NUMERICACONCEITUAL) {
                        $nota = (string)$mediasCc[$ccId][0]->mediaArredondada;
                        $notaConceitualNumerica = (string)$mediasCc[$ccId][0]->media;
                    }
                } else {
                    $nota = $this->getRequest()->notas;
                }

                if (is_numeric($nota)) {
                    $nota = sprintf('%.'.$casasDecimais.'f', $nota);
                }

                if (is_numeric($notaConceitualNumerica)) {
                    $notaConceitualNumerica = sprintf('%.'.$casasDecimais.'f', $notaConceitualNumerica);
                }

                if ($processarMediaGeral && $isGlobalScoreForStage) {
                    $nota = '-';
                }

                if ($mediaAreaConhecimento) {
                    $nota = (float) str_replace(',', '.', $nota);
                    $arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['nome'] = $componenteCurricular->area_conhecimento->nome;
                    if ($arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['nota'] === null) {
                        $arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['nota'] = 0;
                    }

                    $arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['nota_conceitual_numerica'] ??= 0 ;
                    $arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['falta'] ??= 0;

                    $arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['nota'] += $nota;
                    $arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['nota_conceitual_numerica'] += is_numeric($notaConceitualNumerica) ? $notaConceitualNumerica : 0;
                    $arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['falta'] += $this->getFalta($situacaoFaltasCc[$ccId]);
                    $arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['ordenamento'] = $componenteCurricular->area_conhecimento->ordenamento;
                    $arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['carga_horaria_disciplina'] = $componenteCurricular->area_conhecimento->carga_horaria_disciplina;
                    $arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['dependencia'] = $disciplinaDependencia;
                    $arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['tipo_base'] = $tipo_base;
                    $arrayAreaConhecimento[$componenteCurricular->area_conhecimento->id]['count']++;
                } else {
                    $this->_createHistoricoDisciplinas(
                        [
                            'sequencial' => $sequencial,
                            'alunoId' => $alunoId,
                            'historicoSequencial' => $historicoSequencial,
                            'nome' => $nome,
                            'nota' => $nota,
                            'falta' => $this->getFalta($situacaoFaltasCc[$ccId]),
                            'ordenamento' => $ordenamento,
                            'carga_horaria_disciplina' => $carga_horaria_disciplina,
                            'dependencia' => $disciplinaDependencia,
                            'tipo_base' => $tipo_base,
                        ]
                    );
                }
            }

            if ($mediaAreaConhecimento) {
                foreach ($arrayAreaConhecimento as $key => $value) {
                    $sequencial = $this->getNextHistoricoDisciplinasSequencial($historicoSequencial, $alunoId);

                    if ($this->getRequest()->notas == 'buscar-boletim') {
                        $nota = number_format(($value['nota_conceitual_numerica'] / $value['count']), 2, ',', '');
                    }

                    if ($processarMediaGeral && $isGlobalScoreForStage) {
                        $nota = '-';
                    }

                    $this->_createHistoricoDisciplinas(
                        [
                            'sequencial' => $sequencial,
                            'alunoId' => $alunoId,
                            'historicoSequencial' => $historicoSequencial,
                            'nome' => $value['nome'],
                            'nota' => $nota,
                            'falta' => round($value['falta'] / $value['count']),
                            'ordenamento' => $value['ordenamento'],
                            'carga_horaria_disciplina' => $value['carga_horaria_disciplina'],
                            'dependencia' => $value['dependencia'],
                            'tipo_base' => $tipo_base,
                        ]
                    );
                }
            }
            if ($processarMediaGeral) {
                $this->insereComponenteMediaGeral($historicoSequencial, $alunoId);
            }
        } else {
            $i = 0;

            foreach ($this->getRequest()->disciplinas as $disciplina) {
                $sequencial = $this->getNextHistoricoDisciplinasSequencial($historicoSequencial, $alunoId);

                $i++;

                $tipo_base = ComponenteCurricular_Model_TipoBase::DEFAULT;
                $ordenamento = $i;

                $componenteCurricular = (new ComponenteCurricular_Model_ComponenteDataMapper())->findByName($disciplina['nome']);

                if ($componenteCurricular) {
                    $ordenamento = $componenteCurricular->ordenamento;
                    $tipo_base = $componenteCurricular->get('tipo_base');
                }

                $this->_createHistoricoDisciplinas(
                    [
                        'sequencial' => $sequencial,
                        'alunoId' => $alunoId,
                        'historicoSequencial' => $historicoSequencial,
                        'nome' => $disciplina['nome'],
                        'nota' => $disciplina['nota'],
                        'falta' => $falta = $disciplina['falta'],
                        'ordenamento' => $ordenamento,
                        'carga_horaria_disciplina' => null, // Não existe esta informação
                        'dependencia' => false, // Não existe esta informação
                        'tipo_base' => $tipo_base,
                    ]
                );
            }
        }
    }

    protected function insereComponenteMediaGeral($historicoSequencial, $alunoId)
    {
        $sequencial = $this->getNextHistoricoDisciplinasSequencial($historicoSequencial, $alunoId);
        $historicoEscolar = new clsPmieducarHistoricoEscolar($alunoId, $historicoSequencial);

        $historicoEscolar->insereComponenteMediaGeral($sequencial);
    }

    protected function getFalta($situacaoFaltaComponenteCurricular = null)
    {
        if ($this->getRequest()->faltas == 'buscar-boletim') {
            $cnsPresenca = RegraAvaliacao_Model_TipoPresenca::class;
            $tpPresenca = $this->getService()->getRegra()->get('tipoPresenca');

            //retorna '' caso não exista situacaoFalta para o componente curricular,
            //como nos casos em que a regra de avaliação muda
            if ($tpPresenca == $cnsPresenca::POR_COMPONENTE && !is_null($situacaoFaltaComponenteCurricular)) {
                $falta = $situacaoFaltaComponenteCurricular->total;
            } elseif ($tpPresenca == $cnsPresenca::POR_COMPONENTE) {
                $falta = '';
            } elseif ($tpPresenca == $cnsPresenca::GERAL) {
                $falta = $this->getService()->getSituacaoFaltas()->totalFaltas;
            }
        } else {
            $falta = $this->getRequest()->faltas;
        }

        return empty($falta) ? 0 : $falta;
    }

    protected function getDadosMatricula($matriculaId)
    {
        $ano = $this->getAnoMatricula($matriculaId);
        $sql = 'select ref_ref_cod_serie as serie_id, ref_cod_curso as curso_id from pmieducar.matricula
                      where cod_matricula = $1';

        $params = ['params' => $matriculaId, 'return_only' => 'first-line'];
        $idsSerieCurso = Portabilis_Utils_Database::fetchPreparedQuery($sql, $params);

        $matriculaTurma = new clsPmieducarMatriculaTurma();
        $matriculaTurma = $matriculaTurma->lista(
            $matriculaId,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $idsSerieCurso['serie_id'],
            $idsSerieCurso['curso_id']
        );

        $matriculaTurma = $matriculaTurma[0];

        $dadosMatricula = [];

        if (is_array($matriculaTurma) && count($matriculaTurma) > 0) {
            $dadosMatricula['ano'] = $ano;
            $dadosMatricula['instituicao_id'] = $matriculaTurma['ref_cod_instituicao'];
            $dadosMatricula['escola_id'] = $matriculaTurma['ref_ref_cod_escola'];
            $dadosMatricula['turma_id'] = $matriculaTurma['ref_cod_turma'];
            $dadosMatricula['nome_serie'] = $this->getNomeSerie($matriculaTurma['ref_ref_cod_serie']);

            $dadosMatricula['nome_curso'] = $matriculaTurma['nm_curso'];
        } else {
            throw new Exception("Não foi possivel recuperar os dados da matricula: $matriculaId.");
        }

        return $dadosMatricula;
    }

    protected function getAlunoIdByMatriculaId($matriculaId)
    {
        $sql = 'select ref_cod_aluno from pmieducar.matricula where cod_matricula = $1';

        return Portabilis_Utils_Database::selectField($sql, $matriculaId);
    }

    protected function getAnoMatricula($matriculaId)
    {
        $sql = 'select ano from pmieducar.matricula where cod_matricula = $1';

        return Portabilis_Utils_Database::selectField($sql, $matriculaId);
    }

    protected function getNomeSerie($serieId)
    {
        $sql = 'select nm_serie from pmieducar.serie where cod_serie = $1';

        return Portabilis_Utils_Database::selectField($sql, $serieId);
    }

    protected function getSequencial($alunoId, $ano, $matriculaId)
    {
        $sql = 'select sequencial from pmieducar.historico_escolar where ref_cod_aluno = $1 and ano = $2
            and ref_cod_instituicao = $3 and ref_cod_matricula = $4 and ativo = 1 limit 1';

        $params = [$alunoId, $ano, $this->getRequest()->instituicao_id, $matriculaId];

        return Portabilis_Utils_Database::selectField($sql, $params);
    }

    protected function existsHistorico($alunoId, $ano, $matriculaId, $ativo = 1, $reload = false)
    {
        if (!isset($this->existsHistorico) || $reload) {
            $sql = 'select 1 from pmieducar.historico_escolar where ref_cod_aluno = $1 and ano = $2
              and ref_cod_instituicao = $3 and ref_cod_matricula = $4 and ativo = $5';

            $params = [$alunoId, $ano, $this->getRequest()->instituicao_id, $matriculaId, $ativo];
            $this->existsHistorico = Portabilis_Utils_Database::selectField($sql, $params) == 1;
        }

        return $this->existsHistorico;
    }

    protected function getSituacaoHistorico($alunoId, $ano, $matriculaId, $reload = false)
    {
        if ($this->existsHistorico($alunoId, $ano, $matriculaId, 1, $reload)) {
            $situacao = 'Processado';
        } else {
            $situacao = 'Sem histórico';
        }

        return $this->toUtf8($situacao);
    }

    protected function getLinkToHistorico($alunoId, $ano, $matriculaId)
    {
        $sql = 'select sequencial from pmieducar.historico_escolar where ref_cod_aluno = $1 and
            ano = $2 and ref_cod_instituicao = $3 and ref_cod_matricula = $4 and ativo = 1';

        $params = [$alunoId, $ano, $this->getRequest()->instituicao_id, $matriculaId];
        $sequencial = Portabilis_Utils_Database::selectField($sql, $params);

        if (is_numeric($sequencial)) {
            $link = "/intranet/educar_historico_escolar_det.php?ref_cod_aluno=$alunoId&sequencial=$sequencial";
        } else {
            $link = '';
        }

        return $link;
    }

    protected function getMatriculas()
    {
        $matriculas = [];

        if ($this->canGetMatriculas()) {
            $alunos = new clsPmieducarMatriculaTurma();
            $alunos->setOrderby('ref_cod_curso, ref_ref_cod_serie, ref_cod_turma, sequencial_fechamento, nome');

            $alunos = $alunos->lista(
                $this->getRequest()->matricula_id,
                $this->getRequest()->turma_id,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->getRequest()->serie_id,
                $this->getRequest()->curso_id,
                $this->getRequest()->escola_id,
                $this->getRequest()->instituicao_id,
                $this->getRequest()->aluno_id,
                null,
                null,
                null,
                null,
                $this->getRequest()->ano,
                null,
                true,
                null,
                null,
                true,
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
                $this->getRequest()->dependencia
            );

            if (!is_array($alunos)) {
                $alunos = [];
            }

            $situacoesMatricula = [
                'aprovado' => App_Model_MatriculaSituacao::APROVADO,
                'reprovado' => App_Model_MatriculaSituacao::REPROVADO,
                'reprovado-faltas' => App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS,
                'em-andamento' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
                'aprovado-conselho' => App_Model_MatriculaSituacao::APROVADO_PELO_CONSELHO,
                'aprovado-dependencia' => App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA,
                'reclassificado' => App_Model_MatriculaSituacao::RECLASSIFICADO,
                'abandono' => App_Model_MatriculaSituacao::ABANDONO
            ];

            foreach ($alunos as $aluno) {
                $situacaoMatricula = $this->getSituacaoMatricula($aluno['ref_cod_matricula']);

                if (in_array($situacaoMatricula, $situacoesMatricula)) {
                    $matricula = [];
                    $matriculaId = $aluno['ref_cod_matricula'];
                    $matricula['matricula_id'] = $matriculaId;
                    $matricula['aluno_id'] = $aluno['ref_cod_aluno'];
                    $matricula['nome'] = $this->toUtf8($aluno['nome_aluno']);
                    $matricula['nome_curso'] = $this->toUtf8($aluno['nm_curso']);
                    $matricula['nome_serie'] = $this->toUtf8($this->getNomeSerie($aluno['ref_ref_cod_serie']));
                    $matricula['nome_turma'] = $this->toUtf8($aluno['nm_turma']);
                    $matricula['situacao_historico'] = $this->getSituacaoHistorico(
                        $aluno['ref_cod_aluno'],
                        $this->getRequest()->ano,
                        $matriculaId,
                        $reload = true
                    );
                    $matricula['link_to_historico'] = $this->getLinkToHistorico(
                        $aluno['ref_cod_aluno'],
                        $this->getRequest()->ano,
                        $matriculaId
                    );
                    $matriculas[] = $matricula;
                }
            }
        }

        return $matriculas;
    }

    protected function getObservacaoPadraoSerie()
    {
        if ($this->validatesPresenceOfSerieId(false, false)) {
            $sql = 'select coalesce(observacao_historico, \'\') as observacao_historico from pmieducar.serie
                     where cod_serie = $1';

            $observacao = Portabilis_Utils_Database::selectField($sql, $this->getRequest()->serie_id);
        } else {
            $observacao = '';
        }

        return $this->toUtf8($observacao);
    }

    protected function saveService()
    {
        try {
            $this->getService()->save();
        } catch (CoreExt_Service_Exception) {
        }
    }

    /**
     * @param bool $raiseExceptionOnErrors
     * @param bool $appendMsgOnErrors
     *
     * @return Avaliacao_Service_Boletim|null
     *
     * @throws Exception
     */
    protected function getService($raiseExceptionOnErrors = false, $appendMsgOnErrors = true)
    {
        if (isset($this->service) && !is_null($this->service)) {
            return $this->service;
        }

        $msg = 'Erro ao recuperar serviço boletim: serviço não definido.';
        if ($appendMsgOnErrors) {
            $this->appendMsg($msg);
        }

        if ($raiseExceptionOnErrors) {
            throw new Exception($msg);
        }

        return null;
    }

    protected function canSetService($validatesPresenceOfMatriculaId = true)
    {
        try {
            $this->requiresLogin(true);
            if ($validatesPresenceOfMatriculaId) {
                $this->validatesPresenceOfMatriculaId(true);
            }
        } catch (Exception) {
            return false;
        }

        return true;
    }

    protected function setService($matriculaId = null)
    {
        if ($this->canSetService($validatesPresenceOfMatriculaId = is_null($matriculaId))) {
            try {
                if (!$matriculaId) {
                    $matriculaId = $this->getRequest()->matricula_id;
                }

                $this->service = new Avaliacao_Service_Boletim(
                    [
                        'matricula' => $matriculaId,
                        'usuario' => \Illuminate\Support\Facades\Auth::id(),
                        'ignorarDispensasParciais' => true,
                    ]
                );

                return true;
            } catch (Exception $e) {
                $this->appendMsg(
                    'Exception ao instanciar serviço boletim: ' . $e->getMessage(),
                    'error',
                    $encodeToUtf8 = true
                );
            }
        }

        return false;
    }

    protected function notImplementedError()
    {
        $this->appendMsg("Operação '{$this->getRequest()->oper}' inválida para o att '{$this->getRequest()->att}'");
    }

    public function Gerar()
    {
        $this->msgs = [];
        $this->response = [];

        if ($this->canAcceptRequest()) {
            try {
                if (isset($this->getRequest()->matricula_id)) {
                    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
                }

                if ($this->getRequest()->oper == 'get') {
                    if ($this->getRequest()->att == 'matriculas') {
                        $matriculas = $this->getMatriculas();
                        $this->appendResponse('matriculas', $matriculas);
                        $this->appendResponse('observacao_padrao', $this->getObservacaoPadraoSerie());
                    } else {
                        $this->notImplementedError();
                    }
                } elseif ($this->getRequest()->oper == 'post') {
                    if ($this->getRequest()->att == 'processamento') {
                        $this->postProcessamento();
                    } else {
                        $this->notImplementedError();
                    }
                } elseif ($this->getRequest()->oper == 'delete') {
                    if ($this->getRequest()->att == 'historico') {
                        $this->deleteHistorico();
                    } else {
                        $this->notImplementedError();
                    }
                }
            } catch (Exception $e) {
                $this->appendMsg('Exception: ' . $e->getMessage(), $type = 'error', $encodeToUtf8 = true);
            }
        }
        echo $this->prepareResponse();
    }

    protected function appendResponse($name, $value)
    {
        $this->response[$name] = $value;
    }

    protected function prepareResponse()
    {
        $msgs = [];
        $this->appendResponse('att', isset($this->getRequest()->att) ? $this->getRequest()->att : '');

        foreach ($this->msgs as $m) {
            $msgs[] = ['msg' => $m['msg'], 'type' => $m['type']];
        }
        $this->appendResponse('msgs', $msgs);

        echo SafeJson::encode($this->response);
    }

    protected function appendMsg($msg, $type = 'error', $encodeToUtf8 = false)
    {
        if ($encodeToUtf8) {
            $msg = utf8_encode($msg);
        }

        //error_log("$type msg: '$msg'");
        $this->msgs[] = ['msg' => $msg, 'type' => $type];
    }

    public function generate(CoreExt_Controller_Page_Interface $instance)
    {
        header('Content-Type: application/json');
        $instance->Gerar();
    }

    // TODO remover metodo, ao migrar esta classe para novo padrao

    protected function toUtf8($str, $options = [])
    {
        return Portabilis_String_Utils::toUtf8($str, $options);
    }
}
