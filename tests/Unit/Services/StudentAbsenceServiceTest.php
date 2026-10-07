<?php

namespace Tests\Unit\Services;

use App\Models\LegacyStudentAbsence;
use App\Services\StudentAbsenceService;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyStudentAbsenceFactory;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use RegraAvaliacao_Model_TipoPresenca;
use Tests\TestCase;

class StudentAbsenceServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_creates_the_absence_record_once_per_registration(): void
    {
        $service = new StudentAbsenceService;
        $registration = LegacyRegistrationFactory::new()->create();
        $rule = LegacyEvaluationRuleFactory::new()->create(['tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE]);

        $absence = $service->getOrCreateStudentAbsence($registration, $rule);

        $this->assertInstanceOf(LegacyStudentAbsence::class, $absence);
        $this->assertSame($registration->getKey(), $absence->matricula_id);
        $this->assertSame(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE, $absence->tipo_falta);
        $this->assertSame($absence->getKey(), $service->getOrCreateStudentAbsence($registration->fresh(), $rule)->getKey());
        $this->assertDatabaseCount('modules.falta_aluno', 1);
    }

    // Defeito conhecido: a chave do cache é o JSON da matrícula, que muda depois
    // que a relação `studentAbsence` é carregada (como nula) na primeira chamada;
    // a segunda chamada com a mesma instância não acha o cache nem a relação e
    // tenta criar de novo.
    public function test_defeito_chamar_duas_vezes_com_a_mesma_instancia_duplica_o_registro(): void
    {
        $service = new StudentAbsenceService;
        $registration = LegacyRegistrationFactory::new()->create();
        $rule = LegacyEvaluationRuleFactory::new()->create();

        $service->getOrCreateStudentAbsence($registration, $rule);

        $this->expectException(UniqueConstraintViolationException::class);

        $service->getOrCreateStudentAbsence($registration, $rule);
    }

    public function test_reuses_the_existing_absence_record(): void
    {
        $registration = LegacyRegistrationFactory::new()->create();
        $existing = LegacyStudentAbsenceFactory::new()->create(['matricula_id' => $registration]);
        $rule = LegacyEvaluationRuleFactory::new()->create();

        $this->assertSame($existing->getKey(), (new StudentAbsenceService)->getOrCreateStudentAbsence($registration->fresh(), $rule)->getKey());
    }
}
