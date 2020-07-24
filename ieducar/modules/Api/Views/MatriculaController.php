<?php

use App\Models\LegacyRegistration;
use Illuminate\Support\Str;

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'App/Model/MatriculaSituacao.php';
require_once 'intranet/include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'modules/Avaliacao/Model/NotaAlunoDataMapper.php';
require_once 'modules/Avaliacao/Model/NotaComponenteMediaDataMapper.php';
require_once 'modules/Avaliacao/Views/PromocaoApiController.php';
require_once 'lib/CoreExt/Controller/Request.php';

class MatriculaController extends ApiCoreController
{

    protected function canGetMatriculas()
    {
        return $this->validatesId('escola') &&
           $this->validatesId('aluno');
    }

    protected function canDeleteAbandono()
    {
        return (
            $this->validatesPresenceOf('id') &&
            $this->validatesExistenceOf('matricula', $this->getRequest()->id)
        );
    }

    // search options
    protected function searchOptions()
    {
        $escolaId = $this->getRequest()->escola_id ? $this->getRequest()->escola_id : 0;
        $ano = $this->getRequest()->ano ? $this->getRequest()->ano : 0;
        $andamento = $this->getRequest()->andamento ? 1 : 0;
        $utilizaFiltroAbandonoTransferencia = $this->getRequest()->filtro_abandono_transferencia ? 1 : 0;

        return [
            'sqlParams' => [$escolaId, $ano, $andamento, $utilizaFiltroAbandonoTransferencia],
            'selectFields' => ['aluno_id']
        ];
    }

    protected function sqlsForNumericSearch()
    {
        // seleciona por (codigo matricula ou codigo aluno), opcionalmente por codigo escola e
        // opcionalmente por ano.
        return 'select aluno.cod_aluno as aluno_id,
            matricula.cod_matricula as id,
            pessoa.nome as name
       from cadastro.pessoa
      inner join pmieducar.aluno on(pessoa.idpes = aluno.ref_idpes)
      inner join pmieducar.matricula on(aluno.cod_aluno = matricula.ref_cod_aluno)
      inner join pmieducar.escola on(escola.cod_escola = matricula.ref_ref_cod_escola)
      inner join pmieducar.instituicao on (escola.ref_cod_instituicao = instituicao.cod_instituicao)
        and aluno.ativo = 1
        and matricula.ativo = 1
        and (case when $4 = 1 then
              matricula.aprovado = 3
             else
              (case when $5 = 1 then
                (case when instituicao.permissao_filtro_abandono_transferencia then
                  matricula.aprovado in (1, 2, 3, 7, 8, 9, 12, 13, 14)
                 else
                  matricula.aprovado in (1, 2, 3, 4, 6, 7, 8, 9, 12, 13, 14)
                 end)
               else
                matricula.aprovado in (1, 2, 3, 4, 6, 7, 8, 9, 12, 13, 14)
               end)
             end)
        and (case when aprovado = 4 then not exists (select * from pmieducar.matricula m where m.ativo = 1 and m.ano = matricula.ano and m.ref_cod_aluno = aluno.cod_aluno and m.ref_ref_cod_escola = matricula.ref_ref_cod_escola and m.aprovado <> 4 ) else true end)
        and (matricula.cod_matricula::varchar like $1||\'%\' or matricula.ref_cod_aluno::varchar like $1||\'%\')
        and (select case when $2 != 0 then matricula.ref_ref_cod_escola = $2 else true end)
        and (select case when $3 != 0 then matricula.ano = $3 else true end) limit 15';
    }

    protected function sqlsForStringSearch()
    {
        // seleciona por nome aluno, opcionalmente por codigo escola e opcionalmente por ano.
        return 'select aluno.cod_aluno as aluno_id,
            matricula.cod_matricula as id,
            pessoa.nome as name
       from cadastro.pessoa
      inner join pmieducar.aluno on(pessoa.idpes = aluno.ref_idpes)
      inner join pmieducar.matricula on(aluno.cod_aluno = matricula.ref_cod_aluno)
      inner join pmieducar.escola on(escola.cod_escola = matricula.ref_ref_cod_escola)
      inner join pmieducar.instituicao on (escola.ref_cod_instituicao = instituicao.cod_instituicao)
      where aluno.ativo = 1
        and matricula.ativo = 1
        and (case when $4 = 1 then
              matricula.aprovado = 3
             else
              (case when $5 = 1 then
                (case when instituicao.permissao_filtro_abandono_transferencia then
                  matricula.aprovado in (1, 2, 3, 7, 8, 9, 12, 13, 14)
                 else
                  matricula.aprovado in (1, 2, 3, 4, 6, 7, 8, 9, 12, 13, 14)
                 end)
               else
                matricula.aprovado in (1, 2, 3, 4, 6, 7, 8, 9, 12, 13, 14)
               end)
             end)
        and (case when aprovado = 4 then not exists (select * from pmieducar.matricula m where m.ativo = 1 and m.ano = matricula.ano and m.ref_cod_aluno = aluno.cod_aluno and m.ref_ref_cod_escola = matricula.ref_ref_cod_escola and m.aprovado <> 4 ) else true end)
        and lower((pessoa.nome)) like \'%\'||lower(($1))||\'%\'
        and (select case when $2 != 0 then matricula.ref_ref_cod_escola = $2 else true end)
        and (select case when $3 != 0 then matricula.ano = $3 else true end) limit 15';
    }

    /**
     * Método para possibilitar a busca apenas de alunos transferidos, será
     * substituído em futuras versões.
     *
     * @deprecated
     *
     * @return array
     */
    public function getTransferredRegistrations()
    {
        $year = $this->getRequest()->ano;
        $school = $this->getRequest()->escola_id;
        $query = $this->getRequest()->query;

        $query = str_replace('-', ' ', Str::slug($query));

        $registrations = LegacyRegistration::query()
            ->with('student.person')
            ->whereHas('student.person', function ($builder) use ($query) {
                $builder->where('nome', 'ilike', '%' . $query . '%');
            })
            ->where('aprovado', 4)
            ->where('ano', $year)
            ->where('ref_ref_cod_escola', $school)
            ->limit(15)
            ->get();

        $transfers = [];

        foreach ($registrations as $registration) {
            $codAluno = $registration->student->cod_aluno;
            $nome = $registration->student->person->nome;
            $transfers[$registration->cod_matricula] = "({$codAluno}) {$nome}";
        }

        if (empty($transfers)) {
            return ['result' => ['' => 'Sem resultados.']];
        }

        return ['result' => $transfers];
    }

    protected function formatResourceValue($resource)
    {
        $alunoId = $resource['aluno_id'];
        $nome = $this->toUtf8($resource['name'], ['transform' => true]);

        return $resource['id'] . " - ($alunoId) $nome";
    }

    // load
    protected function loadNomeEscola($escolaId)
    {
        $sql = 'select nome from cadastro.pessoa, pmieducar.escola where idpes = ref_idpes and cod_escola = $1';
        $nome = $this->fetchPreparedQuery($sql, $escolaId, false, 'first-field');

        return $this->safeString($nome);
    }

    protected function loadNameFor($resourceName, $id)
    {
        $sql = "select nm_{$resourceName} from pmieducar.{$resourceName} where cod_{$resourceName} = $1";
        $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

        return $this->safeString($nome);
    }

    protected function loadDadosForMatricula($matriculaId)
    {
        $sql = 'select cod_matricula as id, ref_cod_aluno as aluno_id, matricula.ano,
            escola.ref_cod_instituicao as instituicao_id, matricula.ref_ref_cod_escola
            as escola_id, matricula.ref_cod_curso as curso_id, matricula.ref_ref_cod_serie
            as serie_id, matricula_turma.ref_cod_turma as turma_id from
            pmieducar.matricula_turma, pmieducar.matricula, pmieducar.escola where escola.cod_escola =
            matricula.ref_ref_cod_escola and ref_cod_matricula = cod_matricula and ref_cod_matricula =
            $1 and matricula.ativo = matricula_turma.ativo and matricula_turma.ativo = 1 order by
            matricula_turma.sequencial limit 1';

        $dadosMatricula = $this->fetchPreparedQuery($sql, $matriculaId, false, 'first-row');

        $attrs = ['id', 'aluno_id', 'ano', 'instituicao_id', 'escola_id',
            'curso_id', 'serie_id', 'turma_id'];

        return Portabilis_Array_Utils::filter($dadosMatricula, $attrs);
    }

    protected function tryLoadMatriculaTurma($matriculaId)
    {
        $sql = 'select ref_cod_turma as turma_id, turma.tipo_boletim from pmieducar.matricula_turma,
            pmieducar.turma where ref_cod_turma = cod_turma and ref_cod_matricula = $1 and
            matricula_turma.ativo = 1 limit 1';

        $matriculaTurma = $this->fetchPreparedQuery($sql, $matriculaId, false, 'first-row');

        if (is_array($matriculaTurma) and count($matriculaTurma) > 0) {
            $attrs = ['turma_id', 'tipo_boletim'];
            $matriculaTurma = Portabilis_Array_Utils::filter($matriculaTurma, $attrs);
            $matriculaTurma['nome_turma'] = $this->loadNameFor('turma', $matriculaTurma['turma_id']);
        }

        return $matriculaTurma;
    }

    protected function loadMatriculasAluno($alunoId, $escolaId)
    {
        // #TODO mostrar o nome da situação da matricula
        // seleciona somente matriculas em andamento, aprovado, reprovado, em exame, aprovado apos exame e retido faltas
        $sql = 'select cod_matricula as id, ano, ref_cod_instituicao as instituicao_id, ref_ref_cod_escola as
            escola_id, ref_cod_curso as curso_id, ref_ref_cod_serie as serie_id from pmieducar.matricula,
            pmieducar.escola where cod_escola = ref_ref_cod_escola and ref_cod_aluno = $1 and ref_ref_cod_escola =
            $2 and matricula.ativo = 1 and matricula.aprovado in (1, 2, 3, 7, 8, 9) order by ano desc, id';

        $params = [$alunoId, $escolaId];
        $matriculas = $this->fetchPreparedQuery($sql, $params, false);

        if (is_array($matriculas) && count($matriculas) > 0) {
            $attrs = ['id', 'ano', 'instituicao_id', 'escola_id', 'curso_id', 'serie_id'];
            $matriculas = Portabilis_Array_Utils::filterSet($matriculas, $attrs);

            foreach ($matriculas as $key => $matricula) {
                $matriculas[$key]['nome_curso'] = $this->loadNameFor('curso', $matricula['curso_id']);
                $matriculas[$key]['nome_escola'] = $this->loadNomeEscola($this->getRequest()->escola_id);
                $matriculas[$key]['nome_serie'] = $this->loadNameFor('serie', $matricula['serie_id']);
                $matriculas[$key]['situacao'] = '#TODO';

                $turma = $this->tryLoadMatriculaTurma($matricula['id']);

                if (is_array($turma) and count($turma) > 0) {
                    $matriculas[$key]['turma_id'] = $turma['turma_id'];
                    $matriculas[$key]['nome_turma'] = $turma['nome_turma'];
                    $matriculas[$key]['report_boletim_template'] = $turma['report_boletim_template'];
                }
            }
        }

        return $matriculas;
    }

    // api
    protected function get()
    {
        if ($this->canGet()) {
            return $this->loadDadosForMatricula($this->getRequest()->id);
        }
    }

    protected function getMatriculas()
    {
        if ($this->canGetMatriculas()) {
            $matriculas = $this->loadMatriculasAluno($this->getRequest()->aluno_id, $this->getRequest()->escola_id);

            return ['matriculas' => $matriculas];
        }
    }

    protected function canGetMovimentacaoEnturmacao()
    {
        return $this->validatesPresenceOf('ano');
    }

    protected function canGetMatriculasEscola()
    {
        return $this->validatesPresenceOf('ano') && $this->validatesPresenceOf('escola');
    }

    protected function getMatriculasEscola()
    {
        if ($this->canGetMatriculasEscola() == false) {
            return;
        }

        $ano = $this->getRequest()->ano;
        $escola = $this->getRequest()->escola;
        $updatedAt = $this->getRequest()->modified;

        if (is_array($escola)) {
            $escola = implode(',', $escola);
        }

        $params = [$ano];

        $where = '';

        if ($updatedAt) {
            $where = ' AND m.updated_at >= $2';
            $params[] = $updatedAt;
        }

        $sql = "
            SELECT
                m.ref_cod_aluno AS aluno_id,
                m.cod_matricula AS matricula_id,
                m.ref_ref_cod_escola AS escola_id,
                m.aprovado AS situacao,
                m.ativo AS ativo,
                m.updated_at::timestamp(0) AS updated_at,
                (
                    case when m.ativo = 1 then null
                    else m.updated_at::timestamp(0)
                    end
                ) as deleted_at
            FROM pmieducar.matricula m
            INNER JOIN pmieducar.aluno a
                ON a.cod_aluno = m.ref_cod_aluno
            WHERE m.ano = $1
                AND m.ref_ref_cod_escola in ({$escola})
                {$where}
            ORDER BY m.updated_at
        ";

        $matriculas = $this->fetchPreparedQuery($sql, $params, false);

        if (is_array($matriculas) && count($matriculas) > 0) {
            $attrs = ['aluno_id', 'matricula_id', 'escola_id', 'situacao', 'ativo', 'updated_at', 'deleted_at'];

            $matriculas = Portabilis_Array_Utils::filterSet($matriculas, $attrs);
        }

        return ['matriculas' => $matriculas];
    }

    protected function getMovimentacaoEnturmacao()
    {
        $ano = $this->getRequest()->ano;
        $escola = $this->getRequest()->escola;
        $updatedAt = $this->getRequest()->modified;

        if (is_array($escola)) {
            $escola = implode(',', $escola);
        }

        if ($this->canGetMovimentacaoEnturmacao()) {
            if (!$escola) {
                $escola = 0;
            }

            $params = [$ano];

            $whereMatriculaTurma = '';
            $whereMatriculaExcluidos = '';

            if ($updatedAt) {
                $whereMatriculaTurma = ' AND matricula_turma.updated_at >= $2';
                $whereMatriculaExcluidos = ' AND matricula_turma_excluidos.deleted_at >= $2';
                $params[] = $updatedAt;
            }

            $sql = '(SELECT
                       matricula_turma.id,
                       matricula.cod_matricula as matricula_id,
                       matricula_turma.ref_cod_turma AS turma_id,
                       matricula_turma.sequencial AS sequencial,
                       matricula_turma.sequencial_fechamento AS sequencial_fechamento,
                       COALESCE(matricula_turma.data_enturmacao::date::varchar, \'\') AS data_entrada,
                       COALESCE(matricula_turma.data_exclusao::date::varchar, matricula.data_cancel::date::varchar, \'\') AS data_saida,
                       matricula_turma.updated_at::timestamp(0) AS updated_at,
                       CASE
                           WHEN COALESCE(instituicao.data_base_transferencia, instituicao.data_base_remanejamento) IS NULL THEN FALSE
                           WHEN matricula.aprovado = 4 AND
                                matricula_turma.transferido AND
                                matricula_turma.data_exclusao > pmieducar.get_date_in_year($1, instituicao.data_base_transferencia) THEN TRUE
                           WHEN matricula.aprovado = 3 AND
                                matricula_turma.remanejado AND
                                matricula_turma.data_exclusao > pmieducar.get_date_in_year($1, instituicao.data_base_remanejamento) THEN TRUE
                           ELSE FALSE
                       END AS apresentar_fora_da_data,
                       matricula_turma.turno_id,
                       null AS deleted_at
                  FROM pmieducar.matricula
            INNER JOIN pmieducar.escola
                    ON escola.cod_escola = matricula.ref_ref_cod_escola
            INNER JOIN pmieducar.instituicao
                    ON instituicao.cod_instituicao = escola.ref_cod_instituicao
            INNER JOIN pmieducar.matricula_turma
                    ON matricula_turma.ref_cod_matricula = matricula.cod_matricula
                 WHERE matricula.ref_ref_cod_escola in (' . $escola . ')
                   AND matricula.ano = $1::integer
                 ' . $whereMatriculaTurma . ')
                 UNION ALL
                 (SELECT
                       matricula_turma_excluidos.id,
                       matricula.cod_matricula as matricula_id,
                       matricula_turma_excluidos.ref_cod_turma AS turma_id,
                       matricula_turma_excluidos.sequencial AS sequencial,
                       matricula_turma_excluidos.sequencial_fechamento AS sequencial_fechamento,
                       COALESCE(matricula_turma_excluidos.data_enturmacao::date::varchar, \'\') AS data_entrada,
                       COALESCE(matricula_turma_excluidos.data_exclusao::date::varchar, matricula.data_cancel::date::varchar, \'\') AS data_saida,
                       matricula_turma_excluidos.updated_at::timestamp(0) AS updated_at,
                       CASE
                           WHEN COALESCE(instituicao.data_base_transferencia, instituicao.data_base_remanejamento) IS NULL THEN FALSE
                           WHEN matricula.aprovado = 4 AND
                                matricula_turma_excluidos.transferido AND
                                matricula_turma_excluidos.data_exclusao > pmieducar.get_date_in_year($1, instituicao.data_base_transferencia) THEN TRUE
                           WHEN matricula.aprovado = 3 AND
                                matricula_turma_excluidos.remanejado AND
                                matricula_turma_excluidos.data_exclusao > pmieducar.get_date_in_year($1, instituicao.data_base_remanejamento) THEN TRUE
                           ELSE FALSE
                       END AS apresentar_fora_da_data,
                       matricula_turma_excluidos.turno_id,
                       matricula_turma_excluidos.deleted_at
                  FROM pmieducar.matricula
            INNER JOIN pmieducar.escola
                    ON escola.cod_escola = matricula.ref_ref_cod_escola
            INNER JOIN pmieducar.instituicao
                    ON instituicao.cod_instituicao = escola.ref_cod_instituicao
            INNER JOIN pmieducar.matricula_turma_excluidos
                    ON matricula_turma_excluidos.ref_cod_matricula = matricula.cod_matricula
                 WHERE matricula.ref_ref_cod_escola in (' . $escola . ')
                   AND matricula.ano = $1::integer ' . $whereMatriculaExcluidos . ')

                   ORDER BY updated_at';

            $enturmacoes = $this->fetchPreparedQuery($sql, $params, false);

            if (is_array($enturmacoes) && count($enturmacoes) > 0) {
                $attrs = [
                    'id',
                    'turma_id',
                    'matricula_id',
                    'sequencial',
                    'sequencial_fechamento',
                    'data_entrada',
                    'data_saida',
                    'apresentar_fora_da_data',
                    'turno_id',
                    'updated_at',
                    'deleted_at',
                ];
                $enturmacoes = Portabilis_Array_Utils::filterSet($enturmacoes, $attrs);
            }

            return ['enturmacoes' => $enturmacoes];
        }
    }

    protected function getFrequencia()
    {
        $cod_matricula = $this->getRequest()->id;
        $objBanco = new clsBanco();
        $frequencia = $objBanco->unicoCampo(" SELECT modules.frequencia_da_matricula({$cod_matricula}); ");

        return ['frequencia' => $frequencia];
    }

    protected function desfazSaidaEscola()
    {
        $matriculaId = $this->getRequest()->id;
        $params = $matriculaId;

        $sql = 'UPDATE pmieducar.matricula
               SET saida_escola = FALSE,
                   observacao = NULL
             WHERE cod_matricula = $1';

        $this->fetchPreparedQuery($sql, $params);
        $this->messenger->append('Saída da escola cancelada.', 'success');
    }

    protected function deleteAbandono()
    {
        if ($this->canDeleteAbandono()) {
            $matriculaId = $this->getRequest()->id;
            $tipoSemAbandono = null;
            $situacaoAndamento = App_Model_MatriculaSituacao::EM_ANDAMENTO;

            $sql = 'UPDATE pmieducar.matricula
              SET aprovado = $1,
                  ref_cod_abandono_tipo = $2,
                  data_exclusao = NULL
              WHERE cod_matricula = $3';
            $this->fetchPreparedQuery($sql, [$situacaoAndamento, $tipoSemAbandono, $matriculaId]);

            $params = [$matriculaId, 0];
            $sql = 'SELECT max(sequencial) AS codigo
                FROM pmieducar.matricula_turma
               WHERE ref_cod_matricula = $1
                 AND ativo = $2';
            $sequencial = $this->fetchPreparedQuery($sql, $params, false, 'first-field');

            $sql = 'UPDATE pmieducar.matricula_turma
                 SET ativo = 1,
                     transferido = FALSE,
                     remanejado = FALSE,
                     abandono = FALSE,
                     reclassificado = FALSE,
                     data_exclusao = NULL
               WHERE sequencial = $1
                 AND ref_cod_matricula = $2';

            $params = [$sequencial, $matriculaId];
            $this->fetchPreparedQuery($sql, $params);

            $instituicaoId = (new clsBanco)->unicoCampo("select cod_instituicao from pmieducar.instituicao where ativo = 1 order by cod_instituicao asc limit 1;");

            $fakeRequest = new CoreExt_Controller_Request(['data' => [
                'oper' => 'post',
                'resource' => 'promocao',
                'instituicao_id' => $instituicaoId,
                'matricula_id' => $matriculaId
            ]]);

            $promocaoApi = new PromocaoApiController();

            $promocaoApi->setRequest($fakeRequest);
            $promocaoApi->Gerar();

            $this->messenger->append('Abandono desfeito.', 'success');
        }
    }

    protected function deleteReclassificacao()
    {
        $matriculaId = $this->getRequest()->id;
        $matricula = new clsPmieducarMatricula($matriculaId);
        $matricula = $matricula->detalhe();
        $alunoId = $matricula['ref_cod_aluno'];
        $situacaoAndamento = App_Model_MatriculaSituacao::EM_ANDAMENTO;
        $sql = 'update pmieducar.matricula_turma set ativo = 1, reclassificado = NULL, data_exclusao = NULL where ref_cod_matricula = $1';

        $this->fetchPreparedQuery($sql, [$matriculaId]);

        $sql = 'update pmieducar.matricula set matricula_reclassificacao = 0, data_exclusao = NULL, aprovado = $1 where cod_matricula = $2';

        $this->fetchPreparedQuery($sql, [$situacaoAndamento, $matriculaId]);

        return ['aluno_id' => $alunoId];
    }

    protected function canPostReservaExterna()
    {
        return (
            $this->validatesPresenceOf('instituicao_id') &&
            $this->validatesPresenceOf('ano') &&
            $this->validatesPresenceOf('curso_id') &&
            $this->validatesPresenceOf('serie_id') &&
            $this->validatesPresenceOf('turma_turno_id') &&
            $this->validatesPresenceOf('qtd_alunos') &&
            $this->validatesPresenceOf('escola_id')
        );
    }

    protected function postReservaExterna()
    {
        if ($this->canPostReservaExterna()) {
            $instituicaoId = $this->getRequest()->instituicao_id;
            $escolaId = $this->getRequest()->escola_id;
            $cursoId = $this->getRequest()->curso_id;
            $serieId = $this->getRequest()->serie_id;
            $turmaTurnoId = $this->getRequest()->turma_turno_id;
            $ano = $this->getRequest()->ano;
            $qtd_alunos = $this->getRequest()->qtd_alunos;
            $params = [$instituicaoId, $escolaId, $cursoId, $serieId, $turmaTurnoId, $ano];

            $sql = 'DELETE
                FROM pmieducar.quantidade_reserva_externa
                WHERE ref_cod_instituicao = $1
                AND ref_cod_escola = $2
                AND ref_cod_curso = $3
                AND ref_cod_serie = $4
                AND ref_turma_turno_id = $5
                AND ano = $6';

            $this->fetchPreparedQuery($sql, $params);

            $params[] = $qtd_alunos;

            $sql = ' INSERT INTO pmieducar.quantidade_reserva_externa VALUES ($1,$2,$3,$4,$5,$6,$7)';

            $this->fetchPreparedQuery($sql, $params);

            $this->messenger->append('Quantidade de alunos atualizada com sucesso!.', 'success');
        }
    }

    protected function validaDataEntrada()
    {
        if (!Portabilis_Date_Utils::validaData($this->getRequest()->data_entrada)) {
            $this->messenger->append('Valor inválido para data de entrada ' . $this->getRequest()->data_entrada, 'error');

            return false;
        } else {
            return true;
        }
    }

    protected function validaDataSaida()
    {
        if (!Portabilis_Date_Utils::validaData($this->getRequest()->data_saida)) {
            $this->messenger->append('Valor inválido para data de saída', 'error');

            return false;
        } else {
            return true;
        }
    }

    protected function postDataEntrada()
    {
        if ($this->validaDataEntrada()) {
            $matricula_id = $this->getRequest()->matricula_id;
            $data_entrada = Portabilis_Date_Utils::brToPgSQL($this->getRequest()->data_entrada);
            $matricula = new clsPmieducarMatricula($matricula_id);
            $matricula->data_matricula = $data_entrada;

            if ($matricula->edita()) {
                $this->messenger->append('Data de entrada atualizada com sucesso.', 'success');
            }
        }
    }

    protected function postDataSaida()
    {
        if ($this->validaDataSaida()) {
            $matricula_id = $this->getRequest()->matricula_id;
            $data_saida = Portabilis_Date_Utils::brToPgSQL($this->getRequest()->data_saida);
            $matricula = new clsPmieducarMatricula($matricula_id);
            $matricula->data_cancel = $data_saida;

            if ($matricula->edita()) {
                $enrollment = LegacyRegistration::find($matricula_id);
                $lastenrollment = $enrollment->lastEnrollment;
                $lastenrollment->data_exclusao = $data_saida;
                $lastenrollment->save();
                return $this->messenger->append('Data de saida atualizada com sucesso.', 'success');
            }
        }
    }
    protected function postSituacao()
    {
        if ($this->validatesPresenceOf('matricula_id') && $this->validatesPresenceOf('nova_situacao')) {
            $matriculaId = $this->getRequest()->matricula_id;
            $matricula = new clsPmieducarMatricula($matriculaId);
            $objMatricula = $matricula->detalhe();
            $codAluno = $objMatricula['ref_cod_aluno'];

            $situacaoAntiga = $matricula->aprovado;
            $situacaoNova = $this->getRequest()->nova_situacao;

            $enturmacoes = new clsPmieducarMatriculaTurma();
            $enturmacoes = $enturmacoes->lista($matriculaId, null, null, null, null, null, null, null, 1);

            if (
                $situacaoNova == App_Model_MatriculaSituacao::TRANSFERIDO ||
                $situacaoNova == App_Model_MatriculaSituacao::ABANDONO ||
                $situacaoNova == App_Model_MatriculaSituacao::FALECIDO
            ) {
                if ($enturmacoes) {
                    foreach ($enturmacoes as $enturmacao) {
                        $enturmacao = new clsPmieducarMatriculaTurma($matriculaId, $enturmacao['ref_cod_turma'], 1, null, null, date('Y-m-d H:i:s'), 0, null, $enturmacao['sequencial']);

                        if (!$enturmacao->edita()) {
                            return false;
                        }

                        if ($situacaoNova == App_Model_MatriculaSituacao::TRANSFERIDO) {
                            $enturmacao->marcaAlunoTransferido();
                        } elseif ($situacaoNova == App_Model_MatriculaSituacao::ABANDONO) {
                            $enturmacao->marcaAlunoAbandono();
                        }elseif ($situacaoNova == App_Model_MatriculaSituacao::FALECIDO) {
                            $enturmacao->marcaAlunoFalecido();
                        }
                    }
                }

                $notaAluno = (new Avaliacao_Model_NotaAlunoDataMapper())
                    ->findAll(['id'], ['matricula_id' => $matricula->cod_matricula])[0];

                if (!is_null($notaAluno)) {
                    (new Avaliacao_Model_NotaComponenteMediaDataMapper())
                        ->updateSituation($notaAluno->get('id'), $situacaoNova);
                }
            } elseif ($situacaoNova == App_Model_MatriculaSituacao::APROVADO || $situacaoNova == App_Model_MatriculaSituacao::EM_ANDAMENTO || $situacaoNova == App_Model_MatriculaSituacao::REPROVADO) {
                if ($enturmacoes) {
                    $params = [$matriculaId];
                    $sql = 'SELECT sequencial as codigo FROM pmieducar.matricula_turma where ref_cod_matricula = $1 order by ativo desc, sequencial desc limit 1';
                    $sequencial = $this->fetchPreparedQuery($sql, $params, false, 'first-field');

                    $sql = 'UPDATE pmieducar.matricula_turma set ativo = 1, transferido = false, remanejado = false, abandono = false, reclassificado = false where sequencial = $1 and ref_cod_matricula = $2';

                    $params = [$sequencial, $matriculaId];
                    $this->fetchPreparedQuery($sql, $params);
                }
            }

            $matricula->aprovado = $this->getRequest()->nova_situacao;

            if ($matricula->edita()) {
                $this->alteraFalecimentoPessoa($codAluno);

                return $this->messenger->append('Situação da matrícula alterada com sucesso.', 'success');
            }
        }
    }

    protected function alteraFalecimentoPessoa($codAluno)
    {
        $matriculas = new clsPmieducarMatricula();

        $matriculas = $matriculas->lista(
            null,
            null,
            null,
            null,
            null,
            null,
            $codAluno,
            null,
            null,
            null,
            null,
            null,
            1
        );

        $aluno = new clsPmieducarAluno($codAluno);
        $aluno = $aluno->detalhe();

        $pessoaFisica = new clsFisica($aluno['ref_idpes']);

        foreach ($matriculas as $matricula) {
            if ($matricula['aprovado'] == App_Model_MatriculaSituacao::FALECIDO) {
                $pessoaFisica->falecido = true;
                $pessoaFisica->edita();

                return;
            }
        }

        $pessoaFisica->falecido = false;
        $pessoaFisica->edita();
    }

    protected function canGetMatriculasDependencia()
    {
        return $this->validatesPresenceOf('ano') && $this->validatesPresenceOf('escola');
    }

    protected function getMatriculasDependencia()
    {
        if (!$this->canGetMatriculasDependencia()) {
            return false;
        }

        $ano = $this->getRequest()->ano;
        $escola = $this->getRequest()->escola;
        $modified = $this->getRequest()->modified;

        $params = [$ano];

        if (is_array($escola)) {
            $escola = implode(', ', $escola);
        }

        $where = '';

        if ($modified) {
            $params[] = $modified;
            $where = ' AND dd.updated_at >= $2';
        }

        $sql = "
            (
                SELECT
                    m.cod_matricula AS matricula_id,
                    dd.ref_cod_disciplina AS disciplina_id,
                    dd.updated_at,
                    null as deleted_at
                FROM pmieducar.matricula m
                INNER JOIN pmieducar.disciplina_dependencia dd
                ON m.cod_matricula = dd.ref_cod_matricula
                WHERE m.dependencia = 't'
                AND m.ano = $1
                AND m.ref_ref_cod_escola IN ({$escola})
                {$where}
            )
            UNION ALL
            (
                SELECT
                    m.cod_matricula AS matricula_id,
                    dd.ref_cod_disciplina AS disciplina_id,
                    dd.updated_at,
                    dd.deleted_at
                FROM pmieducar.matricula m
                INNER JOIN pmieducar.disciplina_dependencia_excluidos dd
                ON m.cod_matricula = dd.ref_cod_matricula
                WHERE m.dependencia = 't'
                AND m.ano = $1
                AND m.ref_ref_cod_escola IN ({$escola})
                {$where}
            )
            order by updated_at
        ";

        $matriculas = $this->fetchPreparedQuery($sql, $params);
        $attrs = ['matricula_id', 'disciplina_id', 'updated_at', 'deleted_at'];
        $matriculas = Portabilis_Array_Utils::filterSet($matriculas, $attrs);

        return ['matriculas' => $matriculas];
    }

    protected function getDispensaDisciplina()
    {
        if (!$this->validatesPresenceOf('escola')) {
            return false;
        }

        $escola = $this->getRequest()->escola;
        $modified = $this->getRequest()->modified;

        $where = '';
        $params = [];

        if ($modified) {
            $where = " AND dd.updated_at >= $1";
            $params[] = $modified;
        }

        $sql = "
            (
                SELECT
                    dd.ref_cod_matricula AS matricula_id,
                    dd.ref_cod_disciplina AS disciplina_id,
                    string_agg(CAST(de.etapa AS VARCHAR), ',') AS etapas,
                    dd.updated_at,
                    null as deleted_at
                FROM pmieducar.dispensa_disciplina AS dd
                INNER JOIN pmieducar.matricula m
                ON m.cod_matricula = dd.ref_cod_matricula
                LEFT JOIN pmieducar.dispensa_etapa de
                ON de.ref_cod_dispensa = dd.cod_dispensa
                WHERE dd.ativo = 1
                AND m.ref_ref_cod_escola IN ({$escola})
                {$where}
                GROUP BY dd.ref_cod_matricula, dd.ref_cod_disciplina, dd.updated_at
            )
            UNION ALL
            (
                SELECT
                    dd.ref_cod_matricula AS matricula_id,
                    dd.ref_cod_disciplina AS disciplina_id,
                    string_agg(CAST(de.etapa AS VARCHAR), ',') AS etapas,
                    dd.updated_at,
                    dd.deleted_at
                FROM pmieducar.dispensa_disciplina_excluidos AS dd
                INNER JOIN pmieducar.matricula m
                ON m.cod_matricula = dd.ref_cod_matricula
                LEFT JOIN pmieducar.dispensa_etapa de
                ON de.ref_cod_dispensa = dd.cod_dispensa
                WHERE dd.ativo = 1
                AND m.ref_ref_cod_escola IN ({$escola})
                {$where}
                GROUP BY dd.ref_cod_matricula, dd.ref_cod_disciplina, dd.updated_at, dd.deleted_at
            )
            order by updated_at
        ";

        $dispensas = $this->fetchPreparedQuery($sql, $params);
        $attrs = ['matricula_id', 'disciplina_id', 'etapas', 'updated_at', 'deleted_at'];
        $dispensas = Portabilis_Array_Utils::filterSet($dispensas, $attrs);

        return ['dispensas' => $dispensas];
    }

    protected function getEnturmacoesExcluidas()
    {
        $ano = $this->getRequest()->ano;
        $escola = $this->getRequest()->escola;
        $deletedAt = $this->getRequest()->deleted_at;

        if ($this->canGetEnturmacoesExcluidas()) {
            if (!$escola) {
                $escola = 0;
            }

            $sql = 'SELECT id, deleted_at
                    FROM pmieducar.matricula_turma_excluidos
                    JOIN pmieducar.matricula ON matricula.cod_matricula = matricula_turma_excluidos.ref_cod_matricula
                    WHERE matricula.ano = $1
                    AND CASE WHEN $2 = 0 THEN TRUE ELSE ref_ref_cod_escola = $2 END';

            $params = [$ano, $escola];

            if ($deletedAt) {
                $sql .= ' AND matricula_turma_excluidos.deleted_at >= $3';
                $params[] = $deletedAt;
            }

            $enturmacoes = $this->fetchPreparedQuery($sql, $params, false);

            $attrs = [
                'id',
                'deleted_at',
            ];
            $enturmacoes = Portabilis_Array_Utils::filterSet($enturmacoes, $attrs);

            return ['enturmacoes' => $enturmacoes];
        }
    }

    protected function canGetEnturmacoesExcluidas()
    {
        return $this->validatesPresenceOf('ano');
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'matricula')) {
            $this->appendResponse($this->get());
        } elseif ($this->isRequestFor('get', 'matriculas')) {
            $this->appendResponse($this->getMatriculas());
        } elseif ($this->isRequestFor('get', 'matriculas-escola')) {
            $this->appendResponse($this->getMatriculasEscola());
        } elseif ($this->isRequestFor('get', 'frequencia')) {
            $this->appendResponse($this->getFrequencia());
        } elseif ($this->isRequestFor('get', 'matricula-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'matricula-transferida')) {
            $this->appendResponse($this->getTransferredRegistrations());
        } elseif ($this->isRequestFor('get', 'movimentacao-enturmacao')) {
            $this->appendResponse($this->getMovimentacaoEnturmacao());
        } elseif ($this->isRequestFor('delete', 'abandono')) {
            $this->appendResponse($this->deleteAbandono());
        } elseif ($this->isRequestFor('delete', 'reclassificacao')) {
            $this->appendResponse($this->deleteReclassificacao());
        } elseif ($this->isRequestFor('delete', 'saidaEscola')) {
            $this->appendResponse($this->desfazSaidaEscola());
        } elseif ($this->isRequestFor('post', 'reserva-externa')) {
            $this->appendResponse($this->postReservaExterna());
        } elseif ($this->isRequestFor('post', 'data-entrada')) {
            $this->appendResponse($this->postDataEntrada());
        } elseif ($this->isRequestFor('post', 'data-saida')) {
            $this->appendResponse($this->postDataSaida());
        } elseif ($this->isRequestFor('post', 'situacao')) {
            $this->appendResponse($this->postSituacao());
        } elseif ($this->isRequestFor('get', 'matriculas-dependencia')) {
            $this->appendResponse($this->getMatriculasDependencia());
        } elseif ($this->isRequestFor('get', 'dispensa-disciplina')) {
            $this->appendResponse($this->getDispensaDisciplina());
        } elseif ($this->isRequestFor('get', 'enturmacoes-excluidas')) {
            $this->appendResponse($this->getEnturmacoesExcluidas());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
