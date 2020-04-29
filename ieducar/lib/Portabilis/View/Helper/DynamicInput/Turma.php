<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';

class Portabilis_View_Helper_DynamicInput_Turma extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ref_cod_turma';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $instituicaoId = $this->getInstituicaoId($options['instituicaoId'] ?? null);
        $escolaId = $this->getEscolaId($options['escolaId'] ?? null);
        $serieId = $this->getSerieId($options['serieId'] ?? null);
        $ano = $this->viewInstance->ano;
        $naoFiltrarAno = $this->viewInstance->nao_filtrar_ano ?? null;

        $userId = $this->getCurrentUserId();
        $isOnlyProfessor = Portabilis_Business_Professor::isOnlyProfessor($instituicaoId, $userId);

        if ($escolaId and $serieId and empty($resources) and $isOnlyProfessor) {
            $resources = collect(Portabilis_Business_Professor::turmasAlocado($instituicaoId, $escolaId, $serieId, $userId))
                ->keyBy('id')
                ->map(function ($turma) {
                    return $turma['nome'] . ' - ' . $turma['ano'];
                })->toArray();
        } elseif ($escolaId && $serieId && empty($resources)) {
            $resources = App_Model_IedFinder::getTurmas($escolaId, $serieId);
        }

        // caso no letivo esteja definido para filtrar turmas por ano,
        // somente exibe as turmas do ano letivo.

        if ($escolaId && $ano && !$naoFiltrarAno && $this->turmasPorAno($escolaId, $ano)) {
            foreach ($resources as $id => $nome) {
                $turma = new clsPmieducarTurma();
                $turma->cod_turma = $id;
                $turma = $turma->detalhe();

                if ($turma['ano'] != $ano) {
                    unset($resources[$id]);
                }
            }
        }

        return $this->insertOption(null, 'Selecione uma turma', $resources);
    }

    protected function turmasPorAno($escolaId, $ano)
    {
        $anoLetivo = new clsPmieducarEscolaAnoLetivo();
        $anoLetivo->ref_cod_escola = $escolaId;
        $anoLetivo->ano = $ano;
        $anoLetivo = $anoLetivo->detalhe();

        return $anoLetivo['turmas_por_ano'] == 1;
    }

    public function turma($options = [])
    {
        parent::select($options);
    }
}
