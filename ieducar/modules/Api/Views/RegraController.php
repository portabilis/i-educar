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
              SELECT DISTINCT regra_avaliacao.id,
                              tabela_arredondamento_id,
                              tabela_arredondamento_id_conceitual,
                              tipo_nota,
                              tipo_presenca,
                              parecer_descritivo,
                              cod_turma AS turma_id,
                              tipo_recuperacao_paralela AS tipo_recuperacao,
                              media_recuperacao_paralela,
                              nota_maxima_geral,
                              nota_maxima_exame_final AS nota_maxima_exame,
                              COALESCE(regra_avaliacao.regra_diferenciada_id, 0) AS regra_diferenciada_id
              FROM modules.regra_avaliacao
              LEFT JOIN modules.regra_avaliacao_serie_ano rasa
                ON rasa.regra_avaliacao_id = regra_avaliacao.id
              LEFT JOIN pmieducar.serie serie
                ON rasa.serie_id = serie.cod_serie
                AND serie.ativo = 1
              LEFT JOIN pmieducar.turma turma ON turma.ref_ref_cod_serie = serie.cod_serie
              AND turma.ativo = 1
              AND regra_avaliacao.instituicao_id = $1
              AND turma.ano = $2
              ORDER BY COALESCE(regra_avaliacao.regra_diferenciada_id,0),
                       regra_avaliacao.id,
                       cod_turma';

            $_regras = $this->fetchPreparedQuery($sql, [
                $instituicaoId, $ano
                ]
            );

            $attrs = [
                'id', 'tabela_arredondamento_id', 'tabela_arredondamento_id_conceitual',
                'tipo_nota', 'tipo_presenca', 'parecer_descritivo', 'turma_id',
                'tipo_recuperacao', 'media_recuperacao_paralela', 'nota_maxima_geral',
                'nota_maxima_exame', 'regra_diferenciada_id'
            ];

            $_regras = Portabilis_Array_Utils::filterSet($_regras, $attrs);
            $regras = [];
            $__regras = [];

            foreach ($_regras as $regra) {
                $__regras[$regra['id']]['id'] = $regra['id'];
                $__regras[$regra['id']]['regra_diferenciada_id']= $regra['regra_diferenciada_id'] ?: null;
                $__regras[$regra['id']]['tabela_arredondamento_id']= $regra['tabela_arredondamento_id'];
                $__regras[$regra['id']]['tabela_arredondamento_id_conceitual'] = $regra['tabela_arredondamento_id_conceitual'];
                $__regras[$regra['id']]['tipo_nota'] = $regra['tipo_nota'];
                $__regras[$regra['id']]['tipo_presenca'] = $regra['tipo_presenca'];
                $__regras[$regra['id']]['parecer_descritivo'] = $regra['parecer_descritivo'];
                $__regras[$regra['id']]['tipo_recuperacao'] = $regra['tipo_recuperacao'];
                $__regras[$regra['id']]['media_recuperacao_paralela'] = $regra['media_recuperacao_paralela'];
                $__regras[$regra['id']]['nota_maxima_geral'] = $regra['nota_maxima_geral'];
                $__regras[$regra['id']]['nota_maxima_exame'] = $regra['nota_maxima_exame'];
                $__regras[$regra['id']]['turmas'] || $__regras[$regra['id']]['turmas'] = [];

                if ($regra['turma_id']) {
                    $__regras[$regra['id']]['turmas'][] = [
                        'turma_id' => $regra['turma_id'],
                    ];
                }
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
