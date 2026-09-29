<?php

namespace Tests\Feature\Security;

use App\Listeners\LoginLegacySession;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App\Models\LegacyUserType;
use App\Process;
use App\User;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserSchoolFactory;
use Database\Factories\LegacyUserTypeFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WebRoutesAuthorizationTest extends TestCase
{
    use ActivatesSystem, DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->activateSystem();
    }

    private function schoolUser(?int $process = null, ?LegacySchool $school = null): User
    {
        $factory = LegacyUserFactory::new();

        if ($process) {
            $factory = $factory->withAccess($process);
        }

        $user = $factory->create([
            'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_SCHOOLING]),
        ]);

        LegacyUserSchoolFactory::new()->create([
            'ref_cod_usuario' => $user->getKey(),
            'ref_cod_escola' => $school ?? LegacySchoolFactory::new()->create(),
        ]);

        $user = User::query()->findOrFail($user->getKey());
        $user->employee->update(['data_troca_senha' => now(), 'force_reset_password' => false]);

        return $user;
    }

    private function schoolClass(): LegacySchoolClass
    {
        /** @var LegacySchoolClass $schoolClass */
        $schoolClass = LegacySchoolClassFactory::new()->create();

        LegacyAcademicYearStageFactory::new()->create([
            'escola_ano_letivo_id' => LegacySchoolAcademicYearFactory::new()->create([
                'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
                'ano' => $schoolClass->ano,
            ]),
        ]);

        return $schoolClass;
    }

    /**
     * O middleware `can` do projeto nega o acesso redirecionando de volta
     * com a mensagem de erro de permissão.
     */
    private function assertDenied($response): void
    {
        $response->assertRedirect();
        $response->assertSessionHasErrors(['Error' => 'Você não tem permissão para acessar este recurso']);
    }

    /**
     * Autentica o usuário e registra na sessão os dados que o login real
     * grava, usados pelo layout das telas.
     */
    private function login(User $user): static
    {
        $this->actingAs($user);

        app(LoginLegacySession::class)->handle(new Login('web', $user, false));

        return $this;
    }

    public static function routes(): array
    {
        return [
            'CVE-2025-10013 exportação para o SEB' => ['/exportacao-para-o-seb', Process::SEB_EXPORT],
            'CVE-2025-11048 consulta de dispensas' => ['/consulta-dispensas', Process::EXEMPTION_LIST],
            'CVE-2025-11049 unificação de alunos' => ['/unificacao-aluno', Process::STUDENT_UNIFICATION],
            'CVE-2025-11050 período de lançamento' => ['/periodo-lancamento', Process::RELEASE_PERIOD],
            'CVE-2025-9609 consulta do Educacenso' => ['/educacenso/consulta', Process::EDUCACENSO_CONSULT],
            'CVE-2025-11554 cadastro de tipo de usuário' => ['/usuarios/tipos/novo', Process::USER_TYPES],
        ];
    }

    #[DataProvider('routes')]
    public function test_rota_bloqueia_usuario_sem_permissao(string $uri, int $process)
    {
        $this->assertDenied(
            $this->login($this->schoolUser())->get($uri)
        );
    }

    #[DataProvider('routes')]
    public function test_rota_permite_usuario_com_permissao(string $uri, int $process)
    {
        $this->login($this->schoolUser($process))
            ->get($uri)
            ->assertSuccessful();
    }

    public static function schoolClassRoutes(): array
    {
        return [
            'CVE-2025-10070 enturmação em lote' => ['/enturmacao-em-lote/%d'],
            'CVE-2025-10071 desenturmação em lote' => ['/cancelar-enturmacao-em-lote/%d'],
        ];
    }

    #[DataProvider('schoolClassRoutes')]
    public function test_lote_bloqueia_usuario_sem_permissao(string $uri)
    {
        $schoolClass = $this->schoolClass();

        $this->assertDenied(
            $this->login($this->schoolUser(school: $schoolClass->school))
                ->get(sprintf($uri, $schoolClass->getKey()))
        );
    }

    #[DataProvider('schoolClassRoutes')]
    public function test_lote_bloqueia_turma_de_outra_escola(string $uri)
    {
        $schoolClass = $this->schoolClass();

        $this->login($this->schoolUser(Process::BATCH_ENROLLMENT))
            ->get(sprintf($uri, $schoolClass->getKey()))
            ->assertForbidden();
    }

    #[DataProvider('schoolClassRoutes')]
    public function test_lote_permite_turma_da_propria_escola(string $uri)
    {
        $schoolClass = $this->schoolClass();

        $this->login($this->schoolUser(Process::BATCH_ENROLLMENT, $schoolClass->school))
            ->get(sprintf($uri, $schoolClass->getKey()))
            ->assertSuccessful();
    }

    public function test_historico_de_enturmacoes_bloqueia_usuario_sem_permissao()
    {
        /** @var LegacyRegistration $registration */
        $registration = LegacyRegistrationFactory::new()->create();

        $this->assertDenied(
            $this->login($this->schoolUser(school: $registration->school))
                ->get('/enrollment-history/' . $registration->getKey())
        );
    }

    public function test_historico_de_enturmacoes_bloqueia_matricula_de_outra_escola()
    {
        /** @var LegacyRegistration $registration */
        $registration = LegacyRegistrationFactory::new()->create();

        $this->login($this->schoolUser(Process::ENROLLMENT_HISTORY))
            ->get('/enrollment-history/' . $registration->getKey())
            ->assertForbidden();
    }

    public function test_historico_de_enturmacoes_permite_matricula_da_propria_escola()
    {
        /** @var LegacyRegistration $registration */
        $registration = LegacyRegistrationFactory::new()->create();

        $this->login($this->schoolUser(Process::ENROLLMENT_HISTORY, $registration->school))
            ->get('/enrollment-history/' . $registration->getKey())
            ->assertSuccessful();
    }
}
