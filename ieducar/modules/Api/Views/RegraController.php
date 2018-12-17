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
        return (
            $this->validatesPresenceOf('instituicao_id') &&
            $this->validatesPresenceOf('ano')
        );
    }

    protected function canGetRegrasRecuperacao()
    {
        return $this->validatesPresenceOf('instituicao_id');
    }

    protected function canGetRegraSerie()
    {
        return $this->validatesPresenceOf('serie_id');
    }

    protected function getTabelasDeArredondamento()
    {
        if ($this->canGetTabelasDeArredondamento()) {
            $instituicaoId = $this->getRequest()->instituicao_id;

            $sql = 'SELECT ta.id, ta.nome, ta.tipo_nota, tav.nome as rotulo,
                        tav.descricao, tav.valor_maximo, tav.casa_decimal_exata, tav.acao
                FROM modules.tabela_arredondamento ta
                INNER JOIN modules.tabela_arredondamento_valor tav
                ON tav.tabela_arredondamento_id = ta.id
                WHERE ta.instituicao_id = $1';

            $tabelas = $this->fetchPreparedQuery($sql, [$instituicaoId]);

            $attrs = ['id', 'nome', 'tipo_nota', 'rotulo', 'descricao', 'valor_maximo', 'casa_decimal_exata', 'acao'];
            $tabelas = Portabilis_Array_Utils::filterSet($tabelas, $attrs);
            $_tabelas = [];

            foreach ($tabelas as $tabela) {
                $_tabelas[$tabela['id']]['id'] = $tabela['id'];
                $_tabelas[$tabela['id']]['nome'] = Portabilis_String_Utils::toUtf8($tabela['nome']);
                $_tabelas[$tabela['id']]['tipo_nota'] = $tabela['tipo_nota'];
                $_tabelas[$tabela['id']]['valores'][] = [
                    'rotulo' => Portabilis_String_Utils::toUtf8($tabela['rotulo']),
                    'descricao' => Portabilis_String_Utils::toUtf8($tabela['descricao']),
                    'valor_maximo' => Portabilis_String_Utils::toUtf8($tabela['valor_maximo']),
                    'casa_decimal_exata' => Portabilis_String_Utils::toUtf8($tabela['casa_decimal_exata']),
                    'acao' => Portabilis_String_Utils::toUtf8($tabela['acao']),
                ];
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

            $sql = 'SELECT
                rar.id,
                rar.regra_avaliacao_id,
                rar.descricao,
                rar.etapas_recuperadas,
                rar.media,
                rar.nota_maxima
                FROM modules.regra_avaliacao_recuperacao rar
                INNER JOIN modules.regra_avaliacao ra
                    ON rar.regra_avaliacao_id = ra.id
                WHERE ra.instituicao_id = $1
                ';

            $regrasRecuperacao = $this->fetchPreparedQuery($sql, [$instituicaoId]);
            $attrs = ['id', 'regra_avaliacao_id', 'descricao', 'etapas_recuperadas', 'media', 'nota_maxima'];
            $regrasRecuperacao = Portabilis_Array_Utils::filterSet($regrasRecuperacao, $attrs);

            foreach ($regrasRecuperacao as &$regra) {
                $regra['descricao'] = Portabilis_String_Utils::toUtf8($regra['descricao']);
                $regra['etapas_recuperadas'] = explode(';', $regra['etapas_recuperadas']);
            }

            return ['regras-recuperacao' => $regrasRecuperacao];
        }
    }

    protected function getRegras()
    {
        if ($this->canGetRegras()) {
            $instituicaoId = $this->getRequest()->instituicao_id;
            $ano = $this->getRequest()->ano;

            $sql = '
              SELECT 
                  DISTINCT ra.id,
                  ra.tabela_arredondamento_id,
                  ra.tabela_arredondamento_id_conceitual,
                  ra.tipo_nota,
                  ra.tipo_presenca,
                  ra.parecer_descritivo,
                  (
                      SELECT 
                          jsonb_agg(json_build_object(\'turma_id\', t.cod_turma) ORDER BY t.cod_turma)
                      FROM 
                          pmieducar.turma t 
                      INNER JOIN pmieducar.serie s ON true 
                          AND s.cod_serie = t.ref_ref_cod_serie
                      INNER JOIN modules.regra_avaliacao_serie_ano rasa ON true 
                          AND rasa.serie_id = s.cod_serie 
                          AND rasa.ano_letivo = $2
                      WHERE true
                          AND rasa.regra_avaliacao_id = ra.id
                          AND t.ano = $2
                          AND t.ativo = 1
                          AND s.ativo = 1
                  ) AS turmas,
                  ra.tipo_recuperacao_paralela AS tipo_recuperacao,
                  ra.media_recuperacao_paralela,
                  ra.nota_maxima_geral,
                  ra.nota_maxima_exame_final AS nota_maxima_exame,
                  COALESCE(ra.regra_diferenciada_id, 0) AS regra_diferenciada_id
              FROM modules.regra_avaliacao ra
              WHERE true
                  AND ra.instituicao_id = $1
              ORDER BY 
                COALESCE(ra.regra_diferenciada_id,0),
                ra.id';

            $_regras = $this->fetchPreparedQuery($sql, [
                $instituicaoId, $ano
                ]
            );

            $attrs = [
                'id', 'tabela_arredondamento_id', 'tabela_arredondamento_id_conceitual',
                'tipo_nota', 'tipo_presenca', 'parecer_descritivo', 'turmas',
                'tipo_recuperacao', 'media_recuperacao_paralela', 'nota_maxima_geral',
                'nota_maxima_exame', 'regra_diferenciada_id'
            ];

            $_regras = Portabilis_Array_Utils::filterSet($_regras, $attrs);
            $regras = [];
            $__regras = [];

            foreach ($_regras as $regra) {
                $__regras[$regra['id']] = $regra;
                $__regras[$regra['id']]['regra_diferenciada_id']= $regra['regra_diferenciada_id'] ?: null;
                $__regras[$regra['id']]['turmas'] = empty($regra['turmas']) ? [] : json_decode($regra['turmas']);

            }

            foreach ($__regras as $regra) {
                $regras[] = $regra;
            }

            return ['regras' => $regras];
        }
    }

    public function getRegraSerie()
    {
        $serieId = $this->getRequest()->serie_id;

        if ($this->canGetRegraSerie()) {
            $sql = 'SELECT *
                FROM modules.regra_avaliacao
               WHERE regra_avaliacao.id = (SELECT regra_avaliacao_id
                                             FROM pmieducar.serie
                                            WHERE serie.cod_serie = $1) LIMIT 1';

            $regra = $this->fetchPreparedQuery($sql, ['params' => $serieId]);

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
        } else {
            $this->notImplementedOperationError();
        }
    }
}
