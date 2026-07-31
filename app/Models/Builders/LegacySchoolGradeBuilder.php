<?php

namespace App\Models\Builders;

use Illuminate\Support\Facades\DB;

class LegacySchoolGradeBuilder extends LegacyBuilder
{
    /**
     * Filtra os vínculos ativos
     */
    public function active(): self
    {
        return $this->where('escola_serie.ativo', 1);
    }

    /**
     * Filtra por escola
     */
    public function whereSchool(int $school): self
    {
        return $this->where('escola_serie.ref_cod_escola', $school);
    }

    /**
     * Filtra por série
     */
    public function whereGrade(int $grade): self
    {
        return $this->where('escola_serie.ref_cod_serie', $grade);
    }

    /**
     * Filtra por curso da série. Requer joinGradeCourse()
     */
    public function whereCourse(int $course): self
    {
        return $this->where('serie.ref_cod_curso', $course);
    }

    /**
     * Filtra por instituição do curso. Requer joinGradeCourse()
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('curso.ref_cod_instituicao', $institution);
    }

    /**
     * Filtra pelas escolas em que o usuário tem acesso
     */
    public function whereUser(int $user): self
    {
        return $this->whereExists(function ($query) use ($user) {
            $query->select(DB::raw('1'))
                ->from('pmieducar.escola_usuario')
                ->whereColumn('escola_usuario.ref_cod_escola', 'escola_serie.ref_cod_escola')
                ->where('escola_usuario.ref_cod_usuario', $user);
        });
    }

    /**
     * Junta série e curso, expondo nome da série, curso e instituição, apenas para séries ativas
     */
    public function joinGradeCourse(): self
    {
        return $this->select('escola_serie.*')
            ->join('pmieducar.serie', function ($j) {
                $j->on('serie.cod_serie', 'escola_serie.ref_cod_serie');
                $j->where('serie.ativo', 1);
            })
            ->join('pmieducar.curso', 'curso.cod_curso', 'serie.ref_cod_curso')
            ->addSelect('serie.nm_serie', 'serie.ref_cod_curso', 'curso.ref_cod_instituicao');
    }
}
