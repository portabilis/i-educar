<?php

namespace App\Services\SchoolHistory;

use App\Models\LegacySchoolHistory;
use App\Models\LegacyStudentBenefit;
use App\Services\SchoolHistory\Objects\SchoolHistory;

class SchoolHistoryService
{
    public function isEightYears($gradeType)
    {
        if ($gradeType == SchoolHistory::GRADE_SERIE) {
            return true;
        }

        return false;
    }

    public function getCertificationText($data)
    {
        $consideredStatus = [1, 12, 13, 3];
        $year = 0;
        $level = 0;
        $certificationText = '';

        foreach ($data as $history) {
            if (!$this->isValidLevelName($history['nm_serie'])) {
                continue;
            }

            if (!in_array($history['aprovado'], $consideredStatus)) {
                continue;
            }

            if ($history['ano'] < $year) {
                continue;
            }

            if ($this->getLevelByName($history['nm_serie']) < $level) {
                continue;
            }

            $year = $history['ano'];
            $level = $history['nm_serie'];

            $certificationText = $history['aprovado'] == 3 ? 'está cursando ' : 'concluiu ';

            if ($this->isConclusiveLevelByGrade($history['nm_serie'], $history['historico_grade_curso_id'])) {
                $certificationText .= 'o ENSINO FUNDAMENTAL';
            } else {
                $certificationText .= $history['historico_grade_curso_id'] == SchoolHistory::GRADE_SERIE ? 'a ' : 'o ';
                $certificationText .= $this->getLevelByName($history['nm_serie']);
                $certificationText .= $history['historico_grade_curso_id'] == SchoolHistory::GRADE_SERIE ? 'ª série' : 'º ano';
            }
        }

        return $certificationText;
    }

    public function isConclusiveLevelByGrade($levelName, $gradeType)
    {
        $level = $this->getLevelByName($levelName);
    
        if ($gradeType == SchoolHistory::GRADE_SERIE && $level == 8) {
            return true;
        }

        if ($gradeType == SchoolHistory::GRADE_ANO && $level == 9) {
            return true;
        }

        return false;
    }

    public function isValidLevelName($levelName)
    {
        if (is_numeric($this->getLevelByName($levelName))) {
            return true;
        }

        return false;
    }

    public function getLevelByName($levelName)
    {
        return substr($levelName, 0, 1);
    }

    private function getUsedSpaceByTemplate($templateName)
    {
        $usedSpaceByTemplate = [
            'portabilis_historico_escolar_9anos' => 363,
            'portabilis_historico_escolar' => 395,
            'portabilis_historico_escolar_series_anos' => 330,
        ];

        return $usedSpaceByTemplate[$templateName];
    }

    /**
     * Calcula e retorna quantidade de linhas necessários para que o campo
     * de observações preencha o restante da página em branco
     * 
     * @param $usedSpace soma das alturas das bands fixas do histórico
     * @param $numberOfDisciplines número de disciplinas geradas no histórico
     * @param $lineHeight altura da linha
     *
     * @return string
     */
    public function getBlankSpace($templateName, $numberOfDisciplines, $lineHeight)
    {
        $usedSpace = $this->getUsedSpaceByTemplate($templateName);
        $numberOfBlankLines = (($usedSpace - ($numberOfDisciplines * $lineHeight)) / $lineHeight);

        return str_repeat('<br>', (int)$numberOfBlankLines);
    }

    public function getAllObservationsByStudent($studentId)
    {
        $schoolHistory = LegacySchoolHistory::query()
            ->where('ref_cod_aluno', $studentId)
            ->where('ativo', 1)
            ->whereNotNull('observacao')
            ->where('observacao', '<>', '')
            ->orderBy('ano')
            ->pluck('observacao')
            ->toArray();

        return implode('<br>', $schoolHistory);
    }

    private function hasBolsaFamilia($studentId)
    {
        $studentBenefit = LegacyStudentBenefit::query()
            ->where('aluno_id', $studentId)
            ->whereHas('benefit', function ($benefitQuery) {
                $benefitQuery->whereRaw("TRIM(UNACCENT(aluno_beneficio.nm_beneficio)) ILIKE 'BOLSA FAMILIA'");
            })
            ->get()
            ->toArray();

        return count($studentBenefit) > 0;
    }

    public function getBolsaFamiliaText($studentId)
    {
        if ($this->hasBolsaFamilia($studentId)) {
            return '<b>Beneficiário do PBF:</b> Sim';
        }

        return '<b>Beneficiário do PBF:</b> Não';
    }
}
