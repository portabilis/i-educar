<?php

namespace App\Rules;

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolGrade;
use Illuminate\Contracts\Validation\Rule;

class CanCreateSchoolClass implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $escolaId = $value->ref_ref_cod_escola;
        $serieId = $value->ref_ref_cod_serie;
        $turnoId = $value->turma_turno_id;
        $anoLetivo = $value->ano;
        $isCreate = empty($value->cod_turma);

        $schoolGrade = LegacySchoolGrade::query()
            ->where('ref_cod_escola', $escolaId)
            ->where('ref_cod_serie', $serieId)
            ->first();

        if ($isCreate && $schoolGrade && $schoolGrade->bloquear_cadastro_turma_para_serie_com_vagas == 1) {
            $schoolClasses = LegacySchoolClass::query()
                ->where('ref_ref_cod_serie', $serieId)
                ->where('ref_ref_cod_escola', $escolaId)
                ->where('ativo', 1)
                ->where('turma_turno_id', $turnoId)
                ->where('ano', $anoLetivo)
                ->whereExists(function ($builder) use ($anoLetivo, $escolaId) {
                    $builder->from('pmieducar.escola_ano_letivo')
                        ->where('andamento', 1)
                        ->where('ativo', 1)
                        ->where('ref_cod_escola', $escolaId)
                        ->where('ano', $anoLetivo)
                        ->get();
                })
                ->get();

            foreach ($schoolClasses as $schoolClass) {
                $countMatriculas = $schoolClass->getTotalEnrolled();
                $maxAlunos = $schoolClass->max_aluno;
                if (($maxAlunos - $countMatriculas) > 0) {
                    $vagas = $schoolClass->max_aluno - $countMatriculas;
                    $this->message = "Não é possivel cadastrar turmas, pois ainda existem {$vagas} vagas em aberto na turma '{$schoolClass->nm_turma}' desta serie e turno. Tal limitação ocorre devido definição feita para estae scola e série.";

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
