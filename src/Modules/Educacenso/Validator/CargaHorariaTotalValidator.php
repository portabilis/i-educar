<?php

namespace iEducar\Modules\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\EtapaEnsino;

class CargaHorariaTotalValidator implements EducacensoValidator
{
    private const CURSO_TECNICO = 1;

    private const QUALIFICACAO_PROFISSIONAL = 2;

    private $message = '';

    private $iftpAtivo;

    private $etapaEducacenso;

    private $cargaHorariaTotal;

    private $tipoCursoIntinerario;

    private $codCursoProfissionalIntinerario;

    public function __construct(
        $iftpAtivo,
        $etapaEducacenso,
        $cargaHorariaTotal,
        $tipoCursoIntinerario,
        $codCursoProfissionalIntinerario
    ) {
        $this->iftpAtivo = $iftpAtivo;
        $this->etapaEducacenso = $etapaEducacenso;
        $this->cargaHorariaTotal = $cargaHorariaTotal;
        $this->tipoCursoIntinerario = $tipoCursoIntinerario;
        $this->codCursoProfissionalIntinerario = $codCursoProfissionalIntinerario;
    }

    public function isValid(): bool
    {
        if (!$this->permitePreenchimento()) {
            return true;
        }

        $carga = $this->cargaHorariaTotal;

        // Censo 2026: carga horária total é opcional; sem valor não bloqueia
        if ($carga === null || $carga === '') {
            return true;
        }

        $carga = (int) $carga;

        if ($carga <= 0 || $carga > 9999) {
            $this->message = 'a carga horária total deve ser um número maior que zero, com no máximo 4 dígitos.';

            return false;
        }

        $cargaMinimaEtapa = EtapaEnsino::CARGA_HORARIA_MINIMA_POR_ETAPA[(int) $this->etapaEducacenso] ?? 0;

        if ($cargaMinimaEtapa > 0 && $carga < $cargaMinimaEtapa) {
            $this->message = "a carga horária total deve ser de no mínimo {$cargaMinimaEtapa} horas para a etapa de ensino informada.";

            return false;
        }

        $tipoCurso = (int) $this->tipoCursoIntinerario;

        if ($tipoCurso === self::CURSO_TECNICO) {
            $cargaMinima = $this->cargaHorariaMinimaDoCurso();

            if ($cargaMinima > 0 && $carga < $cargaMinima && $carga <= 2000) {
                $this->message = "a carga horária total deve ser maior ou igual à carga horária mínima do curso ({$cargaMinima} horas) ou superior a 2000 horas.";

                return false;
            }
        }

        if ($tipoCurso === self::QUALIFICACAO_PROFISSIONAL && ($carga < 160 || $carga > 800)) {
            $this->message = 'a carga horária total deve estar entre 160 e 800 horas para qualificação profissional técnica.';

            return false;
        }

        return true;
    }

    private function permitePreenchimento(): bool
    {
        return $this->iftpAtivo
            || in_array((int) $this->etapaEducacenso, EtapaEnsino::ETAPAS_PERMITEM_CARGA_HORARIA, true);
    }

    private function cargaHorariaMinimaDoCurso(): int
    {
        $cursos = loadJson(base_path('ieducar/intranet/educacenso_json/cursos_carga_horaria_minima.json'));
        $cursoSelecionado = (int) $this->codCursoProfissionalIntinerario;

        return (int) ($cursos[$cursoSelecionado]['carga_minima'] ?? 0);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
