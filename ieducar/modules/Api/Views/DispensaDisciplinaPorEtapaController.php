<?php

require_once 'modules/Avaliacao/Model/NotaAlunoDataMapper.php';
require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';

class DispensaDisciplinaPorEtapaController extends ApiCoreController
{
    protected function canValidatesData()
    {
        return $this->validatesPresenceOf([
            'ref_cod_matricula',
            'componentecurricular',
            'etapas'
        ]);
    }

    public function existeNota()
    {
        $existeNota = false;
        $notasAlunoComponentes = [];

        if ($this->canValidatesData()) {
            $matricula_id = $this->getRequest()->ref_cod_matricula;
            $disciplinas = $this->getRequest()->componentecurricular;
            $disciplinas = explode(',', $disciplinas);
            $etapas = $this->getRequest()->etapas;
            $etapas = explode(',', $etapas);

            foreach ($disciplinas as $disciplina) {
                foreach ($etapas as $etapa) {
                    $notasAlunoComponentes[] = $this->buscaNotaComponentePorEtapa(
                        $matricula_id,
                        $disciplina,
                        $etapa
                    );
                }
            }
            $existeNota = count(array_shift($notasAlunoComponentes)) > 0;
        }
        return ['existe_nota' => $existeNota];
    }

    private function getNotaAlunoId($matriculaId)
    {
        $notaAlunoMapper = new Avaliacao_Model_NotaAlunoDataMapper();
        $notaAluno = $notaAlunoMapper->findAll([], ['matricula_id' => $matriculaId]);
        return $notaAluno[0]->id;
    }

    private function getNotaComponenteCurricular($matriculaId, $disciplinaId, $etapa)
    {
        $notaAluno = $this->getNotaAlunoId($matriculaId);

        $notaComponenteCurricularMapper = new Avaliacao_Model_NotaComponenteDataMapper();
        return $notaComponenteCurricularMapper->findAll([], [
            'nota_aluno_id' => $notaAluno,
            'componente_curricular_id' => $disciplinaId,
            'etapa' => $etapa
        ]);
    }

    public function buscaNotaComponentePorEtapa($matriculaId, $disciplinaId, $etapa)
    {
        return $this->getNotaComponenteCurricular($matriculaId, $disciplinaId, $etapa);
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'existe-nota')) {
            $this->appendResponse($this->existeNota());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
