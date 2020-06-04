<?php
use App\Models\LegacyDeficiency;
use App\Models\LogUnification;
use iEducar\Modules\Educacenso\Validator\DeficiencyValidator;
require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'intranet/include/clsBanco.inc.php';

class ServidorController extends ApiCoreController
{
    protected function searchOptions()
    {
        $escolaId = $this->getRequest()->escola_id ? $this->getRequest()->escola_id : 0;

        return ['sqlParams' => [$escolaId]];
    }

    protected function formatResourceValue($resource)
    {
        $nome = $this->toUtf8($resource['nome'], ['transform' => true]);

        return $nome;
    }

    protected function canGetServidoresDisciplinasTurmas()
    {
        return (
            $this->validatesPresenceOf('ano') &&
            $this->validatesPresenceOf('instituicao_id') &&
            $this->validatesPresenceOf('escola')
        );
    }

    protected function canGetServidores()
    {
        return $this->validatesPresenceOf('instituicao_id');
    }

    protected function sqlsForNumericSearch()
    {
        $sqls[] = 'SELECT p.idpes as id, p.nome
                FROM cadastro.pessoa p
                 LEFT JOIN cadastro.fisica f ON (p.idpes = f.idpes)
                 LEFT JOIN portal.funcionario fun ON (fun.ref_cod_pessoa_fj = f.idpes)
                INNER JOIN pmieducar.servidor s ON (s.cod_servidor = p.idpes)
                LEFT JOIN pmieducar.servidor_alocacao sa ON (s.cod_servidor = sa.ref_cod_servidor)

                WHERE p.idpes::varchar LIKE \'%\'||$1||\'%\'
                AND (CASE WHEN $2 = 0 THEN
                      1 = 1
                    ELSE
                      sa.ref_cod_escola = $2
                    END)
                LIMIT 15';

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $sqls[] = 'SELECT p.idpes as id, p.nome
                FROM cadastro.pessoa p
                 LEFT JOIN cadastro.fisica f ON (p.idpes = f.idpes)
                 LEFT JOIN portal.funcionario fun ON (fun.ref_cod_pessoa_fj = f.idpes)
                INNER JOIN pmieducar.servidor s ON (s.cod_servidor = p.idpes)
                LEFT JOIN pmieducar.servidor_alocacao sa ON (s.cod_servidor = sa.ref_cod_servidor)

                WHERE p.nome ILIKE \'%\'||$1||\'%\'
                AND (CASE WHEN $2 = 0 THEN
                      1 = 1
                    ELSE
                      sa.ref_cod_escola = $2
                    END)
                LIMIT 15';

        return $sqls;
    }

    protected function getServidores()
    {
        if (false == $this->canGetServidores()) {
            return;
        }

        $instituicaoId = $this->getRequest()->instituicao_id;
        $modified = $this->getRequest()->modified;

        $params = [$instituicaoId];

        $where = '';

        if ($modified) {
            $params[] = $modified;
            $where = ' AND greatest(p.data_rev::timestamp(0), s.updated_at) >= $2';
        }

        $sql = "
            SELECT
                s.cod_servidor as servidor_id,
                p.nome as nome,
                s.ativo as ativo,
                greatest(p.data_rev::timestamp(0), s.updated_at) as updated_at
            FROM pmieducar.servidor s
            INNER JOIN cadastro.pessoa p ON s.cod_servidor = p.idpes
            WHERE s.ref_cod_instituicao = $1
            {$where}
            order by updated_at
        ";

        $servidores = $this->fetchPreparedQuery($sql, $params);

        $attrs = ['servidor_id', 'nome', 'ativo', 'updated_at'];

        $servidores = Portabilis_Array_Utils::filterSet($servidores, $attrs);

        return ['servidores' => $servidores];
    }

    protected function getServidoresDisciplinasTurmas()
    {
        if ($this->canGetServidoresDisciplinasTurmas()) {
            $instituicaoId = $this->getRequest()->instituicao_id;
            $ano = $this->getRequest()->ano;
            $escola = $this->getRequest()->escola;
            $modified = $this->getRequest()->modified;

            $params = [$instituicaoId, $ano];

            if (is_array($escola)) {
                $escola = implode(', ', $escola);
            }

            $where = '';

            if ($modified) {
                $params[] = $modified;
                $where = 'AND greatest(pt.updated_at, ccae.updated_at) >= $3';
                $whereDeleted = 'AND pt.updated_at >= $3';
            }

            $sql = "
                (
                    select
                        pt.id,
                        pt.servidor_id,
                        pt.turma_id,
                        pt.turno_id,
                        pt.permite_lancar_faltas_componente,
                        string_agg(concat(ptd.componente_curricular_id, ' ', ccae.tipo_nota)::varchar, ',') as disciplinas,
                        greatest(pt.updated_at, date(ccae.updated_at)) as updated_at,
                        null as deleted_at
                    from modules.professor_turma pt
                    left join modules.professor_turma_disciplina ptd
                    on ptd.professor_turma_id = pt.id
                    inner join pmieducar.turma t
                    on t.cod_turma = pt.turma_id
                    inner join modules.componente_curricular_ano_escolar ccae
                    on ccae.ano_escolar_id = t.ref_ref_cod_serie
                    and ccae.componente_curricular_id = ptd.componente_curricular_id
                    where true
                    and pt.instituicao_id = $1
                    and pt.ano = $2
                    and t.ref_ref_cod_escola in ({$escola})
                    {$where}
                    group by id, greatest(pt.updated_at, date(ccae.updated_at))
                )
                union all
                (
                    select
                        pt.id,
                        pt.servidor_id,
                        pt.turma_id,
                        pt.turno_id,
                        null as permite_lancar_faltas_componente,
                        null as disciplinas,
                        pt.updated_at,
                        pt.deleted_at
                    from modules.professor_turma_excluidos pt
                    inner join pmieducar.turma t
                    on t.cod_turma = pt.turma_id
                    where true
                    and pt.instituicao_id = $1
                    and pt.ano = $2
                    and t.ref_ref_cod_escola in ({$escola})
                    {$whereDeleted}
                )
                order by updated_at
            ";

            $vinculos = $this->fetchPreparedQuery($sql, $params);

            $attrs = ['id', 'servidor_id', 'turma_id', 'turno_id', 'permite_lancar_faltas_componente', 'disciplinas','tipo_nota', 'updated_at', 'deleted_at'];

            $vinculos = Portabilis_Array_Utils::filterSet($vinculos, $attrs);

            $vinculos = array_map(function ($vinculo) {
                if (is_null($vinculo['disciplinas'])) {
                    $vinculo['disciplinas'] = [];
                } elseif (is_string($vinculo['disciplinas'])) {
                    $vinculo['disciplinas'] = explode(',', $vinculo['disciplinas']);
                }
                return $vinculo;
            }, $vinculos);

            return ['vinculos' => $vinculos];
        }
    }

    protected function getEscolaridade()
    {
        $idesco = $this->getRequest()->idesco;
        $sql = 'SELECT * FROM cadastro.escolaridade where idesco = $1 ';
        $escolaridade = $this->fetchPreparedQuery($sql, [$idesco], true, 'first-row');
        $escolaridade['descricao'] = Portabilis_String_Utils::toUtf8($escolaridade['descricao']);

        return ['escolaridade' => $escolaridade];
    }

    protected function getDadosServidor()
    {
        $servidor = $this->getRequest()->servidor_id;

        $sql = 'SELECT pessoa.nome,
                       pessoa.email,
                       educacenso_cod_docente.cod_docente_inep AS inep
                FROM pmieducar.servidor
                JOIN cadastro.pessoa ON pessoa.idpes = servidor.cod_servidor
                JOIN modules.educacenso_cod_docente ON educacenso_cod_docente.cod_servidor = servidor.cod_servidor
                WHERE servidor.cod_servidor = $1';

        $result = $this->fetchPreparedQuery($sql, [$servidor]);

        return ['result' => $result[0]];
    }

    protected function getUnificacoes()
    {
        $modified = $this->getRequest()->modified;

        $unificationsQuery = LogUnification::query();

        if ($modified) {
            $unificationsQuery->where('created_at', '>=', $modified);
        }

        $unificationsQuery->whereHas('personMain', function ($individualQuery) {
            $individualQuery->whereHas('employee');
        });

        $unificationsQuery->person();

        return ['unificacoes' => $unificationsQuery->get(['main_id', 'duplicates_id', 'created_at', 'active'])];
    }

    /**
     * @return bool
     */
    private function validateDeficiencies()
    {
        $deficiencias = explode(',', $this->getRequest()->deficiencias);
        $deficiencias = array_filter((array) $deficiencias);
        $deficiencias = $this->replaceByEducacensoDeficiencies($deficiencias);
        $validator = new DeficiencyValidator($deficiencias);

        if ($validator->isValid()) {
            return true;
        } else {
            $this->messenger->append($validator->getMessage());
            return false;
        }
    }

    /**
     * @return array
     */
    private function replaceByEducacensoDeficiencies($deficiencies)
    {
        $databaseDeficiencies = LegacyDeficiency::all()->getKeyValueArray('deficiencia_educacenso');
        $arrayEducacensoDeficiencies = [];

        foreach ($deficiencies as $deficiency) {
            $arrayEducacensoDeficiencies[] = $databaseDeficiencies[(int)$deficiency];
        }

        return $arrayEducacensoDeficiencies;
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'servidor-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'escolaridade')) {
            $this->appendResponse($this->getEscolaridade());
        } elseif ($this->isRequestFor('get', 'servidores-disciplinas-turmas')) {
            $this->appendResponse($this->getServidoresDisciplinasTurmas());
        } elseif ($this->isRequestFor('get', 'dados-servidor')) {
            $this->appendResponse($this->getDadosServidor());
        } elseif ($this->isRequestFor('get', 'verifica-deficiencias')) {
            $this->appendResponse($this->validateDeficiencies());
        } elseif ($this->isRequestFor('get', 'servidores')) {
            $this->appendResponse($this->getServidores());
        } elseif ($this->isRequestFor('get', 'unificacoes')) {
            $this->appendResponse($this->getUnificacoes());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
