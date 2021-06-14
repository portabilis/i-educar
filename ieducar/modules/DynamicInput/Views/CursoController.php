<?php

class CursoController extends ApiCoreController
{
    protected function canGetCursos()
    {
        return
            $this->validatesId('instituicao') &&
            $this->validatesId('escola');
    }

    protected function getCursos()
    {
        if ($this->canGetCursos()) {
            $userId = \Illuminate\Support\Facades\Auth::id();
            $instituicaoId = $this->getRequest()->instituicao_id;
            $escolaId = $this->getRequest()->escola_id;
            $ano = $this->getRequest()->ano;

            $isOnlyProfessor = Portabilis_Business_Professor::isOnlyProfessor($instituicaoId, $userId);

            if ($isOnlyProfessor) {
                $cursos = Portabilis_Business_Professor::cursosAlocado($instituicaoId, $escolaId, $userId);
            } else {
                $params = [ $escolaId ];

                $sql = '
                    SELECT
                        c.cod_curso as id,
                        c.nm_curso as nome,
                        c.descricao
                    FROM
                        pmieducar.curso c,
                        pmieducar.escola_curso ec
                    WHERE ec.ref_cod_escola = $1
                    AND ec.ref_cod_curso = c.cod_curso
                    AND ec.ativo = 1 AND c.ativo = 1
                ';

                if (!empty($ano)) {
                    $params[] = $ano;
                    $sql .= ' AND $2 = ANY(ec.anos_letivos) ';
                }

                $sql .= ' ORDER BY c.nm_curso ASC ';

                $cursos = $this->fetchPreparedQuery($sql, $params);
            }

            $options = [];
            foreach ($cursos as $curso) {
                $nomeCurso = empty($curso['descricao']) ? $curso['nome'] : "{$curso['nome']} ({$curso['descricao']})";
                $options['__' . $curso['id']] = $nomeCurso;
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'cursos')) {
            $this->appendResponse($this->getCursos());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
