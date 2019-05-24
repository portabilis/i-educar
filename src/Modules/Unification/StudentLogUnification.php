<?php

namespace iEducar\Modules\Unification;

use App\Models\LogUnification;
use App\Models\Student;

class StudentLogUnification implements LogUnificationTypeInterface
{
    /**
     * @param LogUnification $logUnification
     * @return string
     */
    public function getMainPersonName(LogUnification $logUnification)
    {
        return $logUnification->main->individual->real_name;
    }

    /**
     * @param LogUnification $logUnification
     * @return array
     */
    public function getDuplicatedPeopleName(LogUnification $logUnification)
    {
        $studentIds = $logUnification->duplicates_id;

        $students = Student::query()
            ->with('individual')
            ->whereIn('id', $studentIds)
            ->withTrashed()
            ->get()
            ->pluck('individual.real_name')
            ->toArray();

        return $students;
    }

    /**
     * @return string
     */
    public static function getType()
    {
        return Student::class;
    }
}