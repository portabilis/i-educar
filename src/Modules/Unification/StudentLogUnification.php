<?php

namespace iEducar\Modules\Unification;

use App\Models\LogUnification;
use App\Models\Student;

class StudentLogUnification implements LogUnificationTypeInterface
{
    public function getMainPersonName(LogUnification $logUnification)
    {
        return $logUnification->main->individual->real_name;
    }

    public function getDuplicatedPeopleName(LogUnification $logUnification)
    {
        $studentIds = $logUnification->duplicates_id;

        $students = Student::query()
            ->with('individual')
            ->whereIn('id', $studentIds)
            ->withTrashed()
            ->get();

        $arrayNames = [];
        foreach ($students as $student) {
            $arrayNames[] = $student->individual->real_name;
        }

        return $arrayNames;
    }

    public static function getType()
    {
        return Student::class;
    }
}