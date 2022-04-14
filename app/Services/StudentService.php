<?php

namespace App\Services;

use App\Models\Student;

class StudentService
{
    /**
     * Verifica se o aluno tem alguma matricula em andamento
     *
     * @param int      $studentId
     * @param int|null $levelId
     *
     * @return boolean
     */
    public function hasInProgressRegistration($studentId, $levelId = null)
    {
        $student = Student::find($studentId);

        if (!$student) {
            return false;
        }

        $query = $student->registrations()
            ->where('status', 3)
            ->where('is_last_registration', 1);

        if ($levelId) {
            $query->where('level_id', $levelId);
        }

        return $query->orderBy('year', 'desc')
            ->exists();
    }
}
