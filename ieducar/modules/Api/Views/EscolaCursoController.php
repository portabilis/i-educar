<?php

use App\Models\LegacySchoolCourse;

class EscolaCursoController extends ApiCoreController
{
    public function getAnosLetivos()
    {
        $anosLetivos = [];

        $codEscola = $this->getRequest()->cod_escola;
        $codCurso = $this->getRequest()->cod_curso;

        if (is_numeric($codEscola) && is_numeric($codCurso)) {
            $json = LegacySchoolCourse::query()
                ->whereSchool((int) $codEscola)
                ->whereCourse((int) $codCurso)
                ->selectRaw('array_to_json(anos_letivos) as anos_letivos')
                ->value('anos_letivos');

            $anosLetivos = $json !== null ? json_decode($json) : [];
        }

        return ['anos_letivos' => $anosLetivos];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'anos-letivos')) {
            $this->appendResponse($this->getAnosLetivos());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
