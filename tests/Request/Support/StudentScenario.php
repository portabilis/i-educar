<?php

namespace Tests\Request\Support;

use App\Models\LegacyBenefit;
use App\Models\LegacyDeficiency;
use App\Models\LegacyDocument;
use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacyProject;
use App\Models\LegacyStudent;
use App\Models\Place;
use Database\Factories\CityFactory;
use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyDocumentFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyIndividualPictureFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyPhoneFactory;
use Database\Factories\LegacyProjectFactory;
use Database\Factories\LegacyRaceFactory;
use Database\Factories\LegacyStudentBenefitFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyStudentMedicalRecordFactory;
use Database\Factories\LegacyStudentProjectFactory;
use Database\Factories\PersonHasPlaceFactory;
use Database\Factories\PlaceFactory;
use Database\Factories\ReligionFactory;
use Database\Factories\StateFactory;
use Database\Factories\StudentInepFactory;
use Illuminate\Support\Facades\DB;

/**
 * Um aluno de nome conhecido e, a pedido, o que as telas de aluno e de pessoa
 * mostram em volta dele: filiação, responsável, telefones, endereço,
 * documentos, raça, religião, deficiências, benefícios, projetos, ficha médica
 * e moradia.
 *
 * As factories soltas criam cada uma dessas peças para uma pessoa diferente;
 * aqui todas apontam para a mesma pessoa física, e os textos são fixos para
 * que os testes afirmem o que a tela mostra.
 */
final class StudentScenario
{
    public LegacyPerson $person;

    public LegacyIndividual $individual;

    public LegacyStudent $student;

    private function __construct() {}

    /**
     * Um aluno com esse nome, sem filiação e sem responsável definido.
     *
     * @param array<string, mixed> $student    Colunas de `pmieducar.aluno`.
     * @param array<string, mixed> $individual Colunas de `cadastro.fisica`.
     */
    public static function named(string $name, array $student = [], array $individual = []): self
    {
        $scenario = new self;

        $scenario->individual = self::individual($name, $individual);
        $scenario->person = $scenario->individual->person;
        $scenario->student = LegacyStudentFactory::new()->create($student + [
            'ref_idpes' => $scenario->individual,
            'tipo_responsavel' => null,
        ]);

        return $scenario;
    }

    /**
     * Uma pessoa física com esse nome.
     *
     * @param array<string, mixed> $attributes Colunas de `cadastro.fisica`.
     */
    public static function individual(string $name, array $attributes = []): LegacyIndividual
    {
        return LegacyIndividualFactory::new()->create($attributes + [
            'idpes' => LegacyPersonFactory::new()->create(['nome' => $name, 'tipo' => 'F']),
            'data_nasc' => '2015-04-20',
            'sexo' => 'F',
            'cpf' => null,
        ]);
    }

    /**
     * @param array<string, mixed> $attributes Colunas de `cadastro.fisica`.
     */
    public function mother(string $name, array $attributes = []): LegacyIndividual
    {
        return $this->relative('idpes_mae', $name, $attributes);
    }

    /**
     * @param array<string, mixed> $attributes Colunas de `cadastro.fisica`.
     */
    public function father(string $name, array $attributes = []): LegacyIndividual
    {
        return $this->relative('idpes_pai', $name, $attributes + ['sexo' => 'M']);
    }

    /**
     * Outra pessoa como responsável; não muda o tipo de responsável do aluno.
     *
     * @param array<string, mixed> $attributes Colunas de `cadastro.fisica`.
     */
    public function guardian(string $name, array $attributes = []): LegacyIndividual
    {
        return $this->relative('idpes_responsavel', $name, $attributes);
    }

    /**
     * Quem responde pelo aluno: 'm' mãe, 'p' pai, 'a' ambos, 'r' outra pessoa.
     */
    public function guardianType(?string $type): self
    {
        $this->student->update(['tipo_responsavel' => $type]);

        return $this;
    }

    /**
     * Um telefone da pessoa do aluno; o tipo é uma das constantes
     * `LegacyPhone::TYPE_*`.
     */
    public function phone(int $type, string $areaCode, string $number): self
    {
        LegacyPhoneFactory::new()->create([
            'idpes' => $this->person,
            'tipo' => $type,
            'ddd' => $areaCode,
            'fone' => $number,
        ]);

        return $this;
    }

    /**
     * O endereço da pessoa do aluno, numa cidade e num estado de nome fixo.
     *
     * @param array<string, mixed> $attributes Colunas de `public.places`.
     */
    public function address(array $attributes = [], string $city = 'Cidade Aurora', string $state = 'ZA'): Place
    {
        $place = PlaceFactory::new()->create($attributes + [
            'city_id' => CityFactory::new()->create([
                'name' => $city,
                'state_id' => StateFactory::new()->create(['abbreviation' => $state]),
            ]),
            'address' => 'Rua das Acácias',
            'number' => '120',
            'complement' => 'Casa 2',
            'neighborhood' => 'Bairro Jardim',
            'postal_code' => '88800123',
        ]);

        PersonHasPlaceFactory::new()->create(['person_id' => $this->person, 'place_id' => $place, 'type' => 1]);

        return $place;
    }

    /**
     * Os documentos da pessoa do aluno; sem atributos, a linha fica vazia.
     *
     * @param array<string, mixed> $attributes Colunas de `cadastro.documento`.
     */
    public function document(array $attributes = []): LegacyDocument
    {
        // Nem toda coluna é preenchível no model (certidao_casamento, por
        // exemplo), então os atributos entram sem passar pelo fillable; e tudo
        // vai num único INSERT, porque um gatilho do banco apaga a linha de
        // documento que fica sem nenhum dado.
        $document = LegacyDocumentFactory::new()->make([
            'idpes' => $this->person->getKey(),
            'rg' => null,
            'certidao_nascimento' => null,
        ]);

        $document->forceFill($attributes)->save();

        return $document;
    }

    public function race(string $name): self
    {
        $this->individual->race()->attach(LegacyRaceFactory::new()->create(['nm_raca' => $name]));

        return $this;
    }

    public function religion(string $name): self
    {
        $this->individual->update(['ref_cod_religiao' => ReligionFactory::new()->create(['name' => $name])->getKey()]);

        return $this;
    }

    /**
     * Uma deficiência ou um transtorno da pessoa do aluno; o tipo é uma das
     * constantes de `DeficiencyType`.
     */
    public function deficiency(string $name, int $type): LegacyDeficiency
    {
        $deficiency = LegacyDeficiencyFactory::new()->create(['nm_deficiencia' => $name, 'deficiency_type_id' => $type]);

        $deficiency->individuals()->attach($this->individual->getKey());

        return $deficiency;
    }

    public function benefit(string $name): LegacyBenefit
    {
        $benefit = LegacyBenefitFactory::new()->create(['nm_beneficio' => $name]);

        LegacyStudentBenefitFactory::new()->create(['aluno_id' => $this->student, 'aluno_beneficio_id' => $benefit]);

        return $benefit;
    }

    /**
     * @param array<string, mixed> $attributes Colunas de `pmieducar.projeto_aluno`.
     */
    public function project(string $name, array $attributes = []): LegacyProject
    {
        $project = LegacyProjectFactory::new()->create(['nome' => $name]);

        LegacyStudentProjectFactory::new()->create($attributes + [
            'ref_cod_projeto' => $project,
            'ref_cod_aluno' => $this->student,
            'turno' => 1,
            'data_inclusao' => '2030-02-10',
            'data_desligamento' => null,
        ]);

        return $project;
    }

    public function inep(string $number): self
    {
        StudentInepFactory::new()->create(['student_id' => $this->student, 'number' => $number]);

        return $this;
    }

    public function picture(string $path): self
    {
        LegacyIndividualPictureFactory::new()->create(['idpes' => $this->person, 'caminho' => $path]);

        return $this;
    }

    /**
     * A ficha médica do aluno, com todas as respostas em "não" e as
     * descrições vazias, salvo o que vier em `$attributes`.
     *
     * @param array<string, mixed> $attributes Colunas de `modules.ficha_medica_aluno`.
     */
    public function medicalRecord(array $attributes = []): self
    {
        $definition = LegacyStudentMedicalRecordFactory::new()->make(['ref_cod_aluno' => $this->student])->getAttributes();

        $blank = array_map(
            fn ($value) => in_array($value, ['S', 'N'], true) ? 'N' : (is_string($value) ? '' : $value),
            $definition
        );

        LegacyStudentMedicalRecordFactory::new()->create($attributes + $blank);

        return $this;
    }

    /**
     * Os dados de moradia do aluno, tabela que não tem model nem factory.
     *
     * @param array<string, mixed> $attributes Colunas de `modules.moradia_aluno`.
     */
    public function housing(array $attributes = []): self
    {
        DB::table('modules.moradia_aluno')->insert($attributes + [
            'ref_cod_aluno' => $this->student->getKey(),
            'moradia' => 'C',
            'material' => 'A',
            'moradia_situacao' => 2,
            'quartos' => 3,
            'sala' => 1,
            'copa' => 1,
            'banheiro' => 2,
            'garagem' => 1,
            'quant_pessoas' => 4,
            'renda' => 2500,
        ]);

        return $this;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function relative(string $column, string $name, array $attributes): LegacyIndividual
    {
        $relative = self::individual($name, $attributes);

        $this->individual->update([$column => $relative->getKey()]);

        return $relative;
    }
}
