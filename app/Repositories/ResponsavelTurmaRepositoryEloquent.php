<?php
 
namespace App\Repositories;

use App\Models\RegistrationStatus;
use App\Models\ResponsavelTurma;
use iEducar\Support\Repositories\ResponsavelTurmaRepository;
use Illuminate\Database\Eloquent\Collection;

class ResponsavelTurmaRepositoryEloquent implements ResponsavelTurmaRepository
{
    public function list(array $params): Collection
    {
        $query = ResponsavelTurma::select();

        if ($id = $this->param($params, 'id')) {
            $query->where('id', $id);
        }

        if ($inepCode = $this->param($params, 'inep_code')) {
            $query->whereHas('census', function ($query) use ($inepCode) {
                $query->where('inep_code', $inepCode);
            });
        }

        if ($registryCode = $this->param($params, 'registry_code')) {
            $query->where('registry_code', $registryCode);
        }

        $birthdate = $this->param($params, 'birthdate');
        $studentName = $this->param($params, 'student_name');
        $fatherName = $this->param($params, 'father_name');
        $motherName = $this->param($params, 'mother_name');
        $guardianName = $this->param($params, 'guardian_name');

        if (
            $birthdate ||
            $studentName ||
            $fatherName ||
            $motherName ||
            $guardianName
        ) {
            $query->whereHas('individual', function ($query) use ($birthdate, $studentName, $fatherName, $motherName, $guardianName) {
                if ($birthdate) {
                    list($day, $month, $year) = explode('/', $birthdate);
                    $birthdate = sprintf('%d-%d-%d', $year, $month, $day);

                    $query->where('birthdate', $birthdate);
                }

                if ($studentName) {
                    $query->whereHas('person', function ($query) use ($studentName) {
                        $query->whereRaw('unaccent(name) ILIKE unaccent(\'%' . $studentName . '%\')');
                    });
                }

                if ($motherName) {
                    $query->whereHas('mother', function ($query) use ($motherName) {
                        $query->whereHas('person', function ($query) use ($motherName) {
                            $query->whereRaw('unaccent(name) ILIKE unaccent(\'%' . $motherName . '%\')');
                        });
                    });
                }

                if ($fatherName) {
                    $query->whereHas('father', function ($query) use ($fatherName) {
                        $query->whereHas('person', function ($query) use ($fatherName) {
                            $query->whereRaw('unaccent(name) ILIKE unaccent(\'%' . $fatherName . '%\')');
                        });
                    });
                }

                if ($guardianName) {
                    $query->whereHas('guardian', function ($query) use ($guardianName) {
                        $query->whereHas('person', function ($query) use ($guardianName) {
                            $query->whereRaw('unaccent(name) ILIKE unaccent(\'%' . $guardianName . '%\')');
                        });
                    });
                }
            });
        }
 
        $year = $this->param($params, 'year');
        $schoolId = $this->param($params, 'school_id');
        $courseId = $this->param($params, 'course_id');
        $levelId = $this->param($params, 'level_id');
        $school_class_id = $this->param($params, 'school_class_id');

        if (
            $year ||
            $schoolId ||
            $courseId ||
            $levelId ||
            $school_class_id
        ) {
           

                if ($year) {
                    $query->where('year', $year);
                }

                if ($schoolId) {
                    $query->where('school_id', $schoolId);
                }

                if ($courseId) {
                    $query->where('course_id', $courseId);
                }

                if ($levelId) {
                    $query->where('level_id', $levelId);
                }
                if ($school_class_id) {
                    $query->leftJoin('matricula_turma', 'matricula_turma.ref_cod_matricula', '=', 'id_matricula')->where('matricula_turma.ref_cod_turma', $school_class_id);
                }
           
        }
       

        return $query->get();
    }

    protected function param(array $params, string $key, $default = null)
    {
        return $params[$key] ?? $default;
    }
}
