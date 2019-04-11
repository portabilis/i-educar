<?php

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
            $this->validatesPresenceOf('instituicao_id')
        );
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

    protected function getServidoresDisciplinasTurmas()
    {
        if ($this->canGetServidoresDisciplinasTurmas()) {
            $instituicaoId = $this->getRequest()->instituicao_id;
            $ano = $this->getRequest()->ano;
            $modified = $this->getRequest()->modified;

            $params = [$instituicaoId, $ano];

            $where = '';

            if ($modified) {
                $params[] = $modified;
                $where = ' AND pt.updated_at >= $1';
            }

            $sql = "
                (
                    select
                        pt.id,
                        pt.servidor_id,
                        pt.turma_id,
                        pt.turno_id,
                        pt.permite_lancar_faltas_componente,
                        string_agg(ptd.componente_curricular_id::varchar, ',') as disciplinas,
                        ccae.tipo_nota,
                        pt.updated_at,
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
                    {$where}
                    group by id, ccae.tipo_nota
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
                        null as tipo_nota,
                        pt.updated_at,
                        pt.deleted_at
                    from modules.professor_turma_excluidos pt 
                    where true 
                    and pt.instituicao_id = $1
                    and pt.ano = $2
                    {$where}
                )
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

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'servidor-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'escolaridade')) {
            $this->appendResponse($this->getEscolaridade());
        } elseif ($this->isRequestFor('get', 'servidores-disciplinas-turmas')) {
            $this->appendResponse($this->getServidoresDisciplinasTurmas());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
