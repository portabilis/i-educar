<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Utils/Database.php';

class AreaConhecimentoController extends ApiCoreController
{

    public function canGetAreasDeConhecimento()
    {
        return $this->validatesPresenceOf('instituicao_id');
    }

    public function getAreasDeConhecimento()
    {
        if ($this->canGetAreasDeConhecimento()) {
            $instituicaoId = $this->getRequest()->instituicao_id;
            $modified = $this->getRequest()->modified;

            $params = [$instituicaoId];
            $where = '';

            if ($modified) {
                $params[] = $modified;
                $where = ' AND updated_at >= $2';
            }

            $sql = "
                (
                    SELECT id, nome, ordenamento_ac, updated_at, null as deleted_at
                    FROM modules.area_conhecimento
                    WHERE instituicao_id = $1
                    {$where}
                    
                )
                UNION ALL 
                (
                    SELECT id, nome, ordenamento_ac, updated_at, deleted_at
                    FROM modules.area_conhecimento_excluidos
                    WHERE instituicao_id = $1
                    {$where}
                )
                ORDER BY updated_at, nome 
            ";

            $areas = $this->fetchPreparedQuery($sql, $params);

            $attrs = ['id', 'nome', 'ordenamento_ac', 'updated_at', 'deleted_at'];
            $areas = Portabilis_Array_Utils::filterSet($areas, $attrs);

            return [
                'areas' => $areas
            ];
        }
    }

    protected function getAreasDeConhecimentoForSerie()
    {
        $serieId = $this->getRequest()->serie_id;

        $sql = 'SELECT ac.id as id, (ac.nome) as nome
                  FROM modules.area_conhecimento ac
                 WHERE ac.id in(SELECT area_conhecimento.id
                                  FROM modules.area_conhecimento
                            INNER JOIN modules.componente_curricular cc ON(cc.area_conhecimento_id = ac.id)
                            INNER JOIN modules.componente_curricular_ano_escolar ccae ON (ccae.componente_curricular_id = cc.id
                                                                                                   AND ccae.ano_escolar_id = $1))
              ORDER BY (lower(ac.nome)) ASC';

        $paramsSql = [$serieId];

        return $this->getReturnRequest($this->fetchPreparedQuery($sql, $paramsSql));
    }

    protected function getAreasDeConhecimentoForEscolaSerie()
    {
        $escolaId = $this->getRequest()->escola_id;
        $serieId = $this->getRequest()->serie_id;

        $sql = 'SELECT ac.id AS id, (ac.nome) AS nome
                  FROM modules.area_conhecimento ac
                 WHERE ac.id in (SELECT area_conhecimento.id
                                   FROM modules.area_conhecimento
                             INNER JOIN modules.componente_curricular cc ON(cc.area_conhecimento_id = ac.id)
                             INNER JOIN pmieducar.escola_serie_disciplina esd ON(esd.ref_cod_disciplina = cc.id)
                                  WHERE esd.ref_ref_cod_escola = $1
                                    AND esd.ref_ref_cod_serie = $2
                       )
              ORDER BY (lower(ac.nome)) ASC';

        $paramsSql = [$escolaId, $serieId];

        return $this->getReturnRequest($this->fetchPreparedQuery($sql, $paramsSql));
    }

    protected function getAreasDeConhecimentoForTurma()
    {
        $turmaId = $this->getRequest()->turma_id;

        $sql = 'SELECT ac.id AS id, (ac.nome) AS nome
                  FROM modules.area_conhecimento ac
                 WHERE ac.id in ( SELECT distinct area_conhecimento_id
                                    FROM relatorio.view_componente_curricular
                                   WHERE cod_turma = $1
                 )
                 ORDER BY (lower(ac.nome)) ASC';

        $paramsSql = [$turmaId];

        return $this->getReturnRequest($this->fetchPreparedQuery($sql, $paramsSql));
    }

    protected function getReturnRequest($areasConhecimento)
    {
        $options = [];
        $options = Portabilis_Array_Utils::setAsIdValue($areasConhecimento, 'id', 'nome');

        return ['options' => $options];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'areas-de-conhecimento')) {
            $this->appendResponse($this->getAreasDeConhecimento());
        } elseif ($this->isRequestFor('get', 'areaconhecimento-serie')) {
            $this->appendResponse($this->getAreasDeConhecimentoForSerie());
        } elseif ($this->isRequestFor('get', 'areaconhecimento-turma')) {
            $this->appendResponse($this->getAreasDeConhecimentoForTurma());
        } elseif ($this->isRequestFor('get', 'areaconhecimento-escolaserie')) {
            $this->appendResponse($this->getAreasDeConhecimentoForEscolaSerie());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
