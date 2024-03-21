<?php

namespace App\Models\Exporter\Builders;

use App\Support\Database\JoinableBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class EnrollmentEloquentBuilder extends Builder
{
    use JoinableBuilder;

    /**
     * Colunas legadas usadas para gerar a query do exportador dinámicamente sem a view
     */
    public function getLegacyColumns(): array
    {
        return [
            'mother.person' => [
                'id' => 'm.idpes as ID da mãe',
                'name' => 'm.nome as Nome da mãe',
                'email' => 'm.email as E-mail da mãe',
            ],
            'mother.individual' => [
                'social_name' => 'mf.nome_social as Nome social e/ou afetivo da mãe',
                'cpf' => DB::raw('trim(to_char(mf.cpf, \'000"."000"."000"-"00\')) as "CPF da mãe"'),
                'date_of_birth' => 'mf.data_nasc as Data de nascimento da mãe',
                'sus' => 'mf.sus as Número SUS da mãe',
                'nis' => 'mf.nis_pis_pasep as NIS (PIS/PASEP) da mãe',
                'occupation' => 'mf.ocupacao as Ocupação da mãe',
                'organization' => 'mf.empresa as Empresa da mãe',
                'monthly_income' => 'mf.renda_mensal as Renda Mensal da mãe',
                'gender' => 'mf.sexo as Gênero da mãe',
            ],
            'mother.document' => [
                'rg' => 'md.rg as RG da mãe',
                'rg_issue_date' => 'md.data_exp_rg as RG (Data Emissão) da mãe',
                'rg_state_abbreviation' => 'md.sigla_uf_exp_rg as RG (Estado) da mãe',
            ],
            'mother.phone' => [
                'phone' => 'mep.phones as Telefones da mãe',
            ],
            'father.person' => [
                'id' => 'f.idpes as ID do pai',
                'name' => 'f.nome as Nome do pai',
                'email' => 'f.email as E-mail do pai',
            ],
            'father.individual' => [
                'social_name' => 'ff.nome_social as Nome social e/ou afetivo do pai',
                'cpf' => DB::raw('trim(to_char(ff.cpf, \'000"."000"."000"-"00\')) as "CPF do pai"'),
                'date_of_birth' => 'ff.data_nasc as Data de nascimento do pai',
                'sus' => 'ff.sus as Número SUS do pai',
                'nis' => 'ff.nis_pis_pasep as NIS (PIS/PASEP) do pai',
                'occupation' => 'ff.ocupacao as Ocupação do pai',
                'organization' => 'ff.empresa as Empresa do pai',
                'monthly_income' => 'ff.renda_mensal as Renda Mensal do pai',
                'gender' => 'ff.sexo as Gênero do pai',
            ],
            'father.document' => [
                'rg' => 'fd.rg as RG do pai',
                'rg_issue_date' => 'fd.data_exp_rg as RG (Data Emissão) do pai',
                'rg_state_abbreviation' => 'fd.sigla_uf_exp_rg as RG (Estado) do pai',
            ],
            'father.phone' => [
                'phone' => 'fep.phones as Telefones do pai',
            ],
            'guardian.person' => [
                'id' => 'g.idpes as ID do responsável',
                'name' => 'g.nome as Nome do responsável',
                'email' => 'g.email as E-mail do responsável',
            ],
            'guardian.individual' => [
                'social_name' => 'gf.nome_social as Nome social e/ou afetivo do responsável',
                'cpf' => DB::raw('trim(to_char(gf.cpf, \'000"."000"."000"-"00\')) as "CPF do responsável"'),
                'date_of_birth' => 'gf.data_nasc as Data de nascimento do responsável',
                'sus' => 'gf.sus as Número SUS do responsável',
                'nis' => 'gf.nis_pis_pasep as NIS (PIS/PASEP) do responsável',
                'occupation' => 'gf.ocupacao as Ocupação do responsável',
                'organization' => 'gf.empresa as Empresa do responsável',
                'monthly_income' => 'gf.renda_mensal as Renda Mensal do responsável',
                'gender' => 'gf.sexo as Gênero do responsável',
            ],
            'guardian.document' => [
                'rg' => 'gd.rg as RG do responsável',
                'rg_issue_date' => 'gd.data_exp_rg as RG (Data Emissão) do responsável',
                'rg_state_abbreviation' => 'gd.sigla_uf_exp_rg as RG (Estado) do responsável',
            ],
            'guardian.phone' => [
                'phone' => 'gep.phones as Telefones do responsável',
            ],
            'place' => [
                'address' => 'p.address as Logradouro',
                'number' => 'p.number as Número',
                'complement' => 'p.complement as Complemento',
                'neighborhood' => 'p.neighborhood as Bairro',
                'postal_code' => 'p.postal_code as CEP',
                'latitude' => 'p.latitude as Latitude',
                'longitude' => 'p.longitude as Longitude',
                'city' => 'c.name as Cidade',
                'state_abbreviation' => 's.abbreviation as Sigla do Estado',
                'state' => 's.name as Estado',
                'country' => 'cn.name as País',
            ],
        ];
    }

    /**
     * @param array $columns
     */
    public function mother($columns)
    {
        //pessoa
        if ($only = $this->model->getLegacyExportedColumns('mother.person', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('cadastro.pessoa as m', 'exporter_student.mother_id', 'm.idpes');
        }

        //fisica
        if ($only = $this->model->getLegacyExportedColumns('mother.individual', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('cadastro.fisica as mf', 'exporter_student.mother_id', 'mf.idpes');
        }

        //documento
        if ($only = $this->model->getLegacyExportedColumns('mother.document', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('cadastro.documento as md', 'exporter_student.mother_id', 'md.idpes');
        }

        //telefone
        if ($only = $this->model->getLegacyExportedColumns('mother.phone', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('exporter_phones as mep', 'exporter_student.mother_id', 'mep.person_id');
        }

        return $this;
    }

    /**
     * @param array $columns
     * @return void
     */
    public function father($columns)
    {
        //pessoa
        if ($only = $this->model->getLegacyExportedColumns('father.person', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('cadastro.pessoa as f', 'exporter_student.father_id', 'f.idpes');
        }

        //fisica
        if ($only = $this->model->getLegacyExportedColumns('father.individual', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('cadastro.fisica as ff', 'exporter_student.father_id', 'ff.idpes');
        }

        //documento
        if ($only = $this->model->getLegacyExportedColumns('father.document', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('cadastro.documento as fd', 'exporter_student.father_id', 'fd.idpes');
        }

        //telefone
        if ($only = $this->model->getLegacyExportedColumns('father.phone', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('exporter_phones as fep', 'exporter_student.father_id', 'fep.person_id');
        }

        return $this;
    }

    /**
     * @param array $columns
     * @return EnrollmentEloquentBuilder
     */
    public function guardian($columns)
    {
        //pessoa
        if ($only = $this->model->getLegacyExportedColumns('guardian.person', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('cadastro.pessoa as g', 'exporter_student.guardian_id', 'g.idpes');
        }

        //fisica
        if ($only = $this->model->getLegacyExportedColumns('guardian.individual', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('cadastro.fisica as gf', 'exporter_student.guardian_id', 'gf.idpes');
        }

        //documento
        if ($only = $this->model->getLegacyExportedColumns('guardian.document', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('cadastro.documento as gd', 'exporter_student.guardian_id', 'gd.idpes');
        }

        //telefone
        if ($only = $this->model->getLegacyExportedColumns('guardian.phone', $columns)) {
            $this->addSelect($only);
            $this->leftJoin('exporter_phones as gep', 'exporter_student.guardian_id', 'gep.person_id');
        }

        return $this;
    }

    /**
     * @return EnrollmentEloquentBuilder
     */
    public function benefits()
    {
        $this->addSelect(
            $this->joinColumns('benefits', ['benefits'])
        );

        return $this->leftJoin('exporter_benefits as benefits', function (JoinClause $join) {
            $join->on('exporter_student.student_id', '=', 'benefits.student_id');
        });
    }

    /**
     * @return EnrollmentEloquentBuilder
     */
    public function disabilities()
    {
        $this->addSelect(
            $this->joinColumns('disabilities', ['disabilities'])
        );

        return $this->leftJoin('exporter_disabilities as disabilities', function (JoinClause $join) {
            $join->on('exporter_student.id', '=', 'disabilities.person_id');
        });
    }

    /**
     * @return EnrollmentEloquentBuilder
     */
    public function phones()
    {
        $this->addSelect(
            $this->joinColumns('phones', ['phones'])
        );

        return $this->leftJoin('exporter_phones as phones', function (JoinClause $join) {
            $join->on('exporter_student.id', '=', 'phones.person_id');
        });
    }

    public function place($columns)
    {
        $this->leftJoin('person_has_place', static function (JoinClause $join) {
            $join->on('exporter_student.id', '=', 'person_has_place.person_id');
        });

        if ($only = $this->model->getLegacyExportedColumns('place', $columns)) {
            $this->addSelect($only);

            $this->leftJoin('places as p', 'p.id', 'person_has_place.id')
                ->leftJoin('cities as c', 'c.id', 'p.city_id')
                ->leftJoin('states as s', 's.id', 'c.state_id')
                ->leftJoin('countries as cn', 'cn.id', 's.country_id');
        }

        return $this;
    }

    public function transport($columns)
    {
        if (in_array('tipo_transporte', $columns)) {
            unset($columns[array_search('tipo_transporte', $columns)]);

            $this->addSelect(DB::raw('
                CASE aluno.tipo_transporte
                    WHEN 0 THEN \'Não utiliza\'::varchar
                    WHEN 1 THEN \'Estadual\'::varchar
                    WHEN 2 THEN \'Municipal\'::varchar
                    ELSE \'Não utiliza\'::varchar
                END AS tipo_transporte
            '));
        }

        if (in_array('veiculo_transporte_escolar', $columns)) {
            unset($columns[array_search('veiculo_transporte_escolar', $columns)]);
            $this->addSelect(DB::raw('
                COALESCE(
                    (SELECT string_agg(CASE veiculo
                            WHEN 1 THEN \'Rodoviário - Vans/Kombis\'::varchar
                            WHEN 2 THEN \'Rodoviário - Microônibus\'::varchar
                            WHEN 3 THEN \'Rodoviário - Ônibus\'::varchar
                            WHEN 4 THEN \'Rodoviário - Bicicleta\'::varchar
                            WHEN 5 THEN \'Rodoviário - Tração animal\'::varchar
                            WHEN 6 THEN \'Rodoviário - Outro\'::varchar
                            WHEN 7 THEN \'Aquaviário/Embarcação - Capacidade de até 5 alunos\'::varchar
                            WHEN 8 THEN \'Aquaviário/Embarcação - Capacidade entre 5 a 15 alunos\'::varchar
                            WHEN 9 THEN \'Aquaviário/Embarcação - Capacidade entre 15 a 35 alunos\'::varchar
                            WHEN 10 THEN \'Aquaviário/Embarcação - Capacidade acima de 35 alunos\'::varchar
                            WHEN 11 THEN \'Ferroviário - Trem/Metrô\'::varchar
                            ELSE \'Não Informado\'::varchar
                        END, \' | \') as veiculo_transporte_escolar
                    FROM UNNEST(aluno.veiculo_transporte_escolar) as veiculo)
                , \'Não informado\') AS veiculo_transporte_escolar
            '));
        }

        $this->addSelect(
            $this->joinColumns('aluno', $columns)
        );

        return $this->leftJoin('pmieducar.aluno as aluno', function (JoinClause $join) {
            $join->on('exporter_student.student_id', '=', 'aluno.cod_aluno');
        });
    }
}
