<?php

namespace Tests\Api;

use App\Models\LegacyIndividual;
use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PessoaCpfTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function test_informing_cpf_clears_reason_and_keeps_other_data(): void
    {
        $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);
        $individual = LegacyIndividualFactory::new()->create(['cpf' => null, 'sexo' => 'F', 'data_nasc' => '2010-05-10']);
        LegacyIndividual::query()->whereKey($individual->getKey())->update(['document_absence_reason_id' => $reason->getKey()]);

        $response = $this->postResource('/module/Api/Pessoa', $this->cpfRequest($individual->getKey(), [
            'possui_cpf' => 1,
            'cpf' => '123.456.789-09',
        ]));

        $response->assertOk()->assertJsonPath('pessoa_id', $individual->getKey());

        $individual->refresh();

        $this->assertSame('123.456.789-09', $individual->cpf);
        $this->assertNull($individual->document_absence_reason_id);
        $this->assertSame('F', $individual->sexo);
        $this->assertSame('2010-05-10', $individual->data_nasc->format('Y-m-d'));
        $this->assertCount(1, $individual->timelines);
        $this->assertSame(['CPF' => null, 'Motivo da ausência' => $reason->name], $individual->timelines->first()->payload['before']);
        $this->assertSame(['CPF' => '123.456.789-09'], $individual->timelines->first()->payload['after']);
    }

    public function test_zero_cpf_marked_without_cpf_clears_the_cpf_and_saves_the_reason(): void
    {
        $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);
        $individual = LegacyIndividualFactory::new()->create(['cpf' => 0]);

        $response = $this->postResource('/module/Api/Pessoa', $this->cpfRequest($individual->getKey(), [
            'possui_cpf' => 0,
            'document_absence_reason_id' => $reason->getKey(),
        ]));

        $response->assertOk()->assertJsonPath('pessoa_id', $individual->getKey());

        $individual->refresh();

        $this->assertNull($individual->getRawOriginal('cpf'));
        $this->assertSame($reason->getKey(), $individual->document_absence_reason_id);
    }

    public function test_refuses_system_reason_not_saved_for_the_person(): void
    {
        $systemReason = DocumentAbsenceReasonFactory::new()->create(['from_system' => true, 'requires_observation' => false]);
        $individual = LegacyIndividualFactory::new()->create(['cpf' => null]);

        $response = $this->postResource('/module/Api/Pessoa', $this->cpfRequest($individual->getKey(), [
            'possui_cpf' => 0,
            'document_absence_reason_id' => $systemReason->getKey(),
        ]));

        $response->assertOk()->assertJsonPath('msgs.0.msg', 'O motivo da ausência do CPF informado não existe.');
        $this->assertNull($individual->refresh()->document_absence_reason_id);
    }

    public function test_refuses_cpf_already_used_by_another_person(): void
    {
        $other = LegacyIndividualFactory::new()->create(['cpf' => 12345678909]);
        $individual = LegacyIndividualFactory::new()->create(['cpf' => null]);

        $response = $this->postResource('/module/Api/Pessoa', $this->cpfRequest($individual->getKey(), [
            'possui_cpf' => 1,
            'cpf' => '123.456.789-09',
        ]));

        $response->assertOk()->assertJsonPath('msgs.0.msg', "CPF já utilizado pela pessoa {$other->getKey()}.");
        $this->assertNull($individual->refresh()->getRawOriginal('cpf'));
    }

    public function test_accepts_inactive_reason_already_saved_for_the_person(): void
    {
        $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);
        $individual = LegacyIndividualFactory::new()->create(['cpf' => null]);
        LegacyIndividual::query()->whereKey($individual->getKey())->update(['document_absence_reason_id' => $reason->getKey()]);
        $reason->delete();

        $response = $this->postResource('/module/Api/Pessoa', $this->cpfRequest($individual->getKey(), [
            'possui_cpf' => 0,
            'document_absence_reason_id' => $reason->getKey(),
        ]));

        $response->assertOk()->assertJsonPath('pessoa_id', $individual->getKey());
        $this->assertSame($reason->getKey(), $individual->refresh()->document_absence_reason_id);
    }

    public function test_saving_without_change_does_not_register_timeline(): void
    {
        $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);
        $individual = LegacyIndividualFactory::new()->create(['cpf' => null]);
        LegacyIndividual::query()->whereKey($individual->getKey())->update(['document_absence_reason_id' => $reason->getKey()]);

        $response = $this->postResource('/module/Api/Pessoa', $this->cpfRequest($individual->getKey(), [
            'possui_cpf' => 0,
            'document_absence_reason_id' => $reason->getKey(),
        ]));

        $response->assertOk()->assertJsonPath('pessoa_id', $individual->getKey());
        $this->assertCount(0, $individual->refresh()->timelines);
    }

    public function test_refuses_unknown_person(): void
    {
        $response = $this->postResource('/module/Api/Pessoa', $this->cpfRequest(0, [
            'possui_cpf' => 1,
            'cpf' => '123.456.789-09',
        ]));

        $response->assertOk()->assertJsonPath('msgs.0.msg', 'Pessoa não encontrada.');
    }

    public function test_refuses_user_without_permission(): void
    {
        $individual = LegacyIndividualFactory::new()->create(['cpf' => null]);
        $data = array_merge([
            'access_key' => config('legacy.apis.access_key'),
            'secret_key' => config('legacy.apis.secret_key'),
        ], $this->cpfRequest($individual->getKey(), [
            'possui_cpf' => 1,
            'cpf' => '123.456.789-09',
        ]));
        $_POST = $data;

        $response = $this->actingAs(LegacyUserFactory::new()->institutional()->create())
            ->post('/module/Api/Pessoa?' . http_build_query($data));

        $response->assertOk()->assertJsonPath('msgs.0.msg', "Usuário sem permissão para cadastrar 'pessoa-cpf'.");
        $this->assertNull($individual->refresh()->getRawOriginal('cpf'));
    }

    private function cpfRequest(int $personId, array $state): array
    {
        return array_merge([
            'oper' => 'post',
            'resource' => 'pessoa-cpf',
            'pessoa_id' => $personId,
            'cpf' => '',
            'document_absence_reason_id' => '',
            'document_absence_observation' => '',
        ], $state);
    }
}
