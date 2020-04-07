<?php

namespace App\Models\Exporter;

use App\Models\Exporter\Builders\TeacherEloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class Teacher extends Model
{
    /**
     * @var string
     */
    protected $table = 'exporter_teacher';

    /**
     * @param Builder $query
     *
     * @return TeacherEloquentBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new TeacherEloquentBuilder($query);
    }

    /**
     * @return array
     */
    public function getExportedColumnsByGroup()
    {
        return [
            'Códigos' => [
                'id' => 'ID Pessoa',
                'school_id' => 'ID Escola',
                'school_class_id' => 'ID Turma',
                'grade_id' => 'ID Série',
                'course_id' => 'ID Curso',
            ],
            'Professor' => [
                'name' => 'Nome',
                'social_name' => 'Nome social',
                'cpf' => 'CPF',
                'date_of_birth' => 'Data de nascimento',
                'email' => 'E-mail',
                'sus' => 'Número SUS',
                'occupation' => 'Ocupação',
                'organization' => 'Empresa',
                'monthly_income' => 'Renda Mensal',
                'gender' => 'Gênero',
            ],
            'Escola' => [
                'school' => 'Escola',
                'school_class' => 'Turma',
                'grade' => 'Série',
                'course' => 'Curso',
                'year' => 'Ano',
                'disciplines.disciplines' => 'Disciplinas',
            ],
            'Informações' => [
                'phones.phones' => 'Telefones',
                'disabilities.disabilities' => 'Deficiências',
            ],
            'Endereço' => [
                'place.address' => 'Logradouro',
                'place.number' => 'Número',
                'place.complement' => 'Complemento',
                'place.neighborhood' => 'Bairro',
                'place.postal_code' => 'CEP',
                'place.latitude' => 'Latitude',
                'place.longitude' => 'Longitude',
                'place.city' => 'Cidade',
                'place.state_abbreviation' => 'Sigla do Estado',
                'place.state' => 'Estado',
                'place.country' => 'País',
            ],
        ];
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Professores';
    }

    /**
     * @return array
     */
    public function getAllowedExportedColumns()
    {
        return collect($this->getExportedColumnsByGroup())->flatMap(function ($item) {
            return $item;
        })->all();
    }
}
