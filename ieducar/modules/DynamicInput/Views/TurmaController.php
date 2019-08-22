<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Business/Professor.php';

class TurmaController extends ApiCoreController
{
    protected function canGetTurmas()
    {
        return
            $this->validatesId('instituicao') &&
            $this->validatesId('escola') &&
            $this->validatesId('serie');
    }

    protected function turmasPorAno($escolaId, $ano)
    {
        $anoLetivo = new clsPmieducarEscolaAnoLetivo();
        $anoLetivo->ref_cod_escola = $escolaId;
        $anoLetivo->ano = $ano;
        $anoLetivo = $anoLetivo->detalhe();

        return ($anoLetivo['turmas_por_ano'] == 1);
    }

    protected function getTurmas()
    {
        if ($this->canGetTurmas()) {
            $userId = $this->getSession()->id_pessoa;
            $instituicaoId = $this->getRequest()->instituicao_id;
            $escolaId = $this->getRequest()->escola_id;
            $serieId = $this->getRequest()->serie_id;
            $ano = $this->getRequest()->ano;
            $anoEmAndamento = $this->getRequest()->ano_em_andamento;

            $isProfessor = Portabilis_Business_Professor::isProfessor($instituicaoId, $userId);

            if ($isProfessor) {
                $turmas = Portabilis_Business_Professor::turmasAlocado($instituicaoId, $escolaId, $serieId, $userId);
            } else {
                if (is_numeric($ano)) {
                    $sql = 'select cod_turma as id, nm_turma || \' - \' || COALESCE(ano::varchar,\'SEM ANO\') as nome from pmieducar.turma where ref_ref_cod_escola = $1
                   and (ref_ref_cod_serie = $2 or ref_ref_cod_serie_mult = $2) and ativo = 1 and
                   visivel != \'f\' and turma.ano = $3 order by nm_turma asc';

                    $turmas = $this->fetchPreparedQuery($sql, [$escolaId, $serieId, $ano]);
                } else {
                    $sql = 'select cod_turma as id, nm_turma || \' - \' || COALESCE(ano::varchar,\'SEM ANO\') as nome from pmieducar.turma where ref_ref_cod_escola = $1
                   and (ref_ref_cod_serie = $2 or ref_ref_cod_serie_mult = $2) and ativo = 1 and
                   visivel != \'f\' order by nm_turma asc';

                    $turmas = $this->fetchPreparedQuery($sql, [$escolaId, $serieId]);
                }
            }

            // caso no ano letivo esteja definido para filtrar turmas por ano,
            // somente retorna as turmas do ano letivo.

            if ($ano && $this->turmasPorAno($escolaId, $ano)) {
                foreach ($turmas as $index => $t) {
                    $turma = new clsPmieducarTurma();
                    $turma->cod_turma = $t['id'];
                    $turma = $turma->detalhe();

                    if ($turma['ano'] != $ano) {
                        unset($turmas[$index]);
                    }
                }
            }

            if ($anoEmAndamento == 1) {
                foreach ($turmas as $index => $t) {
                    $turma = new clsPmieducarTurma();
                    $turma->cod_turma = $t['id'];
                    $turma = $turma->checaAnoLetivoEmAndamento();

                    if (!$turma) {
                        unset($turmas[$index]);
                    }
                }
            }

            $options = [];
            foreach ($turmas as $turma) {
                $options['__' . $turma['id']] = mb_strtoupper($turma['nome'], 'UTF-8');
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'turmas')) {
            $this->appendResponse($this->getTurmas());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
