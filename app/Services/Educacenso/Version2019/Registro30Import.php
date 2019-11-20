<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\Employee;
use App\Models\EmployeeInep;
use App\Models\LegacyCity;
use App\Models\LegacyCountry;
use App\Models\LegacyDeficiency;
use App\Models\LegacyIndividual;
use App\Models\LegacyInstitution;
use App\Models\LegacyPerson;
use App\Models\LegacyRace;
use App\Models\LegacyStudent;
use App\Models\StudentInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;
use iEducar\Modules\Educacenso\Model\Deficiencias;

class Registro30Import implements RegistroImportInterface
{
    /**
     * @var Registro30
     */
    private $model;

    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $year;

    /**
     * @var LegacyInstitution
     */
    private $institution;

    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param RegistroEducacenso $model
     * @param int $year
     * @param $user
     * @return void
     */
    public function import(RegistroEducacenso $model, $year, $user)
    {
        $this->user = $user;
        $this->model = $model;
        $this->institution = app(LegacyInstitution::class);

        $person = $this->getOrCreatePerson();

        $this->createRace($person);
        $this->createDeficiencies($person);

        if ($this->model->isStudent()) {
            $student = $this->createStudent($person);
            $this->createStudentInep($student);
        }

        if ($this->model->isTeacher() || $this->model->isManager()) {
            $employee = $this->createEmployee($person);
            $this->createEmployeeInep($employee);
        }
    }

    /**
     * @param $arrayColumns
     * @return Registro30|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro30();
        $registro->hydrateModel($arrayColumns);
        return $registro;
    }

    /**
     * @return LegacyPerson
     */
    private function getOrCreatePerson()
    {
        $person = $this->getPerson();

        if (empty($person)) {
            $person = $this->createPerson();
        }

        return $person;
    }

    /**
     * @return LegacyPerson|null
     */
    private function getPerson()
    {
        $inepNumber = $this->model->inepPessoa;

        if (empty($inepNumber)) {
            return $this->getPersonByCpf($this->model->cpf);
        }

        if ($this->model->isStudent()) {
            /** @var StudentInep $studentInep */
            $studentInep = StudentInep::where('cod_aluno_inep', $inepNumber)->first();

            if (empty($studentInep)) {
                return null;
            }

            return $studentInep->student->person;
        }

        /** @var EmployeeInep $employeeInep */
        $employeeInep = EmployeeInep::where('cod_docente_inep', $inepNumber)->first();

        if (empty($employeeInep)) {
            return null;
        }

        return $employeeInep->employee->person;
    }

    /**
     * @return LegacyPerson
     */
    private function createPerson()
    {
        $filiacao1 = $this->createFiliacao($this->model->filiacao1);
        $filiacao2 = $this->createFiliacao($this->model->filiacao1);

        $this->createFiliacao($this->model->filiacao2);
        $person = LegacyPerson::create([
            'nome' => $this->model->nomePessoa,
            'data_cad' => now(),
            'tipo' => 'F',
            'situacao' => 'P',
            'origem_gravacao' => 'U',
            'operacao' => 'I',
        ]);

        LegacyIndividual::create([
            'idpes' => $person->getKey(),
            'data_cad' => now(),
            'operacao' => 'I',
            'origem_gravacao' => 'U',
            'sexo' => $this->model->sexo == '1' ? 'M' : 'F',
            'data_nasc' => \DateTime::createFromFormat('d/m/Y', $this->model->dataNascimento),
            'idpes_mae' => $filiacao1 ? $filiacao1->getKey() : null,
            'idpes_pai' => $filiacao2 ? $filiacao2->getKey() : null,
            'nacionalidade' => $this->model->nacionalidade,
            'idpais_estrangeiro' => $this->getCountry($this->model->paisNacionalidade),
            'idmun_nascimento' => $this->getCity($this->model->municipioNascimento),
            'cpf' => (int) $this->model->cpf,
        ]);

        return $person;
    }

    /**
     * @param $name
     * @return LegacyPerson|null
     */
    private function createFiliacao($name)
    {
        if (empty($name)) {
            return null;
        }

        $person = LegacyPerson::create([
            'nome' => $name,
            'data_cad' => now(),
            'tipo' => 'F',
            'situacao' => 'P',
            'origem_gravacao' => 'U',
            'operacao' => 'I',
        ]);

        LegacyIndividual::create([
            'idpes' => $person->getKey(),
            'data_cad' => now(),
            'operacao' => 'I',
            'origem_gravacao' => 'U',
        ]);

        return $person;
    }

    /**
     * @param LegacyPerson $person
     * @return LegacyStudent mixed
     */
    private function createStudent($person)
    {
        $student = LegacyStudent::firstOrCreate([
            'ref_idpes' => $person->getKey()
        ], [
            'data_cadastro' => now(),
        ]);

        return $student;
    }

    /**
     * @param LegacyStudent $person
     */
    private function createStudentInep($student)
    {
        if (StudentInep::where('cod_aluno_inep', $this->model->inepPessoa)
            ->exists()) {
            return;
        }

        StudentInep::create([
            'cod_aluno' => $student->getKey(),
            'cod_aluno_inep' => $this->model->inepPessoa,
        ]);
    }

    /**
     * @param LegacyPerson $person
     * @return Employee
     */
    private function createEmployee($person)
    {
        return Employee::firstOrCreate([
            'cod_servidor' => $person->getKey(),
            'ref_cod_instituicao' => $this->institution->getKey()
        ], [
            'carga_horaria' => 0,
            'data_cadastro' => now()
        ]);
    }

    /**
     * @param LegacyPerson c$person
     */
    private function createEmployeeInep($employee)
    {
        if (empty($this->model->inepPessoa)) {
            return;
        }

        if (EmployeeInep::where('cod_docente_inep', $this->model->inepPessoa)
            ->exists()) {
            return;
        }

        EmployeeInep::create([
            'cod_servidor' => $employee->getKey(),
            'cod_docente_inep' => $this->model->inepPessoa,
        ]);
    }

    /**
     * @param LegacyPerson $person
     */
    private function createRace($person)
    {
        if ($person->individual->race()->count()) {
            return;
        }

        $race = $this->getOrCreateRace($person);
        $person->individual->race()->attach($race);
    }

    /**
     * @param $person
     * @return LegacyRace
     */
    private function getOrCreateRace($person)
    {
        $race = LegacyRace::where('raca_educacenso', $this->model->raca)->first();

        if (!empty($race)) {
            return $race;
        }

        return LegacyRace::create([
            'idpes_cad' => $this->user->getKey(),
            'nm_raca' => $this->getRaceName($this->model->raca),
            'data_cadastro' => now(),
            'raca_educacenso' => $this->model->raca,
        ]);
    }

    /**
     * @param LegacyPerson $person
     */
    private function createDeficiencies($person)
    {
        if ($this->model->deficienciaCegueira) {
            $this->createDeficiency($person, Deficiencias::CEGUEIRA);
        }

        if ($this->model->deficienciaBaixaVisao) {
            $this->createDeficiency($person, Deficiencias::BAIXA_VISAO);
        }

        if ($this->model->deficienciaSurdez) {
            $this->createDeficiency($person, Deficiencias::SURDEZ);
        }

        if ($this->model->deficienciaAuditiva) {
            $this->createDeficiency($person, Deficiencias::DEFICIENCIA_AUDITIVA);
        }

        if ($this->model->deficienciaSurdoCegueira) {
            $this->createDeficiency($person, Deficiencias::SURDOCEGUEIRA);
        }

        if ($this->model->deficienciaFisica) {
            $this->createDeficiency($person, Deficiencias::DEFICIENCIA_FISICA);
        }

        if ($this->model->deficienciaIntelectual) {
            $this->createDeficiency($person, Deficiencias::DEFICIENCIA_INTELECTUAL);
        }

        if ($this->model->deficienciaAutismo) {
            $this->createDeficiency($person, Deficiencias::TRANSTORNO_ESPECTRO_AUTISTA);
        }

        if ($this->model->deficienciaAltasHabilidades) {
            $this->createDeficiency($person, Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO);
        }
    }

    /**
     * @param LegacyPerson $person
     * @param int $educacendoDeficiency
     */
    private function createDeficiency($person, $educacendoDeficiency)
    {
        $deficiency = LegacyDeficiency::where('deficiencia_educacenso', $educacendoDeficiency)->first();

        if (empty($deficiency)) {
            $deficiency = LegacyDeficiency::create([
                'nm_deficiencia' => Deficiencias::getDescriptiveValues()[$educacendoDeficiency] ?? 'Deficiência',
                'deficiencia_educacenso' => $educacendoDeficiency,
            ]);
        }

        $individual = $person->individual;
        if($individual->deficiency()
            ->where('deficiencia_educacenso', $educacendoDeficiency)
            ->exists()) {
            return;
        }

        $individual->deficiency()->attach($deficiency);
    }

    /**
     * @param $raca
     * @return string
     */
    private function getRaceName($raca)
    {
        $string = [
            0 => 'Não declarada',
            1 => 'Branca',
            2 => 'Preta',
            3 => 'Parda',
            4 => 'Amarela',
            5 => 'Indígena',
        ];

        return $string[$raca] ?? 'Não declarada';
    }

    /**
     * @param $cpf
     * @return LegacyPerson|null
     */
    private function getPersonByCpf($cpf)
    {
        if (empty($cpf)) {
            return null;
        }

        /** @var LegacyIndividual $individual */
        $individual = LegacyIndividual::where('cpf', $cpf)->first();

        if (empty($individual)) {
            return null;
        }

        return $individual->person;
    }

    private function getCity($cityIbge)
    {
        if (empty($cityIbge)) {
            return null;
        }

        return LegacyCity::where('cod_ibge', $cityIbge)->first()->getKey() ?: null;
    }

    private function getCountry($countryIbge)
    {
        if (empty($countryIbge)) {
            return null;
        }

        return LegacyCountry::where('cod_ibge', $countryIbge)->first()->getKey() ?: null;
    }
}
