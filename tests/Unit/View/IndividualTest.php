<?php

namespace Tests\Unit\View;

use App\Models\City;
use App\Models\Country;
use App\Models\Employee;
use App\Models\Individual;
use App\Models\LocalizationZone;
use App\Models\LogUnification;
use App\Models\Nationality;
use App\Models\Person;
use App\Models\Phone;
use App\Models\Religion;
use App\Models\Student;
use App\User;
use Database\Factories\CityFactory;
use Database\Factories\CountryFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\ReligionFactory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\ViewTestCase;

class IndividualTest extends ViewTestCase
{
    protected $relations = [
        'person' => Person::class,
        'phone' => Phone::class,
        'unification' => LogUnification::class,
    ];

    protected function getViewModelName(): string
    {
        return Individual::class;
    }

    private function individual(array $attributes = []): Individual
    {
        return Individual::query()->findOrFail(LegacyIndividualFactory::new()->create($attributes)->getKey());
    }

    public function test_describes_gender_nationality_registry_origin_and_localization_zone(): void
    {
        $individual = $this->individual([
            'sexo' => 'M',
            'nacionalidade' => Nationality::FOREIGN,
            'zona_localizacao_censo' => LocalizationZone::RURAL,
        ]);

        // O model legado força a origem "M" ao criar; a view traduz a coluna.
        DB::table('cadastro.fisica')->where('idpes', $individual->id)->update(['origem_gravacao' => 'U']);
        $individual->refresh();

        $this->assertSame('Masculino', $individual->gender_description);
        $this->assertSame('Estrangeira', $individual->nationality_description);
        $this->assertSame('Unificação', $individual->registry_origin_description);
        $this->assertSame('Rural', $individual->localization_zone_description);
    }

    public function test_real_name_prefers_the_social_name(): void
    {
        $this->assertSame('Bia', $this->individual(['nome_social' => 'Bia'])->real_name);

        $plain = $this->individual(['nome_social' => null]);

        $this->assertSame($plain->person->name, $plain->real_name);
    }

    public function test_parent_and_guardian_names_come_from_the_linked_people(): void
    {
        $mother = LegacyIndividualFactory::new()->create(['nome_social' => 'Mãe Social']);
        $father = LegacyIndividualFactory::new()->create();
        $guardian = LegacyIndividualFactory::new()->create();

        $individual = $this->individual([
            'idpes_mae' => $mother,
            'idpes_pai' => $father,
            'idpes_responsavel' => $guardian,
            'nome_mae' => 'Mãe Digitada',
        ]);

        $this->assertSame('Mãe Social', $individual->mother_name);
        $this->assertSame($father->person->name, $individual->father_name);
        $this->assertSame($guardian->person->name, $individual->guardian_name);
    }

    public function test_parent_and_guardian_names_fall_back_to_the_typed_names(): void
    {
        $individual = $this->individual([
            'nome_mae' => 'Mãe Digitada',
            'nome_pai' => 'Pai Digitado',
            'nome_responsavel' => 'Responsável Digitado',
        ]);

        $this->assertSame('Mãe Digitada', $individual->mother_name);
        $this->assertSame('Pai Digitado', $individual->father_name);
        $this->assertSame('Responsável Digitado', $individual->guardian_name);
    }

    public function test_formats_the_cpf(): void
    {
        $this->assertSame('123.456.789-09', $this->individual(['cpf' => 12345678909])->cpf);
    }

    public function test_student_user_and_employee_share_the_individual_key(): void
    {
        $individual = $this->individual();

        LegacyStudentFactory::new()->create(['ref_idpes' => $individual->id]);
        LegacyEmployeeFactory::new()->create(['ref_cod_pessoa_fj' => $individual->id]);
        LegacyUserFactory::new()->create(['cod_usuario' => $individual->id]);
        EmployeeFactory::new()->create(['id' => $individual->id]);

        $this->assertInstanceOf(Student::class, $individual->student);
        $this->assertSame($individual->id, $individual->student->individual_id);
        $this->assertInstanceOf(User::class, $individual->user);
        $this->assertSame($individual->id, $individual->user->getKey());
        $this->assertInstanceOf(Employee::class, $individual->employee);
        $this->assertSame($individual->id, $individual->employee->getKey());
    }

    public function test_country_city_religion_and_the_people_who_registered_it(): void
    {
        // A coluna legada do país guarda só três dígitos.
        $country = CountryFactory::new()->create(['id' => 76]);
        $city = CityFactory::new()->create();
        $religion = ReligionFactory::new()->create();
        $creator = LegacyIndividualFactory::new()->create();
        $editor = LegacyIndividualFactory::new()->create();
        $remover = LegacyIndividualFactory::new()->create();

        $individual = $this->individual([
            'idpais_estrangeiro' => $country,
            'idmun_nascimento' => $city,
            'ref_cod_religiao' => $religion,
            'idpes_cad' => $creator,
            'idpes_rev' => $editor,
            'ref_usuario_exc' => $remover,
        ]);

        $this->assertInstanceOf(Country::class, $individual->country);
        $this->assertSame($country->getKey(), $individual->country->getKey());
        $this->assertInstanceOf(City::class, $individual->city);
        $this->assertSame($city->getKey(), $individual->city->getKey());
        $this->assertInstanceOf(Religion::class, $individual->religion);
        $this->assertSame($religion->getKey(), $individual->religion->getKey());
        $this->assertSame($creator->getKey(), $individual->createdBy->id);
        $this->assertSame($editor->getKey(), $individual->updatedBy->id);
        $this->assertSame($remover->getKey(), $individual->deletedBy->id);
    }

    // Defeito conhecido: a relação lê `idmun_nascimento`, mas a view expõe a
    // coluna como `city_id`; o local de nascimento nunca é encontrado.
    public function test_defeito_birthplace_le_uma_coluna_que_a_view_nao_tem(): void
    {
        $this->assertNull($this->individual(['idmun_nascimento' => CityFactory::new()->create()])->birthplace);
    }

    // Defeito conhecido: a relação procura `person_id` em `places`, que não tem
    // essa coluna; o endereço da pessoa vem por `Person::place()`.
    public function test_defeito_place_procura_uma_coluna_que_a_tabela_nao_tem(): void
    {
        $this->expectException(QueryException::class);

        $this->individual()->place;
    }
}
