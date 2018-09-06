<?php

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'include/pmieducar/geral.inc.php';

class SerieController extends ApiCoreController
{
    protected function canGetSeries()
    {
        return $this->validatesPresenceOf('instituicao_id') && $this->validatesPresenceOf('escola_id') && $this->validatesPresenceOf('curso_id');
    }

    protected function getSeries()
    {
        if ($this->canGetSeries()) {
            $instituicaoId = $this->getRequest()->instituicao_id;
            $escolaId = $this->getRequest()->escola_id;
            $cursoId = $this->getRequest()->curso_id;

            if (is_array($escolaId)) {
                $escolaId = implode(',', $escolaId);
            }

            if (is_array($cursoId)) {
                $cursoId = implode(',', $cursoId);
            }

            $sql = "SELECT distinct s.cod_serie, s.nm_serie, s.idade_ideal
                FROM pmieducar.serie s
                INNER JOIN pmieducar.escola_serie es ON es.ref_cod_serie = s.cod_serie
                INNER JOIN pmieducar.curso c ON s.ref_cod_curso = c.cod_curso
                WHERE es.ativo = 1
                AND s.ativo = 1
                AND c.ativo = 1
                AND es.ref_cod_escola IN ({$escolaId})
                AND c.ref_cod_instituicao = $1
                AND c.cod_curso IN ({$cursoId})
                ORDER BY s.nm_serie ASC ";

            $params = [$this->getRequest()->instituicao_id];

            $series = $this->fetchPreparedQuery($sql, $params);

            foreach ($series as &$serie) {
                $serie['nm_serie'] = mb_strtoupper($serie['nm_serie'], 'UTF-8');
            }

            $attrs = [
                'cod_serie' => 'id',
                'nm_serie' => 'nome',
                'idade_ideal' => 'idade_padrao'
            ];

            $series = Portabilis_Array_Utils::filterSet($series, $attrs);

            return ['series' => $series ];
        }
    }

    protected function getSeriesSemComponentesVinculados()
    {
        $cursoId = $this->getRequest()->curso_id;

        $sql = 'SELECT distinct s.cod_serie, s.nm_serie
                  FROM pmieducar.serie s
                  WHERE s.ativo = 1
                  AND s.ref_cod_curso = $1
                  AND s.cod_serie NOT IN (SELECT DISTINCT ano_escolar_id
                                            FROM modules.componente_curricular_ano_escolar)
                  ORDER BY s.nm_serie ASC ';

        $params = [$cursoId];

        $series = $this->fetchPreparedQuery($sql, $params);

        foreach ($series as &$serie) {
            $serie['nm_serie'] = mb_strtoupper($serie['nm_serie'], 'UTF-8');
        }

        $attrs = [
          'cod_serie' => 'id',
          'nm_serie' => 'nome'
        ];

        $series = Portabilis_Array_Utils::filterSet($series, $attrs);

        return ['series' => $series ];
    }

    protected function getSeriesPorEscola()
    {
        $escolas = $this->getRequest()->escolas;

        if (is_array($escolas)) {
            foreach ($escolas as $key => $escola) {
                $query[$key] = "SELECT distinct s.cod_serie, s.nm_serie
                              FROM pmieducar.serie s
                             INNER JOIN pmieducar.escola_serie es ON es.ref_cod_serie = s.cod_serie
                             WHERE es.ativo = 1
                               AND s.ativo = 1
                               AND es.ref_cod_escola = $escola ";
            }

            $query = implode("\n INTERSECT \n", $query);
            $orderBy = ' ORDER BY nm_serie ASC ';
            $sql = $query . $orderBy;
        } else {
            $sql = "SELECT distinct s.cod_serie, s.nm_serie
                  FROM pmieducar.serie s
                 INNER JOIN pmieducar.escola_serie es ON es.ref_cod_serie = s.cod_serie
                 WHERE es.ativo = 1
                   AND s.ativo = 1
                   AND es.ref_cod_escola = $escolas
                 ORDER BY s.nm_serie ASC ";
        }

        $series = $this->fetchPreparedQuery($sql);

        foreach ($series as &$serie) {
            $serie['nm_serie'] = mb_strtoupper($serie['nm_serie'], 'UTF-8');
        }

        $attrs = [
            'cod_serie' => 'id',
            'nm_serie' => 'nome'
        ];

        $series = Portabilis_Array_Utils::filterSet($series, $attrs);

        return ['series' => $series ];
    }

    protected function getSeriesPorCurso()
    {
        $cursoId = $this->getRequest()->curso_id;

        $sql = 'SELECT distinct s.cod_serie, s.nm_serie
              FROM pmieducar.serie s
              WHERE s.ativo = 1
              AND s.ref_cod_curso = $1
              ORDER BY s.nm_serie ASC ';

        $params = [$cursoId];
        $series = $this->fetchPreparedQuery($sql, $params);

        foreach ($series as &$serie) {
            $serie['nm_serie'] = mb_strtoupper($serie['nm_serie'], 'UTF-8');
        }

        $attrs = [
            'cod_serie' => 'id',
            'nm_serie' => 'nome'
        ];

        $series = Portabilis_Array_Utils::filterSet($series, $attrs);

        return ['series' => $series ];
    }

    protected function getSeriesCursoGrouped()
    {
        $sql = 'SELECT c.cod_curso AS cod_curso, c.nm_curso AS nm_curso, s.cod_serie AS id, s.nm_serie AS nome
                FROM pmieducar.serie s
          INNER JOIN pmieducar.curso c ON c.cod_curso = s.ref_cod_curso
            ORDER BY c.nm_curso, s.nm_serie';

        $series = $this->fetchPreparedQuery($sql);
        $attrs = ['id', 'nome', 'cod_curso', 'nm_curso'];
        $series = Portabilis_Array_Utils::filterSet($series, $attrs);

        foreach ($series as $serie) {
            $seriePorCurso[$serie['cod_curso']]['nome'] = $serie['nm_curso'];
            $seriePorCurso[$serie['cod_curso']]['series'][$serie['id']] = $serie['nome'];
        }
        return ['options' => $seriePorCurso ];
    }

    protected function canGetBloqueioFaixaEtaria()
    {
        return $this->validatesPresenceOf('instituicao_id') && $this->validatesPresenceOf('serie_id') && $this->validatesPresenceOf('data_nascimento');
    }

    protected function getBloqueioFaixaEtaria()
    {
        if ($this->canGetBloqueioFaixaEtaria()) {
            $instituicaoId = $this->getRequest()->instituicao_id;
            $serieId = $this->getRequest()->serie_id;
            $dataNascimento = $this->getRequest()->data_nascimento;
            $ano = isset($this->getRequest()->ano) ? $this->getRequest()->ano : date('Y');

            $objSerie = new clsPmieducarSerie($serieId);
            $detSerie = $objSerie->detalhe();

            $permiteFaixaEtaria = $objSerie->verificaPeriodoCorteEtarioDataNascimento($dataNascimento, $ano);

            $alertaFaixaEtaria = $detSerie['alerta_faixa_etaria'] == 't';
            $bloquearMatriculaFaixaEtaria = $detSerie['bloquear_matricula_faixa_etaria'] == 't';

            $retorno = ['bloqueado' => false, 'mensagem_bloqueio' => ''];

            if (!$permiteFaixaEtaria) {
                if ($alertaFaixaEtaria || $bloquearMatriculaFaixaEtaria) {
                    $retorno['bloqueado'] = $bloquearMatriculaFaixaEtaria;
                    $retorno['mensagem_bloqueio'] = 'A idade do aluno encontra-se fora da faixa etária pré-definida para esta série.';
                }
            }

            return $retorno;
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'series')) {
            $this->appendResponse($this->getSeries());
        } elseif ($this->isRequestFor('get', 'series-curso')) {
            $this->appendResponse($this->getSeriesPorCurso());
        } elseif ($this->isRequestFor('get', 'series-escola')) {
            $this->appendResponse($this->getSeriesPorEscola());
        } elseif ($this->isRequestFor('get', 'series-curso-sem-componentes')) {
            $this->appendResponse($this->getSeriesSemComponentesVinculados());
        } elseif ($this->isRequestFor('get', 'bloqueio-faixa-etaria')) {
            $this->appendResponse($this->getBloqueioFaixaEtaria());
        } elseif ($this->isRequestFor('get', 'series-curso-grouped')) {
            $this->appendResponse($this->getSeriesCursoGrouped());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
