<?php

namespace App\Models\Builders;

class LegacyRegistrationBuilder extends LegacyBuilder
{
    public function transfer(): LegacyBuilder
    {
        return $this->active()
            ->currentYear()
            ->statusTransfer()
            ->modalityRegular()
            ->serviceTypeNotComplementaryActivity()
            ->whereHas('student', static function (
                $q
            ) {
                $q->whereDoesntHave('registrations', static function (
                    $q
                ) {
                    $q->currentYear();
                    $q->active();
                    $q->finalized();
                });
            })
            ->orderBy('cod_matricula', 'desc');
    }

    public function finalized(): LegacyBuilder
    {
        return $this->whereIn('aprovado', [
            1,
            2,
            12,
            13,
            14
        ]);
    }

    public function notFinalized(): LegacyBuilder
    {
        return $this->whereNotIn('aprovado', [
            1,
            2,
            12,
            13,
            14
        ]);
    }

    public function statusTransfer(): LegacyBuilder
    {
        return $this->where('aprovado', 4);
    }

    public function modalityRegular(): LegacyBuilder
    {
        return $this->whereHas('course', static fn (
            $q
        ) => $q->where('curso.modalidade_curso', 1));
    }

    public function serviceTypeNotComplementaryActivity(): LegacyBuilder
    {
        return $this->whereHas('schoolClasses', static fn (
            $q
        ) => $q->where('turma.tipo_atendimento', '<>', 4)->orWhereNull('turma.tipo_atendimento'));
    }
}
