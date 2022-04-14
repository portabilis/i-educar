<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro20;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\LegacyCourse;
use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;
use App\Models\LegacyInstitution;
use App\Models\LegacyKnowledgeArea;
use App\Models\LegacyLevel;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassType;
use App\Models\LegacySchoolCourse;
use App\Models\LegacySchoolGrade;
use App\Models\LegacySchoolGradeDiscipline;
use App\Models\SchoolClassInep;
use App\Models\SchoolInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\Services\SchoolClass\PeriodService;
use App\User;
use Exception;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;

class Registro20Import implements RegistroImportInterface
{
    /**
     * @var Registro20
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
     * @var \Illuminate\Contracts\Foundation\Application
     */

    /**
     * @var LegacyInstitution
     */
    private $institution;

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
        $this->year = $year;
        $this->institution = app(LegacyInstitution::class);

        $schoolInep = $this->getSchool();

        if (empty($schoolInep)) {
            return;
        }

        /** @var LegacySchool $school */
        $school = $schoolInep->school;
        $model = $this->model;

        $schoolClass = $this->getSchoolClass();

        if (!empty($schoolClass)) {
            return;
        }

        $schoolClassType = $this->getOrCreateSchoolClassType();
        $course = $this->getOrCreateCourse($school);
        $level = $this->getOrCreateLevel($school, $course);

        $horaInicial = sprintf('%02d:%02d:00', intval($model->horaInicial), intval($model->horaInicialMinuto));
        $horaFinal = sprintf('%02d:%02d:00', intval($model->horaFinal), intval($model->horaFinalMinuto));

        $schoolClass = LegacySchoolClass::create(
            [
                'ref_ref_cod_escola' => $school->getKey(),
                'ref_ref_cod_serie' => $level->getKey(),
                'ref_cod_curso' => $course->getKey(),
                'ref_cod_turma_tipo' => $schoolClassType->getKey(),
                'ref_usuario_cad' => $this->user->getKey(),
                'nm_turma' => $model->nomeTurma,
                'tipo_mediacao_didatico_pedagogico' => $model->tipoMediacaoDidaticoPedagogico,
                'hora_inicial' => $horaInicial,
                'hora_final' => $horaFinal,
                'dias_semana' => $this->getArrayDaysWeek(),
                'tipo_atendimento' => $this->getTipoAtendimento(),
                'atividades_complementares' => $this->getArrayAtividadesComplementares(),
                'local_funcionamento_diferenciado' => (int) $model->localFuncionamentoDiferenciado,
                'etapa_educacenso' => (int) $model->etapaEducacenso,
                'max_aluno' => 99,
                'ativo' => 1,
                'multiseriada' => 0,
                'visivel' => true,
                'tipo_boletim' => 1,
                'ano' => $this->year,
                'sgl_turma' => '',
                'data_cadastro' => now(),
                'ref_cod_instituicao' => $this->institution->id,
                'turma_turno_id' => $this->getTurno($horaInicial, $horaFinal),
            ]
        );

        $this->createInepTurma($schoolClass);
        $this->createDisciplines($schoolClass, $level, $school);
    }

    private function createDisciplines($schoolClass, $level, $school)
    {
        foreach ($this->model->componentes as $discipline) {
            $discipline = $this->createDiscipline($discipline);
            $this->createDisciplineAcademicYear($discipline, $level);
            $this->createSchoolGradeDiscipline($school, $level, $discipline);
        }
    }

    /**
     * @param $disciplineId
     *
     * @return LegacyDiscipline
     */
    private function createDiscipline($disciplineId)
    {
        $discipline = LegacyDiscipline::where('codigo_educacenso', $disciplineId)->first();

        if (!empty($discipline)) {
            return $discipline;
        }

        $knowledgeArea = $this->getOrCreateKnowledgeArea();

        $name = self::getComponentes()[$disciplineId] ?? 'Migração';

        return LegacyDiscipline::create([
            'instituicao_id' => $this->institution->id,
            'area_conhecimento_id' => $knowledgeArea->getKey(),
            'nome' => $name,
            'abreviatura' => mb_substr($name, 0, 3, 'UTF-8'),
            'codigo_educacenso' => $disciplineId,
            'tipo_base' => 1,
        ]);
    }

    private function createDisciplineAcademicYear($discipline, $level)
    {
        if (LegacyDisciplineAcademicYear::where('componente_curricular_id', $discipline->getKey())
            ->where('ano_escolar_id', $level->getKey())
            ->exists()) {
            return;
        }

        LegacyDisciplineAcademicYear::create([
            'componente_curricular_id' => $discipline->getKey(),
            'ano_escolar_id' => $level->getKey(),
            'anos_letivos' => '{' . $this->year . '}',
        ]);
    }

    private function createSchoolGradeDiscipline($school, $level, $discipline)
    {
        if (LegacySchoolGradeDiscipline::where('ref_ref_cod_escola', $school->getKey())
            ->where('ref_ref_cod_serie', $level->getKey())
            ->where('ref_cod_disciplina', $discipline->getKey())
            ->where('ativo', 1)
            ->exists()) {
            return;
        }

        LegacySchoolGradeDiscipline::create([
            'ref_ref_cod_serie' => $level->getKey(),
            'ref_ref_cod_escola' => $school->getKey(),
            'ref_cod_disciplina' => $discipline->getKey(),
            'ativo' => 1,
            'anos_letivos' => '{' . $this->year . '}',
        ]);
    }

    private function createSchoolClassDiscipline(LegacySchoolClass $schoolClass, LegacyDiscipline $discipline)
    {
        if ($schoolClass->disciplines()
            ->where('componente_curricular_id', $discipline->getKey())
            ->exists()) {
            return;
        }

        $schoolClass->disciplines()->attach($discipline->getKey(), [
            'ano_escolar_id' => $schoolClass->grade->getKey(),
            'escola_id' => $schoolClass->school->getKey(),
        ]);
    }

    private function getOrCreateKnowledgeArea()
    {
        $knowledgeArea = LegacyKnowledgeArea::first();

        if (!empty($knowledgeArea)) {
            return $knowledgeArea;
        }

        return LegacyKnowledgeArea::create([
            'instituicao_id' => $this->institution->id,
            'nome' => 'Migração',
        ]);
    }

    /**
     * @return string
     */
    private function getArrayDaysWeek()
    {
        $arrayDaysWeek = [];

        if ($this->model->diaSemanaDomingo) {
            $arrayDaysWeek[] = 1;
        }

        if ($this->model->diaSemanaSegunda) {
            $arrayDaysWeek[] = 2;
        }

        if ($this->model->diaSemanaTerca) {
            $arrayDaysWeek[] = 3;
        }

        if ($this->model->diaSemanaQuarta) {
            $arrayDaysWeek[] = 4;
        }

        if ($this->model->diaSemanaQuinta) {
            $arrayDaysWeek[] = 5;
        }

        if ($this->model->diaSemanaSexta) {
            $arrayDaysWeek[] = 6;
        }

        if ($this->model->diaSemanaSabado) {
            $arrayDaysWeek[] = 7;
        }

        return $this->getPostgresIntegerArray($arrayDaysWeek);
    }

    /**
     * @return SchoolInep|null
     */
    private function getSchool()
    {
        return SchoolInep::where('cod_escola_inep', $this->model->codigoEscolaInep)->first();
    }

    /**
     * @return SchoolClassInep|null
     */
    private function getSchoolClass()
    {
        if (empty($this->model->inepTurma)) {
            return;
        }

        return SchoolClassInep::where('cod_turma_inep', $this->model->inepTurma)->first();
    }

    /**
     * @param $arrayColumns
     *
     * @return Registro10|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro20();
        $registro->hydrateModel($arrayColumns);

        return $registro;
    }

    /**
     * @return LegacySchoolClassType
     */
    private function getOrCreateSchoolClassType()
    {
        $schoolClassType = LegacySchoolClassType::first();

        if (!empty($schoolClassType)) {
            return $schoolClassType;
        }

        return LegacySchoolClassType::create([
            'ref_usuario_cad' => $this->user->id,
            'nm_tipo' => 'Regular',
            'sgl_tipo' => 'Reg',
            'data_cadastro' => now(),
            'ref_cod_instituicao' => $this->institution->id,
        ]);
    }

    /**
     * @param LegacySchool $school
     *
     * @return LegacyCourse
     */
    private function getOrCreateCourse(LegacySchool $school)
    {
        $educationLevel = $this->getOrCreateEducationLevel();
        $educationType = $this->getOrCreateEducationType();

        $courseData = $this->getDataByEtapaEducacenso($this->model->etapaEducacenso);

        if ($this->model->tipoAtendimentoAtividadeComplementar) {
            $courseData = $this->getDataAtividadeCompementar();
        }

        if ($this->model->tipoAtendimentoAee) {
            $courseData = $this->getDataAee();
        }

        if (empty($courseData)) {
            throw new Exception('Não foi possível encontrar os dados do curso');
        }

        $course = LegacyCourse::where('nm_curso', 'ilike', utf8_encode($courseData['curso']))->first();

        if (empty($course)) {
            $course = $this->createCourse($educationLevel, $educationType, $courseData);
        }

        $schoolCourse = LegacySchoolCourse::where('ref_cod_escola', $school->getKey())
            ->where('ref_cod_curso', $course->getKey())
            ->first();

        if (!empty($schoolCourse)) {
            return $course;
        }

        LegacySchoolCourse::create([
            'ref_cod_escola' => $school->getKey(),
            'ref_cod_curso' => $course->getKey(),
            'ref_usuario_cad' => $this->user->id,
            'data_cadastro' => now(),
            'ativo' => 1,
            'anos_letivos' => '{' . $this->year . '}',
        ]);

        return $course;
    }

    /**
     * @return LegacyEducationLevel
     */
    private function getOrCreateEducationLevel()
    {
        $educationLevel = LegacyEducationLevel::where('nm_nivel', 'ilike', 'Ano')->first();

        if (!empty($educationLevel)) {
            return $educationLevel;
        }

        return LegacyEducationLevel::create([
            'ref_usuario_cad' => $this->user->id,
            'nm_nivel' => 'Ano',
            'data_cadastro' => now(),
            'ref_cod_instituicao' => $this->institution->id,
        ]);
    }

    /**
     * @return LegacyEducationType
     */
    private function getOrCreateEducationType()
    {
        $educationType = LegacyEducationType::first();

        if (!empty($educationType)) {
            return $educationType;
        }

        return LegacyEducationType::create([
            'ref_usuario_cad' => $this->user->id,
            'nm_tipo' => 'Padrão',
            'data_cadastro' => now(),
            'ref_cod_instituicao' => $this->institution->id,
        ]);
    }

    /**
     * @param LegacySchool $school
     * @param LegacyCourse $course
     *
     * @return LegacyLevel
     */
    private function getOrCreateLevel(LegacySchool $school, LegacyCourse $course)
    {
        $levelData = $this->getDataByEtapaEducacenso($this->model->etapaEducacenso);

        if ($this->model->tipoAtendimentoAtividadeComplementar) {
            $levelData = $this->getDataAtividadeCompementar();
        }

        if ($this->model->tipoAtendimentoAee) {
            $levelData = $this->getDataAee();
        }

        $level = LegacyLevel::where('ref_cod_curso', $course->getKey())
            ->where('etapa_curso', $levelData['etapa'])
            ->first();

        if (empty($level)) {
            $level = $this->createLevel($levelData, $course);
        }

        $schoolGrade = LegacySchoolGrade::where('ref_cod_escola', $school->getKey())
            ->where('ref_cod_serie', $level->getKey())
            ->first();

        if (!empty($schoolGrade)) {
            return $level;
        }

        LegacySchoolGrade::create([
            'ref_cod_escola' => $school->getKey(),
            'ref_cod_serie' => $level->getKey(),
            'ref_usuario_cad' => $this->user->id,
            'data_cadastro' => now(),
            'anos_letivos' => '{' . $this->year . '}',
            'hora_inicial' => '07:30:00',
            'hora_final' => '12:00:00',
            'hora_inicio_intervalo' => '09:50:00',
            'hora_fim_intervalo' => '10:20:00',
        ]);

        return $level;
    }

    public static function getComponentes()
    {
        return [
            1 => 'Química',
            2 => 'Física',
            3 => 'Matemática',
            4 => 'Biologia',
            5 => 'Ciências',
            6 => 'Língua/Literatura portuguesa',
            7 => 'Língua/Literatura estrangeira - Inglês',
            8 => 'Língua/Literatura estrangeira - Espanhol',
            9 => 'Língua/Literatura estrangeira - Outra',
            10 => 'Artes (educação artística, teatro, dança, música, artes plásticas e outras)',
            11 => 'Educação física',
            12 => 'História',
            13 => 'Geografia',
            14 => 'Filosofia',
            16 => 'Informática/Computação',
            17 => 'Disciplinas dos Cursos Técnicos Profissionais;',
            23 => 'LIBRAS',
            25 => 'Disciplinas pedagógicas',
            26 => 'Ensino religioso',
            27 => 'Língua indígena',
            28 => 'Estudos sociais',
            29 => 'Sociologia',
            30 => 'Língua/Literatura estrangeira - Francês',
            31 => 'Língua Portuguesa como Segunda Língua',
            32 => 'Estágio Curricular Supervisionado',
            99 => 'Outras disciplinas'
        ];
    }

    /**
     * @return array
     */
    public function getDataAtividadeCompementar()
    {
        return [
            'curso' => 'Atividade complementar',
            'serie' => 'Atividade complementar',
            'etapa' => 1,
            'etapas' => 1,
            'nivel' => 'Outros'
        ];
    }

    /**
     * @return array
     */
    public function getDataAee()
    {
        return [
            'curso' => 'Atendimento educacional especializado (AEE)',
            'serie' => 'Atendimento educacional especializado (AEE)',
            'etapa' => 1,
            'etapas' => 1,
            'nivel' => 'Outros'
        ];
    }

    /**
     * @param integer $etapa
     *
     * @return array
     */
    public function getDataByEtapaEducacenso($etapa)
    {
        $arrayData = [
            1 => [
                'curso' => 'Educação Infantil',
                'serie' => 'Creche (0 a 3 anos)',
                'etapa' => 1,
                'etapas' => 3,
                'nivel' => 'Infantil'
            ],
            2 => [
                'curso' => 'Educação Infantil',
                'serie' => 'Pré-escola (4 e 5 anos)',
                'etapa' => 2,
                'etapas' => 3,
                'nivel' => 'Infantil'
            ],
            3 => [
                'curso' => 'Educação Infantil',
                'serie' => 'Unificada (0 a 5 anos)',
                'etapa' => 3,
                'etapas' => 3,
                'nivel' => 'Infantil'
            ],
            4 => [
                'curso' => 'Ensino Fundamental de 8 anos',
                'serie' => '1ª Série',
                'etapa' => 1,
                'etapas' => 8,
                'nivel' => 'Fundamental'
            ],
            5 => [
                'curso' => 'Ensino Fundamental de 8 anos',
                'serie' => '2ª Série',
                'etapa' => 2,
                'etapas' => 8,
                'nivel' => 'Fundamental'
            ],
            6 => [
                'curso' => 'Ensino Fundamental de 8 anos',
                'serie' => '3ª Série',
                'etapa' => 3,
                'etapas' => 8,
                'nivel' => 'Fundamental'
            ],
            7 => [
                'curso' => 'Ensino Fundamental de 8 anos',
                'serie' => '4ª Série',
                'etapa' => 4,
                'etapas' => 8,
                'nivel' => 'Fundamental'
            ],
            8 => [
                'curso' => 'Ensino Fundamental de 8 anos',
                'serie' => '5ª Série',
                'etapa' => 5,
                'etapas' => 8,
                'nivel' => 'Fundamental'
            ],
            9 => [
                'curso' => 'Ensino Fundamental de 8 anos',
                'serie' => '6ª Série',
                'etapa' => 6,
                'etapas' => 8,
                'nivel' => 'Fundamental'
            ],
            10 => [
                'curso' => 'Ensino Fundamental de 8 anos',
                'serie' => '7ª Série',
                'etapa' => 7,
                'etapas' => 8,
                'nivel' => 'Fundamental'
            ],
            11 => [
                'curso' => 'Ensino Fundamental de 8 anos',
                'serie' => '8ª Série',
                'etapa' => 8,
                'etapas' => 8,
                'nivel' => 'Fundamental'
            ],
            12 => [
                'curso' => 'Ensino Fundamental de 8 anos - Multi',
                'serie' => 'Multi',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'Fundamental'
            ],
            13 => [
                'curso' => 'Ensino Fundamental de 8 anos - Correção de Fluxo',
                'serie' => 'Correção de Fluxo',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'Fundamental'
            ],
            14 => [
                'curso' => 'Ensino Fundamental de 9 anos',
                'serie' => '1º Ano',
                'etapa' => 1,
                'etapas' => 9,
                'nivel' => 'Fundamental'
            ],
            15 => [
                'curso' => 'Ensino Fundamental de 9 anos',
                'serie' => '2º Ano',
                'etapa' => 2,
                'etapas' => 9,
                'nivel' => 'Fundamental'
            ],
            16 => [
                'curso' => 'Ensino Fundamental de 9 anos',
                'serie' => '3º Ano',
                'etapa' => 3,
                'etapas' => 9,
                'nivel' => 'Fundamental'
            ],
            17 => [
                'curso' => 'Ensino Fundamental de 9 anos',
                'serie' => '4º Ano',
                'etapa' => 4,
                'etapas' => 9,
                'nivel' => 'Fundamental'
            ],
            18 => [
                'curso' => 'Ensino Fundamental de 9 anos',
                'serie' => '5º Ano',
                'etapa' => 5,
                'etapas' => 9,
                'nivel' => 'Fundamental'
            ],
            19 => [
                'curso' => 'Ensino Fundamental de 9 anos',
                'serie' => '6º Ano',
                'etapa' => 6,
                'etapas' => 9,
                'nivel' => 'Fundamental'
            ],
            20 => [
                'curso' => 'Ensino Fundamental de 9 anos',
                'serie' => '7º Ano',
                'etapa' => 7,
                'etapas' => 9,
                'nivel' => 'Fundamental'
            ],
            21 => [
                'curso' => 'Ensino Fundamental de 9 anos',
                'serie' => '8º Ano',
                'etapa' => 8,
                'etapas' => 9,
                'nivel' => 'Fundamental'
            ],
            22 => [
                'curso' => 'Ensino Fundamental de 9 anos - Multi',
                'serie' => 'Multi',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'Fundamental'
            ],
            41 => [
                'curso' => 'Ensino Fundamental de 9 anos',
                'serie' => '9º Ano',
                'etapa' => 9,
                'etapas' => 9,
                'nivel' => 'Fundamental'
            ],
            23 => [
                'curso' => 'Ensino Fundamental de 9 anos - Correção de Fluxo',
                'serie' => 'Correção de Fluxo',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'Fundamental'
            ],
            24 => [
                'curso' => 'Ensino Fundamental de 8 e 9 anos',
                'serie' => 'Multi 8 e 9 anos',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'Fundamental'
            ],
            56 => [
                'curso' => 'Educação Infantil e Ensino Fundamental (8 e 9 anos)',
                'serie' => 'Multietapa',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'Fundamental'
            ],
            25 => [
                'curso' => 'Ensino Médio',
                'serie' => '1ª Série',
                'etapa' => 1,
                'etapas' => 4,
                'nivel' => 'Médio'
            ],
            26 => [
                'curso' => 'Ensino Médio',
                'serie' => '2ª Série',
                'etapa' => 2,
                'etapas' => 4,
                'nivel' => 'Médio'
            ],
            27 => [
                'curso' => 'Ensino Médio',
                'serie' => '3ª Série',
                'etapa' => 3,
                'etapas' => 4,
                'nivel' => 'Médio'
            ],
            28 => [
                'curso' => 'Ensino Médio',
                'serie' => '4ª Série',
                'etapa' => 4,
                'etapas' => 4,
                'nivel' => 'Médio'
            ],
            29 => [
                'curso' => 'Ensino Médio Não-seriado',
                'serie' => 'Não Seriada',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'Médio'
            ],
            30 => [
                'curso' => 'Ensino Médio Integrado',
                'serie' => 'Integrado 1ª Série',
                'etapa' => 1,
                'etapas' => 4,
                'nivel' => 'Médio'
            ],
            31 => [
                'curso' => 'Ensino Médio Integrado',
                'serie' => 'Integrado 2ª Série',
                'etapa' => 2,
                'etapas' => 4,
                'nivel' => 'Médio'
            ],
            32 => [
                'curso' => 'Ensino Médio Integrado',
                'serie' => 'Integrado 3ª Série',
                'etapa' => 3,
                'etapas' => 4,
                'nivel' => 'Médio'
            ],
            33 => [
                'curso' => 'Ensino Médio Integrado',
                'serie' => 'Integrado 4ª Série',
                'etapa' => 4,
                'etapas' => 4,
                'nivel' => 'Médio'
            ],
            34 => ['curso' => 'Ensino Médio Integrado Não-Seriado',
                'serie' => 'Integrado Não Seriada',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'Médio'
            ],
            74 => ['curso' => 'Ensino Médio Integrado Não-Seriado',
                'serie' => 'EJA',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'Médio'
            ],
            35 => [
                'curso' => 'Ensino Médio - Magistério',
                'serie' => 'Normal/Magistério 1ª Série',
                'etapa' => 1,
                'etapas' => 4,
                'nivel' => 'Médio'
            ],
            36 => [
                'curso' => 'Ensino Médio - Magistério',
                'serie' => 'Normal/Magistério 2ª Série',
                'etapa' => 2,
                'etapas' => 4,
                'nivel' => 'Médio'
            ],
            37 => [
                'curso' => 'Ensino Médio - Magistério',
                'serie' => 'Normal/Magistério 3ª Série',
                'etapa' => 3,
                'etapas' => 4,
                'nivel' => 'Médio'
            ],
            38 => [
                'curso' => 'Ensino Médio - Magistério',
                'serie' => 'Normal/Magistério 4ª Série',
                'etapa' => 4,
                'etapas' => 4,
                'nivel' => 'Médio'
            ],
            39 => [
                'curso' => 'Educação Profissional (Concomitante)',
                'serie' => 'Não-seriado',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'Médio'
            ],
            40 => [
                'curso' => 'Educação Profissional (Subseqüente)',
                'serie' => 'Não-seriado',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'Médio'
            ],
            64 => [
                'curso' => 'Educação Profissional (Subseqüente)',
                'serie' => 'Curso técnico misto',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'Médio'
            ],
            69 => [
                'curso' => 'EJA - Ensino fundamental',
                'serie' => 'Anos iniciais',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'EJA'
            ],
            70 => [
                'curso' => 'EJA - Ensino fundamental',
                'serie' => 'Anos finais',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'EJA'
            ],
            72 => [
                'curso' => 'EJA - Ensino fundamental',
                'serie' => 'Anos iniciais e anos finais',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'EJA'
            ],
            65 => [
                'curso' => 'EJA - Ensino fundamental',
                'serie' => 'Projovem Urbano',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'EJA'
            ],
            71 => [
                'curso' => 'EJA - Ensino médio',
                'serie' => 'Ensino médio',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'EJA'
            ],
            67 => [
                'curso' => 'EJA - Ensino médio',
                'serie' => 'Ensino médio',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'FIC'
            ],
            73 => [
                'curso' => 'EJA - Ensino médio',
                'serie' => 'Ensino médio',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'FIC'
            ],
            68 => [
                'curso' => 'EJA - Ensino médio',
                'serie' => 'Ensino médio',
                'etapa' => 1,
                'etapas' => 1,
                'nivel' => 'FIC'
            ]
        ];

        return $arrayData[$etapa] ?? null;
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
     * @return int|null
     */
    private function getTipoAtendimento()
    {
        if ($this->model->tipoAtendimentoEscolarizacao) {
            return TipoAtendimentoTurma::ESCOLARIZACAO;
        }

        if ($this->model->tipoAtendimentoAtividadeComplementar) {
            return TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR;
        }

        if ($this->model->tipoAtendimentoAee) {
            return TipoAtendimentoTurma::AEE;
        }

        return null;
    }

    /**
     * @return string
     */
    private function getArrayAtividadesComplementares()
    {
        $arrayAtividades[] = $this->model->tipoAtividadeComplementar1;
        $arrayAtividades[] = $this->model->tipoAtividadeComplementar2;
        $arrayAtividades[] = $this->model->tipoAtividadeComplementar3;
        $arrayAtividades[] = $this->model->tipoAtividadeComplementar4;
        $arrayAtividades[] = $this->model->tipoAtividadeComplementar5;
        $arrayAtividades[] = $this->model->tipoAtividadeComplementar6;

        return $this->getPostgresIntegerArray(array_filter($arrayAtividades));
    }

    /**
     * @param LegacySchoolClass $schoolClass
     */
    private function createInepTurma(LegacySchoolClass $schoolClass)
    {
        if (empty($this->model->inepTurma)) {
            return;
        }

        SchoolClassInep::create([
            'cod_turma' => $schoolClass->getKey(),
            'cod_turma_inep' => $this->model->inepTurma,
            'created_at' => now(),
        ]);
    }

    /**
     * @param              $levelData
     * @param LegacyCourse $course
     *
     * @return
     */
    private function createLevel($levelData, $course)
    {
        return LegacyLevel::create([
            'nm_serie' => $levelData['serie'],
            'ref_usuario_cad' => $this->user->id,
            'ref_cod_curso' => $course->getKey(),
            'etapa_curso' => $levelData['etapa'],
            'carga_horaria' => 800,
            'dias_letivos' => 200,
            'data_cadastro' => now(),
            'concluinte' => ($levelData['etapa'] == $levelData['etapas']) ? 1 : 0,
            'ativo' => 1,
            'intervalo' => 1,
        ]);
    }

    /**
     * @param LegacyEducationLevel $educationLevel
     * @param LegacyEducationType  $educationType
     * @param array                $courseData
     *
     * @return LegacyCourse
     */
    private function createCourse($educationLevel, $educationType, $courseData)
    {
        return LegacyCourse::create([
            'ref_usuario_cad' => $this->user->id,
            'ref_cod_nivel_ensino' => $educationLevel->getKey(),
            'ref_cod_tipo_ensino' => $educationType->getKey(),
            'nm_curso' => utf8_encode($courseData['curso']),
            'sgl_curso' => utf8_encode(substr($courseData['curso'], 0, 15)),
            'qtd_etapas' => $courseData['etapas'],
            'carga_horaria' => 800 * $courseData['etapas'],
            'data_cadastro' => now(),
            'ref_cod_instituicao' => $this->institution->id,
            'ativo' => 1,
            'modalidade_curso' => $this->model->modalidadeCurso,
            'padrao_ano_escolar' => 1,
            'multi_seriado' => 1,
        ]);
    }

    /**
     * Retorna o turno da turma se houver horário de início e término
     *
     * @param $horaInicial
     * @param $horaFinal
     *
     * @throws Exception
     *
     * @return string|null
     */
    private function getTurno($horaInicial, $horaFinal)
    {
        if (empty($horaInicial) || empty($horaFinal)) {
            return null;
        }

        $service = new PeriodService();

        return $service->getPeriodByTime($horaInicial, $horaFinal);
    }
}
