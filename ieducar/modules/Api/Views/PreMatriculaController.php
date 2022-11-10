<?php

use App\Models\City;
use iEducar\Modules\Addressing\LegacyAddressingFields;
use Illuminate\Support\Str;

class PreMatriculaController extends ApiCoreController
{
    use LegacyAddressingFields;

    protected function canCancelarPreMatricula()
    {
        return $this->validatesExistenceOf('matricula', $this->getRequest()->matricula_id);
    }

    protected function cancelarPreMatricula()
    {
        if ($this->canCancelarPreMatricula()) {
            $matriculaId = $this->getRequest()->matricula_id;

            $alunoId = Portabilis_Utils_Database::selectField('SELECT ref_cod_aluno FROM pmieducar.matricula WHERE cod_matricula = $1', [$matriculaId]);
            $pessoaId = Portabilis_Utils_Database::selectField('SELECT ref_idpes FROM pmieducar.aluno WHERE cod_aluno = $1', [$alunoId]);
            $pessoaMaeId = Portabilis_Utils_Database::selectField('SELECT idpes_mae FROM cadastro.fisica WHERE idpes = $1', [$pessoaId]);
            $pessoaRespId = Portabilis_Utils_Database::selectField('SELECT idpes_responsavel FROM cadastro.fisica WHERE idpes = $1', [$pessoaId]);

            if (is_numeric($matriculaId)) {
                $this->fetchPreparedQuery('DELETE FROM pmieducar.matricula_turma WHERE ref_cod_matricula = $1', [$matriculaId]);
                $this->fetchPreparedQuery('DELETE FROM pmieducar.matricula WHERE cod_matricula = $1', [$matriculaId]);
            }

            if (is_numeric($alunoId)) {
                $this->fetchPreparedQuery('DELETE FROM pmieducar.aluno WHERE cod_aluno = $1', $alunoId);
            }

            if (is_numeric($pessoaId)) {
                $this->fetchPreparedQuery('DELETE FROM cadastro.fisica WHERE idpes = $1', $pessoaId);
                $this->fetchPreparedQuery('DELETE FROM cadastro.pessoa WHERE idpes = $1', $pessoaId);
            }

            if (is_numeric($pessoaMaeId)) {
                $this->fetchPreparedQuery('DELETE FROM cadastro.fisica WHERE idpes = $1', $pessoaMaeId);
                $this->fetchPreparedQuery('DELETE FROM cadastro.pessoa WHERE idpes = $1', $pessoaMaeId);
            }

            if (is_numeric($pessoaRespId)) {
                $this->fetchPreparedQuery('DELETE FROM cadastro.fisica WHERE idpes = $1', $pessoaRespId);
                $this->fetchPreparedQuery('DELETE FROM cadastro.pessoa WHERE idpes = $1', $pessoaRespId);
            }
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('post', 'cancelar-pre-matricula')) {
            $this->appendResponse($this->cancelarPreMatricula());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
