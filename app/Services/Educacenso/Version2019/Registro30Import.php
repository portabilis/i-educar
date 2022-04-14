<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\EducacensoDegree;
use App\Models\EducacensoInstitution;
use App\Models\Employee;
use App\Models\EmployeeGraduation;
use App\Models\EmployeeInep;
use App\Models\LegacyCity;
use App\Models\LegacyCountry;
use App\Models\LegacyDeficiency;
use App\Models\LegacyDocument;
use App\Models\LegacyIndividual;
use App\Models\LegacyInstitution;
use App\Models\LegacyPerson;
use App\Models\LegacyRace;
use App\Models\LegacySchoolingDegree;
use App\Models\LegacyStudent;
use App\Models\StudentInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\Services\Educacenso\Version2019\Models\Registro30Model;
use App\User;
use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Modules\Educacenso\Model\Escolaridade;
use iEducar\Modules\Educacenso\Model\FormacaoContinuada;
use iEducar\Modules\Educacenso\Model\Nacionalidade;
use iEducar\Modules\Educacenso\Model\PosGraduacao;
use iEducar\Modules\Educacenso\Model\RecursosRealizacaoProvas;

class Registro30Import implements RegistroImportInterface
{
    /**
     * @var Registro30
     */
    protected $model;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var int
     */
    protected $year;

    /**
     * @var LegacyInstitution
     */
    protected $institution;

    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param RegistroEducacenso $model
     * @param int                $year
     * @param                    $user
     *
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
            $student = $this->getOrCreateStudent($person);
            $this->storeStudentData($student);
        }

        if ($this->model->isTeacher() || $this->model->isManager()) {
            $employee = $this->getOrCreateEmployee($person);
            $this->storeEmployeeData($employee);
        }
    }

    /**
     * @param $arrayColumns
     *
     * @return Registro30|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro30Model();
        $registro->hydrateModel($arrayColumns);

        return $registro;
    }

    /**
     * @return LegacyPerson
     */
    protected function getOrCreatePerson()
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
    protected function getPerson()
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
        $filiacao2 = $this->createFiliacao($this->model->filiacao2);

        $person = LegacyPerson::create([
            'nome' => $this->model->nomePessoa,
            'data_cad' => now(),
            'tipo' => 'F',
            'situacao' => 'P',
            'origem_gravacao' => 'U',
            'operacao' => 'I',
            'email' => $this->model->email,
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
            'idmun_nascimento' => $this->model->nacionalidade == Nacionalidade::BRASILEIRA ? $this->getCity($this->model->municipioNascimento) : null,
            'cpf' => $this->model->cpf ?: null,
            'nis_pis_pasep' => $this->model->nis ?: null,
            'pais_residencia' => (int) $this->model->paisResidencia,
            'zona_localizacao_censo' => (int) $this->model->localizacaoResidencia,
        ]);

        return $person;
    }

    /**
     * @param $name
     *
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
     *
     * @return LegacyStudent mixed
     */
    protected function getOrCreateStudent($person)
    {
        return LegacyStudent::firstOrCreate([
            'ref_idpes' => $person->getKey(),
        ], [
            'data_cadastro' => now(),
        ]);
    }

    /**
     * @param LegacyStudent $person
     */
    protected function createStudentInep($student)
    {
        if (empty($this->model->inepPessoa)) {
            return;
        }

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
     *
     * @return Employee
     */
    protected function getOrCreateEmployee($person)
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
    protected function createRace($person)
    {
        if ($person->individual->race()->count()) {
            return;
        }

        $race = $this->getOrCreateRace($person);
        $person->individual->race()->attach($race);
    }

    /**
     * @param $person
     *
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
    protected function createDeficiencies($person)
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
     * @param int          $educacendoDeficiency
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
        if ($individual->deficiency()
            ->where('deficiencia_educacenso', $educacendoDeficiency)
            ->exists()) {
            return;
        }

        $individual->deficiency()->attach($deficiency);
    }

    /**
     * @param $raca
     *
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
     *
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

    /**
     * @param $cityIbge
     *
     * @return LegacyCity|null
     */
    private function getCity($cityIbge)
    {
        if (empty($cityIbge)) {
            return null;
        }

        $legacyCity = LegacyCity::where('cod_ibge', $cityIbge)->first();

        return $legacyCity ? $legacyCity->getKey() : null;
    }

    /**
     * @param $countryIbge
     *
     * @return LegacyCountry|null
     */
    private function getCountry($countryIbge)
    {
        if (empty($countryIbge)) {
            return null;
        }

        $legacyCountry = LegacyCountry::where('cod_ibge', $countryIbge)->first();

        return $legacyCountry ? $legacyCountry->getKey() : null;
    }

    protected function createRecursosProvaInep(LegacyStudent $student)
    {
        $arrayRecursos = [];

        if ($this->model->recursoLedor) {
            $arrayRecursos[] = RecursosRealizacaoProvas::AUXILIO_LEDOR;
        }

        if ($this->model->recursoTranscricao) {
            $arrayRecursos[] = RecursosRealizacaoProvas::AUXILIO_TRANSCRICAO;
        }

        if ($this->model->recursoGuia) {
            $arrayRecursos[] = RecursosRealizacaoProvas::GUIA_INTERPRETE;
        }

        if ($this->model->recursoTradutor) {
            $arrayRecursos[] = RecursosRealizacaoProvas::TRADUTOR_INTERPRETE_DE_LIBRAS;
        }

        if ($this->model->recursoLeituraLabial) {
            $arrayRecursos[] = RecursosRealizacaoProvas::LEITURA_LABIAL;
        }

        if ($this->model->recursoProvaAmpliada) {
            $arrayRecursos[] = RecursosRealizacaoProvas::PROVA_AMPLIADA_FONTE_18;
        }

        if ($this->model->recursoProvaSuperampliada) {
            $arrayRecursos[] = RecursosRealizacaoProvas::PROVA_SUPERAMPLIADA_FONTE_24;
        }

        if ($this->model->recursoAudio) {
            $arrayRecursos[] = RecursosRealizacaoProvas::CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL;
        }

        if ($this->model->recursoLinguaPortuguesaSegundaLingua) {
            $arrayRecursos[] = RecursosRealizacaoProvas::PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS;
        }

        if ($this->model->recursoVideoLibras) {
            $arrayRecursos[] = RecursosRealizacaoProvas::PROVA_EM_VIDEO_EM_LIBRAS;
        }

        if ($this->model->recursoBraile) {
            $arrayRecursos[] = RecursosRealizacaoProvas::MATERIAL_DIDATICO_E_PROVA_EM_BRAILLE;
        }

        if ($this->model->recursoNenhum) {
            $arrayRecursos[] = RecursosRealizacaoProvas::NENHUM;
        }

        $student->recursos_prova_inep = $this->getPostgresIntegerArray($arrayRecursos);
        $student->save();
    }

    /**
     * @param $array
     *
     * @return string
     */
    private function getPostgresIntegerArray($array)
    {
        return '{' . implode(',', $array) . '}';
    }

    /**
     * @param LegacyStudent $student
     */
    protected function createCertidaoNascimento(LegacyStudent $student)
    {
        if (empty($this->model->certidaoNascimento)) {
            return;
        }

        LegacyDocument::updateOrCreate(
            ['idpes' => $student->person->getKey(),],
            [
                'certidao_nascimento' => $this->model->certidaoNascimento,
                'origem_gravacao' => 'U',
                'operacao' => 'I',
                'data_cad' => now(),
            ]
        );
    }

    /**
     * @param LegacyStudent $student
     */
    private function storeStudentData(LegacyStudent $student)
    {
        $this->createStudentInep($student);
        $this->createRecursosProvaInep($student);
        $this->createCertidaoNascimento($student);

        $student->justificativa_falta_documentacao = (int) $this->model->justificativaFaltaDocumentacao;
        $student->save();
    }

    /**
     * @param Employee $employee
     */
    protected function storeEmployeeData(Employee $employee)
    {
        $this->createEmployeeInep($employee);
        $this->createEscolaridade($employee);
        $this->createEmployeeGraduations($employee);
        $this->storeEmployeePostgraduate($employee);
        $this->storeEmployeeCourses($employee);

        $employee->tipo_ensino_medio_cursado = (int) $this->model->tipoEnsinoMedioCursado;
        $employee->save();
    }

    /**
     * @param Employee $employee
     */
    private function createEscolaridade(Employee $employee)
    {
        if ($employee->schoolingDegree()->count()) {
            return;
        }

        $schoolingDegree = LegacySchoolingDegree::firstOrCreate(
            ['idesco' => $this->model->escolaridade],
            [
                'descricao' => Escolaridade::getDescriptiveValues()[$this->model->escolaridade] ?? 'Escolaridade',
                'escolaridade' => $this->model->escolaridade
            ]
        );

        $employee->ref_idesco = $schoolingDegree->getKey();
        $employee->save();
    }

    /**
     * @param Employee $employee
     */
    private function createEmployeeGraduations(Employee $employee)
    {
        $arrayCursos = array_filter($this->model->formacaoCurso);
        $arrayInstituicoes = array_filter($this->model->formacaoInstituicao);
        $arrayAnosConclusao = array_filter($this->model->formacaoAnoConclusao);
        $arrayComponentes = array_filter($this->model->formacaoComponenteCurricular);

        if (empty($arrayCursos)) {
            return;
        }

        if ($employee->graduations->count()) {
            return;
        }

        foreach ($arrayCursos as $key => $curso) {
            $degree = EducacensoDegree::where('curso_id', $curso)->first();
            $institution = EducacensoInstitution::where('ies_id', $arrayInstituicoes[$key])->first();

            if (empty($degree) || empty($institution)) {
                continue;
            }

            EmployeeGraduation::create([
                'employee_id' => $employee->getKey(),
                'course_id' => $degree->getKey(),
                'completion_year' => $arrayAnosConclusao[$key] ?? null,
                'college_id' => $institution->getKey(),
                'discipline_id' => $arrayComponentes[$key] ?? null,
            ]);
        }
    }

    /**
     * @param Employee $employee
     */
    private function storeEmployeePostgraduate(Employee $employee)
    {
        $arrayPostGraduate = [];

        if ($this->model->posGraduacaoEspecializacao) {
            $arrayPostGraduate[] = PosGraduacao::ESPECIALIZACAO;
        }

        if ($this->model->posGraduacaoMestrado) {
            $arrayPostGraduate[] = PosGraduacao::MESTRADO;
        }

        if ($this->model->posGraduacaoDoutorado) {
            $arrayPostGraduate[] = PosGraduacao::DOUTORADO;
        }

        if ($this->model->posGraduacaoNaoPossui) {
            $arrayPostGraduate[] = PosGraduacao::NAO_POSSUI;
        }

        $employee->pos_graduacao = $this->getPostgresIntegerArray($arrayPostGraduate);
        $employee->save();
    }

    private function storeEmployeeCourses(Employee $employee)
    {
        $arrayCourses = [];

        if ($this->model->formacaoContinuadaCreche) {
            $arrayCourses[] = FormacaoContinuada::CRECHE;
        }

        if ($this->model->formacaoContinuadaPreEscola) {
            $arrayCourses[] = FormacaoContinuada::PRE_ESCOLA;
        }

        if ($this->model->formacaoContinuadaAnosIniciaisFundamental) {
            $arrayCourses[] = FormacaoContinuada::ANOS_INICIAIS;
        }

        if ($this->model->formacaoContinuadaAnosFinaisFundamental) {
            $arrayCourses[] = FormacaoContinuada::ANOS_FINAIS;
        }

        if ($this->model->formacaoContinuadaEnsinoMedio) {
            $arrayCourses[] = FormacaoContinuada::ENSINO_MEDIO;
        }

        if ($this->model->formacaoContinuadaEducacaoJovensAdultos) {
            $arrayCourses[] = FormacaoContinuada::EJA;
        }

        if ($this->model->formacaoContinuadaEducacaoEspecial) {
            $arrayCourses[] = FormacaoContinuada::EDUCACAO_ESPECIAL;
        }

        if ($this->model->formacaoContinuadaEducacaoIndigena) {
            $arrayCourses[] = FormacaoContinuada::EDUCACAO_INDIGENA;
        }

        if ($this->model->formacaoContinuadaEducacaoCampo) {
            $arrayCourses[] = FormacaoContinuada::EDUCACAO_DO_CAMPO;
        }

        if ($this->model->formacaoContinuadaEducacaoAmbiental) {
            $arrayCourses[] = FormacaoContinuada::EDUCACAO_AMBIENTAL;
        }

        if ($this->model->formacaoContinuadaEducacaoDireitosHumanos) {
            $arrayCourses[] = FormacaoContinuada::EDUCACAO_DIREITOS_HUMANOS;
        }

        if ($this->model->formacaoContinuadaGeneroDiversidadeSexual) {
            $arrayCourses[] = FormacaoContinuada::GENERO_DIVERSIDADE_SEXUAL;
        }

        if ($this->model->formacaoContinuadaDireitosCriancaAdolescente) {
            $arrayCourses[] = FormacaoContinuada::DIREITOS_CRIANCA_ADOLESCENTE;
        }

        if ($this->model->formacaoContinuadaEducacaoRelacoesEticoRaciais) {
            $arrayCourses[] = FormacaoContinuada::CRECHE;
        }

        if ($this->model->formacaoContinuadaEducacaoGestaoEscolar) {
            $arrayCourses[] = FormacaoContinuada::GESTAO_ESCOLAR;
        }

        if ($this->model->formacaoContinuadaEducacaoOutros) {
            $arrayCourses[] = FormacaoContinuada::OUTROS;
        }

        if ($this->model->formacaoContinuadaEducacaoNenhum) {
            $arrayCourses[] = FormacaoContinuada::NENHUM;
        }

        $employee->curso_formacao_continuada = $this->getPostgresIntegerArray($arrayCourses);
        $employee->save();
    }
}
