<?php

namespace App\Imports;

use App\Models\LegacyDiscipline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class DisciplineImport implements ToModel, WithHeadingRow, WithProgressBar
{
    use Importable;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * DisciplineImport constructor.
     */
    public function __construct()
    {
        $this->collection = new Collection();
    }

    /**
    * @param array $row
    *
    * @return Model
    */
    public function model(array $row)
    {
        $discipline = LegacyDiscipline::query()->firstOrNew([
            'instituicao_id' => $row['institution_id'],
            'area_conhecimento_id' => $row['knowledge_area_id'],
            'nome' => $row['name'],
        ], [
            'abreviatura' => $row['abbreviation'],
            'tipo_base' => $row['curriculum_base'],
            'codigo_educacenso' => $row['educacenso_discipline'],
            'ordenamento' => $row['order'],
        ]);

        $collection = new Collection([
            'row' => $row,
            'discipline' => $discipline,
        ]);
        
        $this->collection->push($collection);

        return $discipline;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }
}
