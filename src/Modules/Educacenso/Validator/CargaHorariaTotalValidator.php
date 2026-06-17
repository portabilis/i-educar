<?php

namespace iEducar\Modules\Educacenso\Validator;

class CargaHorariaTotalValidator implements EducacensoValidator
{
    private const CURSO_TECNICO = 1;

    private const QUALIFICACAO_PROFISSIONAL = 2;

    private $message = '';

    private $iftpAtivo;

    private $cargaHorariaTotal;

    private $tipoCursoIntinerario;

    public function __construct(
        $iftpAtivo,
        $cargaHorariaTotal,
        $tipoCursoIntinerario
    ) {
        $this->iftpAtivo = $iftpAtivo;
        $this->cargaHorariaTotal = $cargaHorariaTotal;
        $this->tipoCursoIntinerario = $tipoCursoIntinerario;
    }

    public function isValid(): bool
    {
        if (!$this->iftpAtivo) {
            return true;
        }

        $carga = $this->cargaHorariaTotal;

        if ($carga === null || $carga === '') {
            $this->message = 'O campo: <b>Carga horária total do curso (em horas)</b> é obrigatório quando o campo: <b>Organização curricular da turma</b> incluir: <b>Itinerário de formação técnica e profissional</b>.';

            return false;
        }

        $carga = (int) $carga;

        $tipoCurso = (int) $this->tipoCursoIntinerario;

        if ($tipoCurso === self::CURSO_TECNICO && $carga < 2000) {
            $this->message = 'O campo: <b>Carga horária total do curso (em horas)</b> deve ser maior ou igual a 2000 horas para cursos técnicos.';

            return false;
        }

        if ($tipoCurso === self::QUALIFICACAO_PROFISSIONAL && ($carga < 160 || $carga > 800)) {
            $this->message = 'O campo: <b>Carga horária total do curso (em horas)</b> deve estar entre 160 e 800 horas para qualificação profissional técnica.';

            return false;
        }

        return true;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
