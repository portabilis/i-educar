<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.escola';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_escola';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return Enrollment[]
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'ref_cod_turma', 'cod_turma');
    }

    /**
     * Retorna os dias da semana em um array
     *
     * @param  string  $value
     * @return array|null
     */
    public function getDiasSemanaAttribute($value)
    {
        if (is_string($value)) {
            $value = explode(',', str_replace(['{', '}'], '', $value));
        }

        return (array) $value;
    }

    /**
     * Seta os dias da semana transformando um array em uma string
     *
     * @param  array  $values
     * @return void
     */
    public function setDiasSemanaAttribute($values)
    {
        if (is_array($values)) {
            $values = '{' . implode(',', $values) . '}';
        }

        $this->attributes['dias_semana'] = $values;
    }
}
