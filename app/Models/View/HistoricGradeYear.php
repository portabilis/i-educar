<?php

namespace App\Models\View;

use App\Casts\LegacyArray;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class HistoricGradeYear extends Model
{
    protected $table = 'relatorio.view_historico_series_anos';

    protected $primaryKey = 'cod_aluno';

    public $timestamps = false;

    protected $casts = [
        'tipos_base' => LegacyArray::class,
    ];

    public function gradeScore1(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nota_1serie !== null && $this->nota_1serie !== '' ? $this->nota_1serie : '-'
        );
    }

    public function gradeScore2(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nota_2serie !== null && $this->nota_2serie !== '' ? $this->nota_2serie : '-'
        );
    }

    public function gradeScore3(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nota_3serie !== null && $this->nota_3serie !== '' ? $this->nota_3serie : '-'
        );
    }

    public function gradeScore4(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nota_4serie !== null && $this->nota_4serie !== '' ? $this->nota_4serie : '-'
        );
    }

    public function gradeScore5(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nota_5serie !== null && $this->nota_5serie !== '' ? $this->nota_5serie : '-'
        );
    }

    public function chd1(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ano_1serie !== null ? $this->carga_horaria_disciplina1 : 0
        );
    }

    public function chd2(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ano_2serie !== null ? $this->carga_horaria_disciplina2 : 0
        );
    }

    public function chd3(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ano_3serie !== null ? $this->carga_horaria_disciplina3 : 0
        );
    }

    public function chd4(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ano_4serie !== null ? $this->carga_horaria_disciplina4 : 0
        );
    }

    public function chd5(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ano_5serie !== null ? $this->carga_horaria_disciplina5 : 0
        );
    }
}
