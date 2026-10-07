<?php

namespace Database\Factories;

use App\Models\LegacyCourse;
use App\Models\LegacyPeriod;
use App\Models\LegacyPhone;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App_Model_ZonaLocalizacao;
use Database\Factories\Concerns\ReleaseAbsencesForStudentsFactory;
use Database\Factories\Concerns\ReleaseScoresForStudentsFactory;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\Regulamentacao;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;
use iEducar\Modules\Educacenso\Model\SituacaoFuncionamento;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolFactory extends Factory
{
    protected $model = LegacySchool::class;

    public function definition(): array
    {
        return [
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'ref_cod_instituicao' => fn () => LegacyInstitutionFactory::new()->current(),
            'sigla' => $this->faker->asciify(),
            'data_cadastro' => now(),
            'ref_idpes' => fn () => LegacyOrganizationFactory::new()->create(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'ativo' => 1, // ativo
            'situacao_funcionamento' => SituacaoFuncionamento::EM_ATIVIDADE,
            'dependencia_administrativa' => DependenciaAdministrativaEscola::MUNICIPAL,
            'regulamentacao' => Regulamentacao::SIM,
            'zona_localizacao' => App_Model_ZonaLocalizacao::URBANA,
            'ref_idpes_gestor' => LegacyEmployeeFactory::new()->current(),
            'cargo_gestor' => SchoolManagerRole::DIRETOR,
            'nao_ha_funcionarios_para_funcoes' => true,
        ];
    }

    /**
     * Altera o nome da escola.
     */
    public function withName(string $name): static
    {
        $person = LegacyPersonFactory::new()->create([
            'nome' => $name,
            // Escola é pessoa jurídica, e a busca das telas filtra por tipo.
            'tipo' => 'J',
        ]);

        $organization = LegacyOrganizationFactory::new()->create([
            'idpes' => $person,
            'fantasia' => $name,
        ]);

        return $this->state([
            'ref_idpes' => $organization,
        ]);
    }

    /**
     * Adiciona telefone para a escola.
     */
    public function withPhone(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            LegacyPhoneFactory::new()->create([
                'idpes' => $school->person,
                'tipo' => 1,
            ]);
        });
    }

    /**
     * Preenche os dados de infraestrutura, equipamentos, pessoal e ensino que
     * o Censo Escolar pede, com valores que o cadastro de escola aceita.
     *
     * As colunas de múltipla escolha são vetores do Postgres, daí as chaves.
     */
    public function complete(): static
    {
        $staff = array_fill_keys([
            'qtd_secretario_escolar', 'qtd_auxiliar_administrativo', 'qtd_apoio_pedagogico', 'qtd_coordenador_turno',
            'qtd_tecnicos', 'qtd_bibliotecarios', 'qtd_segurancas', 'qtd_auxiliar_servicos_gerais',
            'qtd_agronomos_horticultores', 'qtd_nutricionistas', 'qtd_profissionais_preparacao', 'qtd_bombeiro',
            'qtd_psicologo', 'qtd_fonoaudiologo', 'qtd_vice_diretor', 'qtd_orientador_comunitario',
            'qtd_assistente_social', 'qtd_tradutor_interprete_libras_outro_ambiente', 'qtd_revisor_braile',
        ], 1);

        return $this->state($staff + [
            'nao_ha_funcionarios_para_funcoes' => false,
            'caracteristica_escolar' => 1,
            'orgao_vinculado_escola' => '{2}',
            'esfera_administrativa' => 3,
            'unidade_vinculada_outra_instituicao' => 0,
            'ato_criacao' => 'Decreto 1 de 1990',
            'ato_autorizativo' => 'Portaria 2 de 1991',
            'lei_conclusao_ensino_medio' => 'Lei 3 de 2000',
            'local_funcionamento' => '{3}',
            'condicao' => 1,
            'predio_compartilhado_outra_escola' => 0,
            'agua_potavel_consumo' => 1,
            'abastecimento_agua' => '{1,2}',
            'abastecimento_energia' => '{1}',
            'esgoto_sanitario' => '{1}',
            'destinacao_lixo' => '{1}',
            'tratamento_lixo' => '{2}',
            'alimentacao_escolar_alunos' => 1,
            'compartilha_espacos_atividades_integracao' => 0,
            'usa_espacos_equipamentos_atividades_regulares' => 0,
            'possui_dependencias' => 1,
            'salas_gerais' => '{1}',
            'salas_funcionais' => '{1}',
            'banheiros' => '{1}',
            'laboratorios' => '{1}',
            'salas_atividades' => '{1}',
            'dormitorios' => '{1}',
            'areas_externas' => '{1}',
            'recursos_acessibilidade' => '{2}',
            'numero_salas_utilizadas_dentro_predio' => 8,
            'numero_salas_utilizadas_fora_predio' => 2,
            'numero_salas_climatizadas' => 4,
            'numero_salas_acessibilidade' => 3,
            'numero_salas_cantinho_leitura' => 1,
            'equipamentos' => '{1}',
            'uso_internet' => '{1}',
            'acesso_internet' => 1,
            'rede_local' => '{3}',
            'equipamentos_acesso_internet' => '{1}',
            'quantidade_computadores_alunos_mesa' => 10,
            'quantidade_computadores_alunos_portateis' => 5,
            'quantidade_computadores_alunos_tablets' => 3,
            'lousas_digitais' => 1,
            'televisoes' => 2,
            'dvds' => 1,
            'aparelhos_de_som' => 1,
            'projetores_digitais' => 2,
            'atendimento_aee' => 1,
            'fundamental_ciclo' => 0,
            'organizacao_ensino' => '{1}',
            'instrumentos_pedagogicos' => '{1}',
            'orgaos_colegiados' => '{2}',
            'exame_selecao_ingresso' => 0,
            'reserva_vagas_cotas' => '{2}',
            'acao_area_ambiental' => 1,
            'acoes_area_ambiental' => '{2}',
            'projeto_politico_pedagogico' => 1,
            'lingua_ministrada' => 1,
            'codigo_lingua_indigena' => '{2}',
            'poder_publico_parceria_convenio' => '{1}',
            'formas_contratacao_parceria_escola_secretaria_estadual' => '{5}',
            'formas_contratacao_parceria_escola_secretaria_municipal' => '{5}',
        ]);
    }

    /**
     * Escola da rede privada, com a categoria e a mantenedora que o Censo exige
     * dela.
     */
    public function private(): static
    {
        return $this->state([
            'dependencia_administrativa' => DependenciaAdministrativaEscola::PRIVADA,
            'categoria_escola_privada' => 1,
            'mantenedora_escola_privada' => '{1}',
            'cnpj_mantenedora_principal' => '11222333000181',
        ]);
    }

    /**
     * Adiciona os quatro telefones que o cadastro de escola aceita: os dois
     * fixos, o celular e o fax.
     */
    public function withPhones(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            $phones = [
                LegacyPhone::TYPE_LANDLINE => '34221001',
                LegacyPhone::TYPE_MOBILE => '34221002',
                LegacyPhone::TYPE_MOBILE_ALT => '991221003',
                LegacyPhone::TYPE_FAX => '34221004',
            ];

            foreach ($phones as $type => $number) {
                LegacyPhoneFactory::new()->create([
                    'idpes' => $school->ref_idpes,
                    'tipo' => $type,
                    'ddd' => '48',
                    'fone' => $number,
                ]);
            }
        });
    }

    /**
     * Adiciona o endereço da escola, que pertence à pessoa jurídica dela.
     */
    public function withAddress(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            PersonHasPlaceFactory::new()->create([
                'person_id' => $school->ref_idpes,
                'place_id' => PlaceFactory::new()->create(),
            ]);
        });
    }

    /**
     * Adiciona o código INEP da escola.
     */
    public function withInep(string $code = '42000001'): static
    {
        return $this->afterCreating(function (LegacySchool $school) use ($code) {
            SchoolInepFactory::new()->create([
                'cod_escola' => $school,
                'cod_escola_inep' => $code,
            ]);
        });
    }

    /**
     * Adiciona espaços escolares à escola.
     */
    public function withSpaces(int $count = 2): static
    {
        return $this->afterCreating(function (LegacySchool $school) use ($count) {
            SchoolSpaceFactory::new()->count($count)->create([
                'school_id' => $school,
            ]);
        });
    }

    /**
     * Define o usuário "admin" como diretor da escola.
     */
    public function withAdminAsDirector(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            SchoolManagerFactory::new()->create([
                'employee_id' => LegacyEmployeeFactory::new()->current(),
                'school_id' => $school,
                'chief' => true, // Gestor Principal
            ]);
        });
    }

    /**
     * Vincula o curso, suas séres e disciplinas à escola.
     */
    public function withCourse(LegacyCourse $course): static
    {
        return $this->afterCreating(function (LegacySchool $school) use ($course) {
            $schoolCourse = LegacySchoolCourseFactory::new()->create([
                'ref_cod_escola' => $school,
                'ref_cod_curso' => $course,
            ]);

            $course->grades->each(fn ($grade) => LegacySchoolGradeFactory::new()
                ->withDisciplines()
                ->create([
                    'ref_cod_escola' => $school,
                    'ref_cod_serie' => $grade,
                    'anos_letivos' => $schoolCourse->anos_letivos,
                ]));
        });
    }

    /**
     * Adiciona uma turma para o turno e para cada série da escola.
     */
    public function withClassroomsForEachGrade(LegacyCourse $course, LegacyPeriod $period): static
    {
        return $this->afterCreating(function (LegacySchool $school) use ($course, $period) {
            $course->grades->each(fn ($grade) => LegacySchoolClassFactory::new()
                ->create([
                    'nm_turma' => $grade->name . ' ' . $period->name,
                    'sgl_turma' => mb_substr($grade->name, 0, 1),
                    'turma_turno_id' => $period,
                    'ref_ref_cod_escola' => $school,
                    'ref_ref_cod_serie' => $grade,
                    'ref_cod_curso' => $course,
                    'max_aluno' => 20,
                ]));
        });
    }

    /**
     * Adiciona alunos para cada turma da escola.
     */
    public function withStudentsForEachClassrooms(int $count = 1): static
    {
        return $this->afterCreating(function (LegacySchool $school) use ($count) {
            $school->schoolClasses->each(function (LegacySchoolClass $schoolClass) use ($count) {
                for ($i = 0; $i < $count; $i++) {
                    $random = $this->faker->randomElement([0, 1, -1]);

                    $individual = LegacyIndividualFactory::new()
                        ->father()
                        ->mother()
                        ->withAge($schoolClass->grade->idade_inicial + $random)
                        ->create();

                    $student = LegacyStudentFactory::new()->create([
                        'ref_idpes' => $individual,
                    ]);

                    LegacyRegistrationFactory::new()
                        ->withStudent($student)
                        ->withEnrollment($schoolClass)
                        ->create();
                }
            });
        });
    }

    /**
     * Lança notas para cada aluno da escola.
     */
    public function withScoresForEachStudent(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            ReleaseScoresForStudentsFactory::fromSchool($school);
        });
    }

    /**
     * Lança faltas para cada aluno da escola.
     */
    public function withAbsencesForEachStudent(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            ReleaseAbsencesForStudentsFactory::fromSchool($school);
        });
    }

    /**
     * Inicia um ano letivo do tipo "semestre".
     */
    public function withSemesterAsStageType(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            $semester = LegacyStageTypeFactory::new()->semester();

            LegacySchoolAcademicYearFactory::new()
                ->withStageType($semester)
                ->withSchool($school)
                ->create();
        });
    }

    /**
     * Inicia um ano letivo do tipo "bimestre".
     */
    public function withBimonthlyAsStageType(): static
    {
        return $this->afterCreating(function (LegacySchool $school) {
            $bimonthly = LegacyStageTypeFactory::new()->bimonthly();

            LegacySchoolAcademicYearFactory::new()
                ->withStageType($bimonthly)
                ->withSchool($school)
                ->create();
        });
    }
}
