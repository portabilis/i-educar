<?php

namespace App\Imports;

use App\Models\LegacySchoolGrade;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class SchoolGradeImport implements ToModel, WithProgressBar, WithHeadingRow
{
    use Importable;

    /**
     * @var int
     */
    private $school;

    /**
     * @param int $school
     */
    public function __construct($school)
    {
        $this->school = $school;
    }

    /**
    * @param array $row
    *
    * @return Model
    */
    public function model(array $row)
    {
        return LegacySchoolGrade::query()->firstOrNew([
            'ref_cod_serie' => $row['grade_id'],
            'ref_cod_escola' => $row['school_id'] ?? $this->school,
        ], [
            'ref_usuario_cad' => 1,
            'anos_letivos' => '{' . $row['academic_year'] . '}',
            'data_cadastro' => now(),
        ]);
    }
}
