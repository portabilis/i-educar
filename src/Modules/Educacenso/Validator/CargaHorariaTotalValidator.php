<?php

namespace iEducar\Modules\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\EtapaEnsino;

class CargaHorariaTotalValidator implements EducacensoValidator
{
    private const CURSO_TECNICO = 1;

    private const QUALIFICACAO_PROFISSIONAL = 2;

    private const CARGA_HORARIA_MAXIMA = 9999;

    private $message = '';

    private $motivoMinimo = '';

    private $iftpAtivo;

    private $fgbAtivo;

    private $etapaEducacenso;

    private $cargaHorariaTotal;

    private $tipoCursoIntinerario;

    private $codCursoProfissional;

    private $codCursoProfissionalIntinerario;

    public function __construct(
        $iftpAtivo,
        $fgbAtivo,
        $etapaEducacenso,
        $cargaHorariaTotal,
        $tipoCursoIntinerario,
        $codCursoProfissional,
        $codCursoProfissionalIntinerario
    ) {
        $this->iftpAtivo = $iftpAtivo;
        $this->fgbAtivo = $fgbAtivo;
        $this->etapaEducacenso = $etapaEducacenso;
        $this->cargaHorariaTotal = $cargaHorariaTotal;
        $this->tipoCursoIntinerario = $tipoCursoIntinerario;
        $this->codCursoProfissional = $codCursoProfissional;
        $this->codCursoProfissionalIntinerario = $codCursoProfissionalIntinerario;
    }

    public function isValid(): bool
    {
        if (!$this->permitePreenchimento()) {
            return true;
        }

        $carga = $this->cargaHorariaTotal;

        // Campo opcional: sem valor não bloqueia
        if ($carga === null || $carga === '') {
            return true;
        }

        $carga = (int) $carga;

        if ($carga <= 0 || $carga > self::CARGA_HORARIA_MAXIMA) {
            $this->message = 'deve ser um número maior que zero, com no máximo 4 dígitos.';

            return false;
        }

        $cargaMinima = $this->resolverCargaMinima();

        if ($cargaMinima > 0 && $carga < $cargaMinima) {
            $this->message = "deve ser de no mínimo {$cargaMinima} horas {$this->motivoMinimo}.";

            return false;
        }

        return true;
    }

    private function permitePreenchimento(): bool
    {
        return $this->iftpAtivo
            || in_array((int) $this->etapaEducacenso, EtapaEnsino::ETAPAS_CARGA_HORARIA_TURMA, true);
    }

    /**
     * Resolve a carga horária mínima exigida. A etapa de ensino tem precedência
     * sobre o itinerário; retorna 0 quando nenhum mínimo se aplica.
     */
    private function resolverCargaMinima(): int
    {
        $etapa = (int) $this->etapaEducacenso;

        if (in_array($etapa, EtapaEnsino::ETAPAS_CARGA_HORARIA_POR_CURSO, true)) {
            $this->motivoMinimo = 'para o curso informado';

            return $this->cargaHorariaMinimaDoCurso($this->codCursoProfissional);
        }

        if (array_key_exists($etapa, EtapaEnsino::CARGA_HORARIA_MINIMA_FIXA_POR_ETAPA)) {
            $this->motivoMinimo = 'para a etapa de ensino informada';

            return EtapaEnsino::CARGA_HORARIA_MINIMA_FIXA_POR_ETAPA[$etapa];
        }

        if ($this->iftpAtivo) {
            // Censo 2026 (Anexo 8): turma de ensino médio (etapas 25 a 29) com formação
            // geral básica e itinerário formativo tem a carga do curso completo (formação
            // geral básica mais o itinerário), independente do tipo do curso do itinerário.
            if ($this->fgbAtivo && in_array($etapa, EtapaEnsino::ETAPAS_ENSINO_MEDIO_COM_FORMACAO_GERAL_BASICA, true)) {
                $this->motivoMinimo = 'para o itinerário de formação técnica e profissional com formação geral básica';

                return EtapaEnsino::CARGA_HORARIA_MINIMA_TECNICO_FGB;
            }

            $tipoCurso = (int) $this->tipoCursoIntinerario;

            if ($tipoCurso === self::QUALIFICACAO_PROFISSIONAL) {
                $this->motivoMinimo = 'para qualificação profissional técnica';

                return EtapaEnsino::CARGA_HORARIA_MINIMA_QUALIFICACAO;
            }

            if ($tipoCurso === self::CURSO_TECNICO) {
                $this->motivoMinimo = 'para o curso técnico informado';

                return $this->cargaHorariaMinimaDoCurso($this->codCursoProfissionalIntinerario);
            }
        }

        return 0;
    }

    private function cargaHorariaMinimaDoCurso($codigoCurso): int
    {
        $cursos = loadJson(base_path('ieducar/intranet/educacenso_json/cursos_carga_horaria_minima.json'));

        return (int) ($cursos[(int) $codigoCurso]['carga_minima'] ?? 0);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
