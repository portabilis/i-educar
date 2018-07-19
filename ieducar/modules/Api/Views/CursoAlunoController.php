<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Utils/Database.php';

class CursoAlunoController extends ApiCoreController
{

    public function canGetCursoDoAluno()
    {
        return $this->validatesPresenceOf('aluno_id');
    }

    public function getCursoDoAluno()
    {
        if ($this->canGetCursoDoAluno()) {
            $alunoId = $this->getRequest()->aluno_id;
            $sql = 'SELECT \'\'\'\' || (nm_curso ) || \'\'\'\' AS id, (nm_curso ) AS nome FROM pmieducar.historico_escolar WHERE ref_cod_aluno = $1';
            $cursos = $this->fetchPreparedQuery($sql, [$alunoId]);
            $attrs = ['id', 'nome'];
            $cursos = Portabilis_Array_Utils::filterSet($cursos, $attrs);
            $options = [];

            foreach ($cursos as $curso) {
                $options[$curso['id']] = Portabilis_String_Utils::toUtf8($curso['nome']);
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'curso-aluno')) {
            $this->appendResponse($this->getCursoDoAluno());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
