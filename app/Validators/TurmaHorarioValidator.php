<?php

namespace App\Validators;

class TurmaHorarioValidator
{
    private const TIME_REGEX = '/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/';

    private array $messages = [];

    public function isValid(
        ?string $horaInicial = null,
        ?string $horaFinal = null,
        ?string $horaInicioIntervalo = null,
        ?string $horaFimIntervalo = null
    ): bool {
        $this->messages = [];

        $horaInicial = $this->sanitize($horaInicial);
        $horaFinal = $this->sanitize($horaFinal);
        $horaInicioIntervalo = $this->sanitize($horaInicioIntervalo);
        $horaFimIntervalo = $this->sanitize($horaFimIntervalo);

        $horarios = [
            $horaInicial,
            $horaFinal,
            $horaInicioIntervalo,
            $horaFimIntervalo,
        ];

        foreach ($horarios as $horario) {
            if ($horario !== null && !preg_match(self::TIME_REGEX, $horario)) {
                $this->messages[] = 'O formato de horário informado é inválido. Utilize HH:MM.';

                return false;
            }
        }

        if (($horaInicial === null && $horaFinal !== null) || ($horaInicial !== null && $horaFinal === null)) {
            $this->messages[] = 'A hora inicial e a hora final da turma devem ser informadas juntas.';
        }

        if ($horaInicial !== null && $horaFinal !== null && $horaInicial >= $horaFinal) {
            $this->messages[] = 'A hora inicial deve ser anterior à hora final da turma.';
        }

        $temInicioIntervalo = $horaInicioIntervalo !== null;
        $temFimIntervalo = $horaFimIntervalo !== null;

        if ($temInicioIntervalo xor $temFimIntervalo) {
            $this->messages[] = 'Ambos os horários de início e término do intervalo devem ser informados.';
        }

        if ($temInicioIntervalo && $temFimIntervalo) {
            if ($horaInicioIntervalo >= $horaFimIntervalo) {
                $this->messages[] = 'A hora de início do intervalo deve ser anterior à hora de término do intervalo.';
            }

            if ($horaInicial !== null && $horaFinal !== null) {
                if ($horaInicioIntervalo < $horaInicial || $horaFimIntervalo > $horaFinal) {
                    $this->messages[] = 'O intervalo deve estar compreendido entre a hora inicial e a hora final da turma.';
                }
            }
        }

        return empty($this->messages);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getFirstMessage(): ?string
    {
        return $this->messages[0] ?? null;
    }

    private function sanitize(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $trimmed = trim($valor);

        return $trimmed === '' ? null : $trimmed;
    }
}
