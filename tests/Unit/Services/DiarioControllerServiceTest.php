<?php

use App\Models\LegacyRegistration;
use App\Services\DiarioControllerService;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyRegistrationFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\Api\DiarioApiFakeDataTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiFakeDataTestTrait::class);

beforeEach(fn () => Cache::flush());

test('os componentes da turma deixam de fora o componente dispensado', function () {
    $enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    $this->createDisciplines($enrollment->schoolClass, 2);
    [$first, $second] = $enrollment->schoolClass->disciplines()->orderBy('id')->get()->all();
    $enrollment->schoolClass->update(['ref_cod_disciplina_dispensada' => $first->getKey()]);

    expect((new DiarioControllerService)->getComponentesTurma($enrollment->schoolClass->getKey())->pluck('id')->all())->toBe([$second->getKey()]);
});

test('o campo de recuperação vem da regra da matrícula ou da regra informada', function () {
    $enrollment = $this->getCommonFakeData(LegacyEvaluationRuleFactory::new()->create(['tipo_recuperacao_paralela' => RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPA]));
    $specific = new RegraAvaliacao_Model_Regra(['tipoRecuperacaoParalela' => RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPAS_ESPECIFICAS]);

    expect((new DiarioControllerService)->getCampoTipoRecuperacao($enrollment->registration->getKey()))->toBe('notaRecuperacaoParalela')
        ->and((new DiarioControllerService)->getCampoTipoRecuperacao(0, $specific))->toBe('notaRecuperacaoEspecifica');
});

test('grava a nota necessária no exame quando o componente está em exame', function () {
    $registration = LegacyRegistrationFactory::new()->create();
    $discipline = LegacyDisciplineFactory::new()->create();

    $boletim = Mockery::mock(Avaliacao_Service_Boletim::class);
    $boletim->shouldReceive('preverNotaRecuperacao')->with($discipline->getKey())->andReturn('6.5');
    $boletim->shouldReceive('getSituacaoComponentesCurriculares')->andReturn((object) [
        'componentesCurriculares' => [$discipline->getKey() => (object) ['situacao' => App_Model_MatriculaSituacao::EM_EXAME]],
    ]);

    $service = new class($boletim) extends DiarioControllerService
    {
        public function __construct(private Avaliacao_Service_Boletim $boletim) {}

        public function getServiceBoletim(int $turmaId, int $alunoId, ?LegacyRegistration $registration): Avaliacao_Service_Boletim
        {
            return $this->boletim;
        }
    };

    $service->atualizaNotaNecessariaExame(1, 1, $discipline->getKey(), $registration);

    expect(DB::table('modules.nota_exame')->where('ref_cod_matricula', $registration->getKey())->value('nota_exame'))->toEqual(6.5);
});
