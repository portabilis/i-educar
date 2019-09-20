<?php

namespace App\Imports;

use App\Models\LegacySchoolGradeDiscipline;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class SchoolGradeDisciplineImport implements ToModel, WithProgressBar, WithHeadingRow
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
        return LegacySchoolGradeDiscipline::query()->updateOrCreate([
            'ref_ref_cod_serie' => $row['grade_id'],
            'ref_ref_cod_escola' => $row['school_id'] ?? $this->school,
            'ref_cod_disciplina' => $row['discipline_id'],
        ], [
            // FIXME
            // Quando a carga horária é definida nesta tabela, não é marcado o
            // check "Etapas utilizadas" da tela de séries da escola.
            'carga_horaria' => null,
            'etapas_especificas' => empty($row['stage']) ? 0 : 1,
            'etapas_utilizadas' => str_replace('.', ',', (string) $row['stage']),
            'anos_letivos' => '{' . $row['academic_year'] . '}',
        ]);
    }
}
