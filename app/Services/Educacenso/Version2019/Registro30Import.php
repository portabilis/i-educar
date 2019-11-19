<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\Employee;
use App\Models\EmployeeInep;
use App\Models\LegacyCity;
use App\Models\LegacyCountry;
use App\Models\LegacyIndividual;
use App\Models\LegacyInstitution;
use App\Models\LegacyPerson;
use App\Models\LegacyRace;
use App\Models\LegacyStudent;
use App\Models\StudentInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;

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

        dd($person);
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

        $this->createPersonInep($person);
        $this->createRace($person);

        return $person;
    }

    /**
     * @return LegacyPerson|null
     */
    private function getPerson()
    {
        $inepNumber = $this->model->inepPessoa;

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
            'idpes_mae' => $filiacao1,
            'idpes_pai' => $filiacao2,
            'nacionalidade' => $this->model->nacionalidade,
            'idpais_estrangeiro' => LegacyCountry::where('cod_ibge', $this->model->paisNacionalidade)->first()->getkey() ?: null,
            'idmun_nascimento' => LegacyCity::where('cod_ibge', $this->model->municipioNascimento)->first()->getKey() ?: null,
            'cpf' => $this->model->cpf,
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
            'operacao' => 'I', 'origem_gravacao',
        ]);

        return $person;
    }

    /**
     * @param LegacyPerson $person
     */
    private function createPersonInep($person)
    {
        if ($this->model->isStudent() || true) {
            $this->createStudentInep($person);
        }

        if ($this->model->isTeacher() || $this->model->isManager()) {
            $this->createEmployeeInep($person);
        }
    }

    private function createStudentInep($person)
    {
        if (StudentInep::where('cod_aluno_inep', $this->model->inepPessoa)
            ->exists()) {
            return;
        }

        $student = LegacyStudent::create([
            'ref_idpes' => $person->getKey(),
            'data_cadastro' => now(),
        ]);

        StudentInep::create([
            'cod_aluno' => $student->getKey(),
            'cod_aluno_inep' => $this->model->inepPessoa,
        ]);
    }

    private function createEmployeeInep($person)
    {
        if (EmployeeInep::where('cod_docente_inep', $this->model->inepPessoa)
            ->exists()) {
            return;
        }

        $employee = Employee::create([
            'cod_servidor' => $person->getKey(),
            'ref_cod_instituicao' => $this->institution->getKey(),
            'carga_horaria' => 0,
            'data_cadastro' => now()
        ]);

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
}
