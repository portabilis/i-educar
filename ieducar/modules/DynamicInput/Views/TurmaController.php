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
                            t.cod_turma AS id,
                            t.nm_turma || \' - \' || COALESCE(ano::varchar,\'SEM ANO\') AS nome
                        FROM pmieducar.turma AS t
                        LEFT JOIN pmieducar.turma_serie AS ts ON ts.turma_id = t.cod_turma
                        WHERE t.ref_ref_cod_escola = $1
                        AND (
                            t.ref_ref_cod_serie = $2
                            OR ts.serie_id = $2
                        )
                        AND t.ativo = 1
                        AND t.visivel != \'f\'
                        AND t.ano = $3
                        ORDER BY t.nm_turma ASC
                    ';

                    $turmas = $this->fetchPreparedQuery($sql, [$escolaId, $serieId, $ano]);
                } else {
                    $sql = '
                        SELECT
                            t.cod_turma AS id,
                            t.nm_turma || \' - \' || COALESCE(ano::varchar,\'SEM ANO\') AS nome
                        FROM pmieducar.turma AS t
                        LEFT JOIN pmieducar.turma_serie AS ts ON ts.turma_id = t.cod_turma
                        WHERE t.ref_ref_cod_escola = $1
                        AND (
                            t.ref_ref_cod_serie = $2
                            OR ts.serie_id = $2
                        )
                        AND t.ativo = 1
                        AND t.visivel != \'f\'
                        ORDER BY t.nm_turma ASC
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

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'turmas')) {
            $this->appendResponse($this->getTurmas());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
