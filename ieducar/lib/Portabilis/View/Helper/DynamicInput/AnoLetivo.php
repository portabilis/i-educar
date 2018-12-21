<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/App/Model/IedFinder.php';

class Portabilis_View_Helper_DynamicInput_AnoLetivo extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ano_letivo';
    }

    protected function filtroSituacao()
    {
        $tiposSituacao = ['nao_iniciado' => 0, 'em_andamento' => 1, 'finalizado' => 2];
        $situacaoIn = [];

        foreach ($tiposSituacao as $nome => $flag) {
            if (in_array("$nome", $this->options['situacoes'])) {
                $situacaoIn[] = $flag;
            }
        }

        return (empty($situacaoIn) ? '' : 'and andamento in (' . implode(',', $situacaoIn) . ')');
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $escolaId = $this->getEscolaId($options['escolaId']);
        $serieId = $this->getSerieId($options['serieId']);

        if ($serieId && $escolaId && empty($resources)) {
            $resources = App_Model_IedFinder::getAnosLetivosEscolaSerie($escolaId, $serieId);
        } elseif ($escolaId && empty($resources)) {
            $sql = "
                select ano 
                from pmieducar.escola_ano_letivo as al 
                where ref_cod_escola = $1
                and ativo = 1 {$this->filtroSituacao()}
                order by ano desc
            ";

            $resources = Portabilis_Utils_Database::fetchPreparedQuery($sql, ['params' => $escolaId]);
            $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'ano', 'ano');
        }

        return $this->insertOption(null, 'Selecione um ano letivo', $resources);
    }

    protected function defaultOptions()
    {
        return ['escolaId' => null, 'situacoes' => ['em_andamento', 'nao_iniciado', 'finalizado']];
    }

    public function anoLetivo($options = [])
    {
        parent::select($options);

        foreach ($this->options['situacoes'] as $situacao) {
            $this->viewInstance->appendOutput("<input type='hidden' name='situacoes_ano_letivo' value='$situacao' />");
        }
    }
}
