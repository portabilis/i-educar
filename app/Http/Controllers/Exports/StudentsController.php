<?php

namespace App\Http\Controllers\Exports;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentsExport as Request;
use App\Models\Student;
use App\Models\RegistrationStatus;

class StudentsController extends Controller
{
    public function export(Request $request)
    {
        $query = Student::select();

        if ($id = $request->query('cod_aluno')) {
            $query->where('id', $id);
        }

        if ($inepCode = $request->query('cod_inep')) {
            $query->whereHas('census', function ($query) use ($inepCode) {
                $query->where('inep_code', $inepCode);
            });
        }

        if ($registryCode = $request->query('aluno_estado_id')) {
            $query->where('registry_code', $registryCode);
        }

        $birthdate = $request->query('data_nascimento');
        $studentName = $request->query('nome_aluno');
        $fatherName = $request->query('nome_pai');
        $motherName = $request->query('nome_mae');
        $guardianName = $request->query('nome_responsavel');

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

        $year = $request->query('ano');
        $schoolId = $request->query('ref_cod_escola');
        $courseId = $request->query('ref_cod_curso');
        $levelId = $request->query('ref_cod_serie');

        if (
            $year ||
            $schoolId ||
            $courseId ||
            $levelId
        ) {
            $query->whereHas('registrations', function ($query) use ($year, $schoolId, $courseId, $levelId) {
                $query->whereNull('deleted_at')
                    ->where('status', RegistrationStatus::ONGOING);

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
            });
        }

        $students = $query->get();

        dd($students);

        die();
    }
}
