<?php

namespace App\Models\Exporter;

use App\Models\Exporter\Builders\StudentEloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class Student extends Model
{
    /**
     * @var string
     */
    protected $table = 'exporter_student_grouped_registration';

    /**
     * @var Collection
     */
    protected $alias;

    /**
     * @param Builder $query
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
            'Códigos' => [
                'id' => 'ID Pessoa',
                'student_id' => 'ID Aluno',
                'school_id' => 'ID Escola',
                'school_class_id' => 'ID Turma',
                'grade_id' => 'ID Série',
                'course_id' => 'ID Curso',
                'inep_id' => 'Código INEP (Aluno)',
                'codigo_sistema' => 'Código Sistema',
            ],
            'Aluno' => [
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
                'registration_code_id' => 'Código da Rede Estadual (RA)',
                'occupation' => 'Ocupação',
                'organization' => 'Empresa',
                'monthly_income' => 'Renda Mensal',
                'gender' => 'Gênero',
                'race' => 'Raça',
                'religion' => 'Religião',
                'height' => 'Altura',
                'weight' => 'Peso',
                'uses_rural_transport' => 'Utiliza Transporte Rural',
            ],
            'Escola' => [
                'school' => 'Escola',
                'school_inep' => 'Código INEP',
                'school_class' => 'Turma',
                'grade' => 'Série',
                'course' => 'Curso',
                'year' => 'Ano',
                'period' => 'Turno',
            ],
            'Informações' => [
                'nationality' => 'Nacionalidade',
                'birthplace' => 'Naturalidade',
                'phones.phones' => 'Telefones',
                'benefits.benefits' => 'Benefícios',
                'projects.projects' => 'Projetos',
                'disabilities.disabilities' => 'Deficiências',
                'modalidade_ensino' => 'Modalidade de ensino cursada',
                'technological_resources' => 'Recursos tecnológicos',
                'transport.tipo_transporte' => 'Transporte escolar público',
                'transport_route' => 'Rota',
                'transport.veiculo_transporte_escolar' => 'Veículo utilizado',
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
                'mother.social_name' => 'Nome social e/ou afetivo da mãe',
                'mother.cpf' => 'CPF da mãe',
                'mother.rg' => 'RG da mãe',
                'mother.rg_issue_date' => 'RG (Data Emissão) da mãe',
                'mother.rg_state_abbreviation' => 'RG (Estado) da mãe',
                'mother.date_of_birth' => 'Data de nascimento da mãe',
                'mother.email' => 'E-mail da mãe',
                'mother.sus' => 'Número SUS da mãe',
                'mother.nis' => 'NIS (PIS/PASEP) da mãe',
                'mother.occupation' => 'Ocupação da mãe',
                'mother.organization' => 'Empresa da mãe',
                'mother.monthly_income' => 'Renda Mensal da mãe',
                'mother.gender' => 'Gênero da mãe',
                'mother.phone' => 'Telefones da mãe',
            ],
            'Pai' => [
                'father.id' => 'ID do pai',
                'father.name' => 'Nome do pai',
                'father.social_name' => 'Nome social e/ou afetivo do pai',
                'father.cpf' => 'CPF do pai',
                'father.rg' => 'RG do pai',
                'father.rg_issue_date' => 'RG (Data Emissão) do pai',
                'father.rg_state_abbreviation' => 'RG (Estado) do pai',
                'father.date_of_birth' => 'Data de nascimento do pai',
                'father.email' => 'E-mail do pai',
                'father.sus' => 'Número SUS do pai',
                'father.nis' => 'NIS (PIS/PASEP) do pai',
                'father.occupation' => 'Ocupação do pai',
                'father.organization' => 'Empresa do pai',
                'father.monthly_income' => 'Renda Mensal do pai',
                'father.gender' => 'Gênero do pai',
                'father.phone' => 'Telefones do pai',
            ],
            'Responsável' => [
                'guardian.id' => 'ID do responsável',
                'guardian.name' => 'Nome do responsável',
                'guardian.social_name' => 'Nome social e/ou afetivo do responsável',
                'guardian.cpf' => 'CPF do responsável',
                'guardian.rg' => 'RG do responsável',
                'guardian.rg_issue_date' => 'RG (Data Emissão) do responsável',
                'guardian.rg_state_abbreviation' => 'RG (Estado) do responsável',
                'guardian.date_of_birth' => 'Data de nascimento do responsável',
                'guardian.email' => 'E-mail do responsável',
                'guardian.sus' => 'Número SUS do responsável',
                'guardian.nis' => 'NIS (PIS/PASEP) do responsável',
                'guardian.occupation' => 'Ocupação do responsável',
                'guardian.organization' => 'Empresa do responsável',
                'guardian.monthly_income' => 'Renda Mensal do responsável',
                'guardian.gender' => 'Gênero do responsável',
                'guardian.phone' => 'Telefones do responsável',
            ],
            'Uniforme' => [
                'uniform_distributions.type' => 'Tipo',
                'uniform_distributions.distribution_date' => 'Data de Distribuição',
                'uniform_distributions.complete_kit' => 'Kit Completo',
                'uniform_distributions.coat_pants_qty' => 'Agasalho Calça (Quantidade)',
                'uniform_distributions.coat_pants_tm' => 'Agasalho Calça (Tamanho)',
                'uniform_distributions.coat_jacket_qty' => 'Agasalho Jaqueta (Quantidade)',
                'uniform_distributions.coat_jacket_tm' => 'Agasalho Jaqueta (Tamanho)',
                'uniform_distributions.shirt_short_qty' => 'Camiseta Curta (Quantidade)',
                'uniform_distributions.shirt_short_tm' => 'Camiseta Curta (Tamanho)',
                'uniform_distributions.shirt_long_qty' => 'Camiseta Longa (Quantidade)',
                'uniform_distributions.shirt_long_tm' => 'Camiseta Longa (Tamanho)',
                'uniform_distributions.socks_qty' => 'Meias (Quantidade)',
                'uniform_distributions.socks_tm' => 'Meias (Tamanho)',
                'uniform_distributions.shorts_tactel_qty' => 'Bermuda masculina (tecidos diversos) (Quantidade)',
                'uniform_distributions.shorts_tactel_tm' => 'Bermuda masculina (tecidos diversos) (Tamanho)',
                'uniform_distributions.shorts_coton_qty' => 'Bermuda feminina (tecidos diversos) (Quantidade)',
                'uniform_distributions.shorts_coton_tm' => 'Bermuda feminina (tecidos diversos) (Tamanho)',
                'uniform_distributions.sneakers_qty' => 'Tênis (Quantidade)',
                'uniform_distributions.sneakers_tm' => 'Tênis (Tamanho)',
                'uniform_distributions.kids_shirt_qty' => 'Camiseta Infantil (Quantidade)',
                'uniform_distributions.kids_shirt_tm' => 'Camiseta Infantil (Tamanho)',
                'uniform_distributions.pants_jeans_qty' => 'Calça Jeans (Quantidade)',
                'uniform_distributions.pants_jeans_tm' => 'Calça Jeans (Tamanho)',
                'uniform_distributions.skirt_qty' => 'Saia (Quantidade)',
                'uniform_distributions.skirt_tm' => 'Saia (Tamanho)',
            ],
        ];
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Alunos';
    }

    public function getDescription()
    {
        return 'Os dados exportados serão contabilizados por quantidade de alunos(as), agrupando as informações de séries, cursos, turmas quando o(a) aluno(a) possuir mais de uma matrícula para a situação e ano filtrados.';
    }

    /**
     * @param string $column
     * @return string
     */
    public function alias($column)
    {
        if (empty($this->alias)) {
            $this->alias = collect($this->getExportedColumnsByGroup())->flatMap(static fn ($item) => $item);
        }

        return $this->alias->get($column, $column);
    }
}
