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
            'Professor' => [
                'person.name' => 'Nome',
                'person.social_name' => 'Nome social',
                'person.cpf' => 'CPF',
                'person.date_of_birth' => 'Data de nascimento',
                'person.email' => 'E-mail',
                'person.sus' => 'Número SUS',
                'person.occupation' => 'Ocupação',
                'person.organization' => 'Empresa',
                'person.monthly_income' => 'Renda Mensal',
                'person.gender' => 'Gênero',
                'phones.phones' => 'Telefones',
                'disabilities.disabilities' => 'Deficiências',
            ],
            'Códigos' => [
                'id' => 'ID Pessoa',
                'school_id' => 'ID Escola',
                'school_class_id' => 'ID Turma',
                'grade_id' => 'ID Série',
                'course_id' => 'ID Curso',
            ],
            'Escola' => [
                'school' => 'Escola',
                'school_class' => 'Turma',
                'disciplines.disciplines' => 'Disciplinas',
                'grade' => 'Série',
                'course' => 'Curso',
                'year' => 'Ano',
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
