<?php

namespace Tests\Api;

use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyIndividualFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PessoaParentCpfTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function test_new_parent_without_cpf_saves_the_reason_and_registers_timeline(): void
    {
        $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);

        $response = $this->postResource('/module/Api/Pessoa', $this->parentRequest('Pai Sem Cpf', [
            'possui_cpf' => 0,
            'document_absence_reason_id' => $reason->getKey(),
        ]));

        $response->assertOk();

        $individual = LegacyIndividual::findOrFail($response->json('pessoa_id'));

        $this->assertNull($individual->getRawOriginal('cpf'));
        $this->assertSame($reason->getKey(), $individual->document_absence_reason_id);
        $this->assertCount(1, $individual->timelines);
        $this->assertSame(['CPF' => null, 'Motivo da ausência' => $reason->name], $individual->timelines->first()->payload['after']);
    }

    public function test_parent_with_cpf_informed_clears_the_reason(): void
    {
        $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);
        $individual = LegacyIndividualFactory::new()->create(['cpf' => 0]);
        LegacyIndividual::query()->whereKey($individual->getKey())->update(['document_absence_reason_id' => $reason->getKey()]);

        $response = $this->postResource('/module/Api/Pessoa', $this->parentRequest('Pai Com Cpf', [
            'pessoa_id' => $individual->getKey(),
            'possui_cpf' => 1,
            'cpf' => '123.456.789-09',
        ]));

        $response->assertOk()->assertJsonPath('pessoa_id', (string) $individual->getKey());

        $individual->refresh();

        $this->assertSame('123.456.789-09', $individual->cpf);
        $this->assertNull($individual->document_absence_reason_id);
    }

    public function test_refuses_parent_with_cpf_state_without_number(): void
    {
        $response = $this->postResource('/module/Api/Pessoa', $this->parentRequest('Pai Recusado Sem Numero', [
            'possui_cpf' => 1,
        ]));

        $response->assertOk()->assertJsonPath('msgs.0.msg', 'É necessário o preenchimento do CPF.');
        $this->assertFalse(LegacyPerson::query()->where('nome', 'Pai Recusado Sem Numero')->exists());
    }

    public function test_refuses_parent_without_cpf_and_without_reason(): void
    {
        $response = $this->postResource('/module/Api/Pessoa', $this->parentRequest('Pai Recusado Sem Motivo', [
            'possui_cpf' => 0,
        ]));

        $response->assertOk()->assertJsonPath('msgs.0.msg', 'É necessário informar o motivo da ausência do CPF.');
        $this->assertFalse(LegacyPerson::query()->where('nome', 'Pai Recusado Sem Motivo')->exists());
    }

    public function test_refuses_system_reason_for_new_parent(): void
    {
        $systemReason = DocumentAbsenceReasonFactory::new()->create(['from_system' => true, 'requires_observation' => false]);

        $response = $this->postResource('/module/Api/Pessoa', $this->parentRequest('Pai Recusado Motivo Sistema', [
            'possui_cpf' => 0,
            'document_absence_reason_id' => $systemReason->getKey(),
        ]));

        $response->assertOk()->assertJsonPath('msgs.0.msg', 'O motivo da ausência do CPF informado não existe.');
        $this->assertFalse(LegacyPerson::query()->where('nome', 'Pai Recusado Motivo Sistema')->exists());
    }

    public function test_parent_details_treat_zero_cpf_as_without_cpf(): void
    {
        $withCpf = LegacyIndividualFactory::new()->create(['cpf' => 12345678909]);
        $zeroCpf = LegacyIndividualFactory::new()->create(['cpf' => 0]);
        $nullCpf = LegacyIndividualFactory::new()->create(['cpf' => null]);

        $this->assertSame(1, $this->parentDetails($withCpf)->json('possui_cpf'));
        $this->assertSame(0, $this->parentDetails($zeroCpf)->json('possui_cpf'));
        $this->assertSame(0, $this->parentDetails($nullCpf)->json('possui_cpf'));
    }

    private function parentDetails(LegacyIndividual $individual)
    {
        return $this->getResource('/module/Api/Pessoa', [
            'oper' => 'get',
            'resource' => 'pessoa-parent',
            'id' => $individual->getKey(),
        ]);
    }

    private function parentRequest(string $name, array $state): array
    {
        return array_merge([
            'oper' => 'post',
            'resource' => 'pessoa',
            'nome' => $name,
            'sexo' => 'M',
            'cpf' => '',
            'document_absence_reason_id' => '',
            'document_absence_observation' => '',
        ], $state);
    }
}
