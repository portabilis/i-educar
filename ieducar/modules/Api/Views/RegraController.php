<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';

class RegraController extends ApiCoreController
{

    protected function canGetTabelasDeArredondamento()
    {
        return $this->validatesPresenceOf('instituicao_id');
    }

    protected function canGetRegras()
    {
        return $this->validatesPresenceOf('instituicao_id');
    }

    protected function canGetRegrasRecuperacao()
    {
        return $this->validatesPresenceOf('instituicao_id');
    }

    protected function canGetRegraSerie()
    {
        return $this->validatesPresenceOf('serie_id') &&
        $this->validatesPresenceOf('ano_letivo');
    }

    protected function canGetRegraSerieAno()
    {
        return $this->validatesPresenceOf('ano');
    }

    protected function getTabelasDeArredondamento()
    {
        if ($this->canGetTabelasDeArredondamento()) {
            $instituicaoId = $this->getRequest()->instituicao_id;

            $params = [$instituicaoId];
            $modified = $this->getRequest()->modified ?: null;

            if ($modified) {
                $params[] = $modified;
                $modified = 'AND updated_at >= $2';
            }

            $sql = "
                SELECT
                    ta.id,
                    ta.nome,
                    ta.tipo_nota,
                    tav.nome as rotulo,
                    tav.descricao,
                    tav.valor_maximo,
                    tav.casa_decimal_exata,
                    tav.acao,
                    ta.updated_at
                FROM modules.tabela_arredondamento ta
                INNER JOIN modules.tabela_arredondamento_valor tav
                    ON tav.tabela_arredondamento_id = ta.id
                WHERE ta.instituicao_id = $1
                {$modified}
                ORDER BY ta.updated_at
            ";

            $tabelas = $this->fetchPreparedQuery($sql, $params);

            $attrs = ['id', 'nome', 'tipo_nota', 'rotulo', 'descricao', 'valor_maximo', 'casa_decimal_exata', 'acao', 'updated_at'];
            $tabelas = Portabilis_Array_Utils::filterSet($tabelas, $attrs);
            $_tabelas = [];

            foreach ($tabelas as $tabela) {
                $_tabelas[$tabela['id']]['id'] = $tabela['id'];
                $_tabelas[$tabela['id']]['nome'] = $tabela['nome'];
                $_tabelas[$tabela['id']]['tipo_nota'] = $tabela['tipo_nota'];
                $_tabelas[$tabela['id']]['valores'][] = [
                    'rotulo' => $tabela['rotulo'],
                    'descricao' => $tabela['descricao'],
                    'valor_maximo' => $tabela['valor_maximo'],
                    'casa_decimal_exata' => $tabela['casa_decimal_exata'],
                    'acao' => $tabela['acao'],
                ];
                $_tabelas[$tabela['id']]['updated_at'] = $tabela['updated_at'];
            }

            $tabelas = [];

            foreach ($_tabelas as $tabela) {
                $tabelas[] = $tabela;
            }

            return ['tabelas' => $tabelas];
        }
    }

    protected function getRegrasRecuperacao()
    {
        if ($this->canGetRegrasRecuperacao()) {
            $instituicaoId = $this->getRequest()->instituicao_id;
            $modified = $this->getRequest()->modified;

            $params = [$instituicaoId];
            $where = '';

            if ($modified) {
                $params[] = $modified;
                $where = ' AND rar.updated_at >= $2';
            }

            $sql = "
                (
                    SELECT
                        rar.id,
                        rar.regra_avaliacao_id,
                        rar.descricao,
                        rar.etapas_recuperadas,
                        rar.media,
                        rar.nota_maxima,
                        rar.updated_at,
                        null as deleted_at
                    FROM modules.regra_avaliacao_recuperacao rar
                    INNER JOIN modules.regra_avaliacao ra
                        ON rar.regra_avaliacao_id = ra.id
                    WHERE ra.instituicao_id = $1
                    {$where}
                )

                UNION ALL

                (
                    SELECT
                        rar.id,
                        rar.regra_avaliacao_id,
                        rar.descricao,
                        rar.etapas_recuperadas,
                        rar.media,
                        rar.nota_maxima,
                        rar.updated_at,
                        rar.deleted_at
                    FROM modules.regra_avaliacao_recuperacao_excluidos rar
                    INNER JOIN modules.regra_avaliacao ra
                        ON rar.regra_avaliacao_id = ra.id
                    WHERE ra.instituicao_id = $1
                    {$where}
                )
                ORDER BY updated_at
            ";

            $regrasRecuperacao = $this->fetchPreparedQuery($sql, $params);
            $regrasRecuperacao = Portabilis_Array_Utils::filterSet($regrasRecuperacao, [
                'id', 'regra_avaliacao_id', 'descricao', 'etapas_recuperadas',
                'media', 'nota_maxima', 'updated_at', 'deleted_at'
            ]);

            foreach ($regrasRecuperacao as &$regra) {
                $regra['etapas_recuperadas'] = explode(';', $regra['etapas_recuperadas']);
            }

            return [
                'regras-recuperacao' => $regrasRecuperacao
            ];
        }
    }

    protected function getRegras()
    {
        if ($this->canGetRegras()) {
            $instituicaoId = $this->getRequest()->instituicao_id;
            $modified = $this->getRequest()->modified;

            $params = [$instituicaoId];

            $where = '';

            if ($modified) {
                $params[] = $modified;
                $where = ' AND ra.updated_at >= $2';
            }

            $sql = '
              SELECT
                  DISTINCT ra.id,
                  ra.tabela_arredondamento_id,
                  ra.tabela_arredondamento_id_conceitual,
                  ra.tipo_nota,
                  ra.tipo_presenca,
                  ra.parecer_descritivo,
                  ra.tipo_recuperacao_paralela AS tipo_recuperacao,
                  ra.media_recuperacao_paralela,
                  ra.tipo_calculo_recuperacao_paralela,
                  ra.nota_maxima_geral,
                  ra.nota_maxima_exame_final AS nota_maxima_exame,
                  COALESCE(ra.regra_diferenciada_id, 0) AS regra_diferenciada_id,
                  ra.updated_at
              FROM modules.regra_avaliacao ra
              WHERE true
                  AND ra.instituicao_id = $1 '. $where . '
              ORDER BY
                ra.updated_at,
                COALESCE(ra.regra_diferenciada_id,0),
                ra.id';

            $_regras = $this->fetchPreparedQuery($sql, $params);

            $attrs = [
                'id', 'tabela_arredondamento_id', 'tabela_arredondamento_id_conceitual',
                'tipo_nota', 'tipo_presenca', 'parecer_descritivo',
                'tipo_recuperacao', 'media_recuperacao_paralela', 'nota_maxima_geral',
                'nota_maxima_exame', 'updated_at', 'regra_diferenciada_id',
                'tipo_calculo_recuperacao_paralela'
            ];

            $_regras = Portabilis_Array_Utils::filterSet($_regras, $attrs);

            return ['regras' => $_regras];
        }
    }

    public function getRegraSerie()
    {
        $serieId = $this->getRequest()->serie_id;
        $anoLetivo = $this->getRequest()->ano_letivo;

        if ($this->canGetRegraSerie()) {
            $sql = 'SELECT ra.*
                      FROM modules.regra_avaliacao AS ra
                INNER JOIN modules.regra_avaliacao_serie_ano AS rasa ON rasa.regra_avaliacao_id = ra.id
                     WHERE rasa.serie_id = $1
                       AND rasa.ano_letivo = $2
                     LIMIT 1';

            $regra = $this->fetchPreparedQuery($sql, [$serieId, $anoLetivo]);

            $atributos = [
                'id',
                'tabela_arredondamento_id',
                'tipo_nota',
                'tipo_presenca',
                'parecer_descritivo',
                'turma_id',
                'tipo_recuperacao',
                'media_recuperacao_paralela',
                'nota_maxima_geral',
                'nota_maxima_exame'
            ];

            $regra = Portabilis_Array_Utils::filterSet($regra, $atributos);

            return $regra[0];
        }
    }

    public function getRegraSerieAno()
    {
        if (empty($this->canGetRegraSerieAno())) {
            return;
        }

        $ano = $this->getRequest()->ano;
        $modified = $this->getRequest()->modified;

        $params = [$ano];

        if ($modified) {
            $params[] = $modified;
            $modified = " AND updated_at >= $2";
        }

        $sql = "
            (
                SELECT 
                    serie_id,
                    regra_avaliacao_id,
                    regra_avaliacao_diferenciada_id,
                    ano_letivo,
                    updated_at,
                    null as deleted_at
                FROM modules.regra_avaliacao_serie_ano
                WHERE ano_letivo = $1
                {$modified}
            )
            UNION 
            (
                SELECT 
                    serie_id,
                    regra_avaliacao_id,
                    regra_avaliacao_diferenciada_id,
                    ano_letivo,
                    updated_at,
                    deleted_at
                FROM modules.regra_avaliacao_serie_ano_excluidos
                WHERE ano_letivo = $1
                {$modified}
            )
            ORDER BY updated_at
        ";

        $regras = $this->fetchPreparedQuery($sql, $params);

        $attrs = [
            'serie_id',
            'regra_avaliacao_id',
            'regra_avaliacao_diferenciada_id',
            'ano_letivo',
            'updated_at',
            'deleted_at',
        ];

        $regras = Portabilis_Array_Utils::filterSet($regras, $attrs);

        return [
            'regras' => $regras
        ];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'tabelas-de-arredondamento')) {
            $this->appendResponse($this->getTabelasDeArredondamento());
        } elseif ($this->isRequestFor('get', 'regras')) {
            $this->appendResponse($this->getRegras());
        } elseif ($this->isRequestFor('get', 'regras-recuperacao')) {
            $this->appendResponse($this->getRegrasRecuperacao());
        } elseif ($this->isRequestFor('get', 'regra-serie')) {
            $this->appendResponse($this->getRegraSerie());
        } elseif ($this->isRequestFor('get', 'regra-serie-ano')) {
            $this->appendResponse($this->getRegraSerieAno());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
