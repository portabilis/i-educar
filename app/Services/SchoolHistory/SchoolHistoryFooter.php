<?php

namespace App\Services\SchoolHistory;

class SchoolHistoryFooter
{
    private $footerDataKeys = [
        'ano_1serie',
        'ano_2serie',
        'ano_3serie',
        'ano_4serie',
        'ano_5serie',
        'ano_6serie',
        'ano_7serie',
        'ano_8serie',
        'ano_9serie',
        'escola_1serie',
        'escola_2serie',
        'escola_3serie',
        'escola_4serie',
        'escola_5serie',
        'escola_6serie',
        'escola_7serie',
        'escola_8serie',
        'escola_9serie',
        'escola_cidade_1serie',
        'escola_cidade_2serie',
        'escola_cidade_3serie',
        'escola_cidade_4serie',
        'escola_cidade_5serie',
        'escola_cidade_6serie',
        'escola_cidade_7serie',
        'escola_cidade_8serie',
        'escola_cidade_9serie',
        'escola_uf_1serie',
        'escola_uf_2serie',
        'escola_uf_3serie',
        'escola_uf_4serie',
        'escola_uf_5serie',
        'escola_uf_6serie',
        'escola_uf_7serie',
        'escola_uf_8serie',
        'escola_uf_9serie',
        'status_serie1',
        'status_serie2',
        'status_serie3',
        'status_serie4',
        'status_serie5',
        'status_serie6',
        'status_serie7',
        'status_serie8',
        'status_serie9',
        'carga_horaria1',
        'carga_horaria2',
        'carga_horaria3',
        'carga_horaria4',
        'carga_horaria5',
        'carga_horaria6',
        'carga_horaria7',
        'carga_horaria8',
        'carga_horaria9',
        'freq1',
        'freq2',
        'freq3',
        'freq4',
        'freq5',
        'freq6',
        'freq7',
        'freq8',
        'freq9',
    ];

    public function __construct($studentDisciplines)
    {
        $this->studentDisciplines = $studentDisciplines;
    }

    public function insertFooterDataInLastStudentDiscipline()
    {
        foreach ($this->studentDisciplines as $key => $studentDiscipline) {
            $this->setFooterDataByStudentDiscipline($key, $studentDiscipline);
        }

        return $this->studentDisciplines;
    }

    private function setFooterDataByStudentDiscipline($studentId, $studentDisciplines)
    {
        $lastDiscipline = array_key_last($studentDisciplines);
        foreach ($studentDisciplines as $discipline) {
            $this->setFooterDataInLastDiscipline($discipline, $lastDiscipline, $studentId);
        }
    }

    private function setFooterDataInLastDiscipline($discipline, $lastDiscipline, $studentId)
    {
        foreach ($this->footerDataKeys as $dataFooter) {
            if (property_exists($discipline, $dataFooter)) {
                $this->studentDisciplines[$studentId][$lastDiscipline]->$dataFooter = $discipline->$dataFooter;
            }
        }
    }
}
