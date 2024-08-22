<?php

namespace App\Models\Exporter;

use App\Models\Exporter\Builders\EmployeeEloquentBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Employee extends Model
{
    /** @use HasBuilder<EmployeeEloquentBuilder> */
    use HasBuilder;

    protected static string $builder = EmployeeEloquentBuilder::class;

    /**
     * @var string
     */
    protected $table = 'exporter_employee';

    /**
     * @var Collection<string, string>
     */
    protected $alias;

    /**
     * @return array
     */
    public function getExportedColumnsByGroup()
    {
        return [
            'Códigos' => [
                'id' => 'ID Pessoa',
                'school_id' => 'ID Escola',
            ],
            'Servidor' => [
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
                'race' => 'Raça',
            ],
            'Alocação' => [
                'employee_workload' => 'Carga horária do servidor',
                'year' => 'Ano',
                'school' => 'Escola',
                'period' => 'Período',
                'role' => 'Função',
                'link' => 'Vínculo',
                'allocated_workload' => 'Carga horária alocada',
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

    public function getLabel(): string
    {
        return 'Servidores';
    }

    public function getDescription(): string
    {
        return 'Os dados exportados serão contabilizados por quantidade de servidores(as) alocados(as) no ano filtrado, agrupando as informações das alocações nas escolas.';
    }

    /**
     * @param string $column
     * @return string
     */
    public function alias($column)
    {
        /** @phpstan-ignore-next-line */
        if (empty($this->alias)) {
            /** @phpstan-ignore-next-line */
            $this->alias = collect($this->getExportedColumnsByGroup())->flatMap(function ($item) {
                return $item;
            });
        }

        return $this->alias->get($column, $column);
    }
}
