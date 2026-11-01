<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckSchoolClassTimes implements Rule
{
    public string $message = '';

    /**
     * Pares de horários em que o início deve ser anterior ao fim.
     */
    private const PARES = [
        ['hora_inicial', 'hora_final', 'A hora final deve ser posterior à hora inicial.'],
        ['hora_inicio_intervalo', 'hora_fim_intervalo', 'A hora final do intervalo deve ser posterior à hora de início do intervalo.'],
        ['hora_inicial_matutino', 'hora_final_matutino', 'A hora final do turno matutino deve ser posterior à hora inicial do turno matutino.'],
        ['hora_inicial_vespertino', 'hora_final_vespertino', 'A hora final do turno vespertino deve ser posterior à hora inicial do turno vespertino.'],
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $params
     * @return bool
     */
    public function passes($attribute, $params)
    {
        foreach (self::PARES as [$inicio, $fim, $mensagem]) {
            $minutosInicio = $this->emMinutos($params->{$inicio} ?? null);
            $minutosFim = $this->emMinutos($params->{$fim} ?? null);

            if ($minutosInicio !== null && $minutosFim !== null && $minutosFim <= $minutosInicio) {
                $this->message = $mensagem;

                return false;
            }
        }

        return $this->validaIntervaloDentroDoHorario($params);
    }

    /**
     * O intervalo deve estar contido entre a hora inicial e a hora final da turma.
     */
    private function validaIntervaloDentroDoHorario($params): bool
    {
        $horaInicial = $this->emMinutos($params->hora_inicial ?? null);
        $horaFinal = $this->emMinutos($params->hora_final ?? null);

        if ($horaInicial === null || $horaFinal === null) {
            return true;
        }

        foreach (['hora_inicio_intervalo', 'hora_fim_intervalo'] as $campo) {
            $hora = $this->emMinutos($params->{$campo} ?? null);

            if ($hora !== null && ($hora < $horaInicial || $hora > $horaFinal)) {
                $this->message = 'O intervalo deve estar entre a hora inicial e a hora final da turma.';

                return false;
            }
        }

        return true;
    }

    /**
     * Converte uma hora no formato HH:MM ou HH:MM:SS em minutos desde a
     * meia-noite. Retorna null quando a hora não foi informada ou é inválida.
     */
    private function emMinutos($hora): ?int
    {
        if (empty($hora) || !preg_match('/^(\d{1,2}):(\d{2})/', (string) $hora, $partes)) {
            return null;
        }

        return ((int) $partes[1]) * 60 + (int) $partes[2];
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
