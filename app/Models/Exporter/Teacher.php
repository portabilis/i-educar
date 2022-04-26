<?php

namespace App\Models\Exporter;

use App\Models\Exporter\Builders\TeacherEloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class Teacher extends Model
{
    /**
     * @var string
     */
    protected $table = 'exporter_teacher';

    /**
     * @var Collection
     */
    protected $alias;

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
                'social_name' => 'Nome social e/ou afetivo',
                'cpf' => 'CPF',
                'rg' => 'RG',
                'rg_issue_date' => 'RG (Data Emissão)',
                'rg_state_abbreviation' => 'RG (Estado)',
                'date_of_birth' => 'Data de nascimento',
                'email' => 'E-mail',
                'sus' => 'Número SUS',
                'nis' => 'NIS (PIS/PASEP)',
                'occupation' => 'Ocupação',
                'organization' => 'Empresa',
                'monthly_income' => 'Renda Mensal',
                'gender' => 'Gênero',
                'race' => 'Raça'
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
                'schooling_degree' => 'Escolaridade',
                'high_school_type' => 'Tipo de ensino médio cursado',
                'employee_postgraduates_complete' => 'Pós-Graduações concluídas',
                'continuing_education_course' => 'Outros cursos de formação continuada',
                'employee_graduation_complete' => 'Curso(s) superior(es) concluído(s)',
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
     * @param string $column
     *
     * @return string
     */
    public function alias($column)
    {
        if (empty($this->alias)) {
            $this->alias = collect($this->getExportedColumnsByGroup())->flatMap(function ($item) {
                return $item;
            });
        }

        return $this->alias->get($column, $column);
    }
}
