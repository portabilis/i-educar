<?php

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Date/Utils.php';

class CursoController extends ApiCoreController
{

    protected function canGetCursos()
    {
        return $this->validatesPresenceOf('instituicao_id');
    }

    protected function getCursos()
    {
        if ($this->canGetCursos()) {
            $instituicaoId = $this->getRequest()->instituicao_id;
            $escolaId = $this->getRequest()->escola_id;
            $getSeries = (bool) $this->getRequest()->get_series;
            $getTurmas = (bool) $this->getRequest()->get_turmas;
            $ano = $this->getRequest()->ano ? $this->getRequest()->ano : 0;
            $turnoId = $this->getRequest()->turno_id;

            $params = [ $this->getRequest()->instituicao_id ];

            if ($escolaId) {
                if (is_array($escolaId)) {
                    $escolaId = implode(",", $escolaId);
                }

                $sql = "SELECT DISTINCT c.cod_curso, c.nm_curso
                  FROM pmieducar.curso c
                  INNER JOIN pmieducar.escola_curso ec ON ec.ref_cod_curso = c.cod_curso
                  WHERE c.ativo = 1
                  AND ec.ativo = 1
                  AND c.ref_cod_instituicao = $1
                  AND ec.ref_cod_escola IN ($escolaId) ";

                if (!empty($ano)) {
                    $params[] = $ano;
                    $sql .= ' AND $2 = ANY(ec.anos_letivos) ';
                }

                $sql .= ' ORDER BY c.nm_curso ASC ';
            } else {
                $sql = "SELECT cod_curso, nm_curso
                  FROM pmieducar.curso
                    WHERE ref_cod_instituicao = $1
                    AND ativo = 1
                    ORDER BY nm_curso ASC ";
            }

            $cursos = $this->fetchPreparedQuery($sql, $params);

            $sqlSerie = "SELECT DISTINCT s.cod_serie, s.nm_serie
                    FROM pmieducar.serie s
                    INNER JOIN pmieducar.escola_serie es ON es.ref_cod_serie = s.cod_serie
                    WHERE es.ativo = 1
                    AND s.ativo = 1";
            if ($escolaId) {
                $sqlSerie .= " AND es.ref_cod_escola IN ({$escolaId}) ";
            }
            $paramsSerie = [];
            if (!empty($ano)) {
                $paramsSerie[] = $ano;
                $sqlSerie .= ' AND $1 = ANY(es.anos_letivos) ';
            }

            $sqlTurma = "SELECT DISTINCT t.cod_turma, t.nm_turma, t.ref_ref_cod_escola as escola_id, t.turma_turno_id, t.ano as ano
                    FROM pmieducar.turma t
                    WHERE t.ativo = 1
                    AND (CASE WHEN {$ano} = '0' THEN ano is not null else t.ano = {$ano} END)
                    AND t.ref_ref_cod_escola IN ({$escolaId}) ";

            foreach ($cursos as &$curso) {
                $curso['nm_curso'] = Portabilis_String_Utils::toUtf8($curso['nm_curso']);
                if ($getSeries) {
                    $series = $this->fetchPreparedQuery($sqlSerie . " AND s.ref_cod_curso = {$curso['cod_curso']} ORDER BY s.nm_serie ASC", $paramsSerie);

                    $attrs = array('cod_serie' => 'id', 'nm_serie' => 'nome');
                    foreach ($series as &$serie) {
                        $serie['nm_serie'] = Portabilis_String_Utils::toUtf8($serie['nm_serie']);

                        if ($getTurmas && is_numeric($ano) && !empty($escolaId)) {
                            $turmas = $this->fetchPreparedQuery($sqlTurma . " AND t.ref_cod_curso = {$curso['cod_curso']} AND t.ref_ref_cod_serie = {$serie['cod_serie']}
                  " . (is_numeric($turnoId) ? " AND t.turma_turno_id = {$turnoId} " : "") . "
               ORDER BY t.nm_turma ASC");
                            foreach ($turmas as &$turma) {
                                $turma['nm_turma'] = Portabilis_String_Utils::toUtf8($turma['nm_turma']);
                            }
                            $attrs['turmas'] = 'turmas';
                            $serie['turmas'] = Portabilis_Array_Utils::filterSet($turmas, ['cod_turma', 'nm_turma', 'escola_id', 'turma_turno_id', 'ano']);
                        }
                    }
                    $curso['series'] = Portabilis_Array_Utils::filterSet($series, $attrs);
                }
            }

            $attrs = [
                'cod_curso' => 'id',
                'nm_curso' => 'nome'
            ];

            if ($getSeries) {
                $attrs['series'] = 'series';
            }

            $cursos = Portabilis_Array_Utils::filterSet($cursos, $attrs);

            return ['cursos' => $cursos ];
        }
    }

    protected function getCursosMultipleSearch()
    {
        $instituicaoId = $this->getRequest()->instituicao_id;

        $sql = "SELECT cod_curso AS id,
                   nm_curso AS nome
              FROM pmieducar.curso
             INNER JOIN pmieducar.instituicao ON (instituicao.cod_instituicao = curso.ref_cod_instituicao)
             WHERE curso.ativo = 1
               AND instituicao.cod_instituicao = $instituicaoId";

        $cursos = $this->fetchPreparedQuery($sql);

        foreach ($cursos as &$curso) {
            $curso['nome'] = Portabilis_String_Utils::toUtf8($curso['nome']);
        }

        $cursos = Portabilis_Array_Utils::setAsIdValue($cursos, 'id', 'nome');

        return ['options' => $cursos];
    }

    protected function getModalidadeCurso()
    {
        $cursoId = $this->getRequest()->curso_id;

        if (is_numeric($cursoId)) {
            $sql = 'SELECT modalidade_curso
                FROM pmieducar.curso
               WHERE cod_curso = $1;';
            $modalidade = $this->fetchPreparedQuery($sql, [$cursoId], false, 'first-line');
        }

        return $modalidade;
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'cursos')) {
            $this->appendResponse($this->getCursos());
        } elseif ($this->isRequestFor('get', 'modalidade-curso')) {
            $this->appendResponse($this->getModalidadeCurso());
        } elseif ($this->isRequestFor('get', 'cursos-multiple-search')) {
            $this->appendResponse($this->getCursosMultipleSearch());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
