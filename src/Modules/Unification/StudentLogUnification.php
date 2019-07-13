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
        if ($logUnification->main) {
            return $logUnification->main->individual->real_name;
        }

        return 'Aluno removido pela unificação de pessoas (' . $logUnification->main_id . ')';
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

        if (empty($students)) {
            $students[] = 'Aluno(s) removido(s) pela unificação de pessoas (' . implode(',', $studentIds) . ')';
        }

        return $students;
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