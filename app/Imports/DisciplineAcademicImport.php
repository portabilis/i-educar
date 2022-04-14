<?php

namespace App\Imports;

use App\Models\LegacyDisciplineAcademicYear;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class DisciplineAcademicImport implements ToModel, WithProgressBar, WithHeadingRow
{
    use Importable;

    /**
     * @param array $row
     *
     * @return Model
     */
    public function model(array $row)
    {
        return LegacyDisciplineAcademicYear::query()->firstOrNew([
            'componente_curricular_id' => $row['discipline_id'],
            'ano_escolar_id' => $row['grade_id'],
        ], [
            'carga_horaria' => $row['class_hours'],
            'anos_letivos' => '{' . $row['academic_year'] . '}',
        ]);
    }
}
