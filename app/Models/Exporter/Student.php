<?php

namespace App\Models\Exporter;

use App\Models\Exporter\Builders\StudentEloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class Student extends Model
{
    /**
     * @var string
     */
    protected $table = 'exporter_student';

    /**
     * @param Builder $query
     *
     * @return StudentEloquentBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new StudentEloquentBuilder($query);
    }

    /**
     * @return array
     */
    public function getExportedColumnsByGroup()
    {
        return [
            'Aluno' => [
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
                'phones.phones' => 'Telefones',
                'benefits.benefits' => 'Benefícios',
                'disabilities.disabilities' => 'Deficiências',
            ],
            'Códigos' => [
                'id' => 'ID Pessoa',
                'student_id' => 'ID Aluno',
                'registration_id' => 'ID Matrícula',
                'school_id' => 'ID Escola',
                'school_class_id' => 'ID Turma',
                'grade_id' => 'ID Série',
                'course_id' => 'ID Curso',
            ],
            'Escola' => [
                'school' => 'Escola',
                'school_class' => 'Turma',
                'grade' => 'Série',
                'course' => 'Curso',
                'registration_date' => 'Data da Matrícula',
                'year' => 'Ano',
                'status_text' => 'Situação da Matrícula',
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
            'Mãe' => [
                'mother.id' => 'ID da mãe',
                'mother.name' => 'Nome da mãe',
                'mother.social_name' => 'Nome social da mãe',
                'mother.cpf' => 'CPF da mãe',
                'mother.date_of_birth' => 'Data de nascimento da mãe',
                'mother.email' => 'E-mail da mãe',
                'mother.sus' => 'Número SUS da mãe',
                'mother.occupation' => 'Ocupação da mãe',
                'mother.organization' => 'Empresa da mãe',
                'mother.monthly_income' => 'Renda Mensal da mãe',
                'mother.gender' => 'Gênero da mãe',
            ],
            'Pai' => [
                'father.id' => 'ID do pai',
                'father.name' => 'Nome do pai',
                'father.social_name' => 'Nome social do pai',
                'father.cpf' => 'CPF do pai',
                'father.date_of_birth' => 'Data de nascimento do pai',
                'father.email' => 'E-mail do pai',
                'father.sus' => 'Número SUS do pai',
                'father.occupation' => 'Ocupação do pai',
                'father.organization' => 'Empresa do pai',
                'father.monthly_income' => 'Renda Mensal do pai',
                'father.gender' => 'Gênero do pai',
            ],
            'Responsável' => [
                'guardian.id' => 'ID do responsável',
                'guardian.name' => 'Nome do responsável',
                'guardian.social_name' => 'Nome social do responsável',
                'guardian.cpf' => 'CPF do responsável',
                'guardian.date_of_birth' => 'Data de nascimento do responsável',
                'guardian.email' => 'E-mail do responsável',
                'guardian.sus' => 'Número SUS do responsável',
                'guardian.occupation' => 'Ocupação do responsável',
                'guardian.organization' => 'Empresa do responsável',
                'guardian.monthly_income' => 'Renda Mensal do responsável',
                'guardian.gender' => 'Gênero do responsável',
            ],
        ];
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
