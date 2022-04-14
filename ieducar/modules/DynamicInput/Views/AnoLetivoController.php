<?php

class AnoLetivoController extends ApiCoreController
{
    protected function canGetAnosLetivos()
    {
        return $this->validatesId('escola');
    }

    protected function canGetAnosLetivosPorEscolaSerie()
    {
        return $this->validatesId('escola') && $this->validatesId('serie');
    }

    protected function filtroSituacao()
    {
        $tiposSituacao  = ['nao_iniciado' => 0, 'em_andamento' => 1, 'finalizado' => 2];
        $situacaoIn     = [];

        foreach ($tiposSituacao as $nome => $flag) {
            if ($this->getRequest()->{"situacao_$nome"} == true) {
                $situacaoIn[] = $flag;
            }
        }

        return (empty($situacaoIn) ? '' : 'and al.andamento in ('. implode(',', $situacaoIn) . ')');
    }

    protected function getAnosLetivos()
    {
        if ($this->canGetAnosLetivos()) {
            $params       = [$this->getRequest()->escola_id];
            $sql          = "select ano from pmieducar.escola_ano_letivo as al where ref_cod_escola = $1
                       and ativo = 1 {$this->filtroSituacao()} order by ano desc";

            $records = $this->fetchPreparedQuery($sql, $params);
            $options = [];

            foreach ($records as $record) {
                $options[$record['ano']] = $record['ano'];
            }

            return ['options' => $options];
        }
    }

    protected function getAnosLetivosPorEscolaSerie()
    {
        if ($this->canGetAnosLetivos()) {
            $anosLetivos = App_Model_IedFinder::getAnosLetivosEscolaSerie($this->getRequest()->escola_id, $this->getRequest()->serie_id);
            asort($anosLetivos);

            return [ 'options' => $anosLetivos ];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'anos_letivos')) {
            $this->appendResponse($this->getAnosLetivos());
        } elseif ($this->isRequestFor('get', 'anos_letivos_escola_serie')) {
            $this->appendResponse($this->getAnosLetivosPorEscolaSerie());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
