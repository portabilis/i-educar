<?php

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
            $userId = \Illuminate\Support\Facades\Auth::id();
            $instituicaoId = $this->getRequest()->instituicao_id;
            $escolaId = $this->getRequest()->escola_id;
            $serieId = $this->getRequest()->serie_id;
            $ano = $this->getRequest()->ano;
            $anoEmAndamento = $this->getRequest()->ano_em_andamento;

            $isOnlyProfessor = Portabilis_Business_Professor::isOnlyProfessor($instituicaoId, $userId);

            if ($isOnlyProfessor) {
                $turmas = Portabilis_Business_Professor::turmasAlocado($instituicaoId, $escolaId, $serieId, $userId);
            } else {
                if (is_numeric($ano)) {
                    $sql = '
                        SELECT
                            cod_turma AS id,
                            nm_turma || \' - \' || COALESCE(ano::varchar,\'SEM ANO\') AS nome
                        FROM pmieducar.turma
                        WHERE ref_ref_cod_escola = $1
                        AND (
                            ref_ref_cod_serie = $2
                            OR ref_ref_cod_serie_mult = $2
                        )
                        AND ativo = 1
                        AND visivel != \'f\'
                        AND turma.ano = $3
                        ORDER BY nm_turma asc
                    ';

                    $turmas = $this->fetchPreparedQuery($sql, [$escolaId, $serieId, $ano]);
                } else {
                    $sql = '
                        SELECT
                            cod_turma AS id,
                            nm_turma || \' - \' || COALESCE(ano::varchar,\'SEM ANO\') AS nome
                        FROM pmieducar.turma
                        WHERE ref_ref_cod_escola = $1
                        AND (
                            ref_ref_cod_serie = $2
                            OR ref_ref_cod_serie_mult = $2
                        ) and ativo = 1
                        AND visivel != \'f\'
                        ORDER BY nm_turma asc
                    ';

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

    public function getDetalhe()
    {
        $turmaId = $this->getRequest()->turma_id;

        if (is_numeric($turmaId)) {
            $obj = new clsPmieducarTurma($turmaId);
            $turma = $obj->detalheWithCurso();

            return $turma;
        }

        return false;
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'turmas')) {
            $this->appendResponse($this->getTurmas());
        } else if ($this->isRequestFor('get', 'detalhe')) {
            $this->appendResponse($this->getDetalhe());
            $this->appendResponse($this->getDetalhe());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
