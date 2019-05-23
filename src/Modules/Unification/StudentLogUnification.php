<?php

namespace iEducar\Modules\Unification;

use App\Models\LogUnification;
use App\Models\LogUnificationOldData;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

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
            ->get();

        $arrayNames = [];
        foreach ($students as $student) {
            $arrayNames[] = $student->individual->real_name;
        }

        return $arrayNames;
    }

    /**
     * @return string
     */
    public static function getType()
    {
        return Student::class;
    }

    public function undo(LogUnification $logUnification)
    {
        $oldData = $logUnification->oldData;

        DB::beginTransaction();

        foreach ($oldData as $data) {
            $this->undoData($data);
        }

        DB::commit();
    }

    /**
     * @param LogUnificationOldData $data
     */
    private function undoData($data)
    {
        $query = DB::table($data->table)->select();

        foreach ($data->keys as $key) {
            $query->where(key($key), $key);
        }

        $query->update($data->old_data);
    }
}