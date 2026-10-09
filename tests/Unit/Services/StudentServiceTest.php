<?php

use App\Services\StudentService;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\ReligionFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * O que o serviço de aluno faz e a API de aluno não alcança: a guarda da
 * pessoa física, a normalização de cada campo opcional no cadastro e na
 * edição, a limpeza dos campos e a exclusão lógica com reativação.
 */
uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->row = function (int $id): object {
        return DB::table('pmieducar.aluno')->where('cod_aluno', $id)->first();
    };

    $this->service = new StudentService;
    $this->user = LegacyUserFactory::new()->admin()->create()->getKey();
    $this->person = LegacyIndividualFactory::new()->create()->getKey();
});

test('cadastra com todos os campos opcionais', function () {
    $religion = ReligionFactory::new()->create()->getKey();

    $id = $this->service->create([
        'ref_idpes' => $this->person,
        'ref_cod_religiao' => $religion,
        'rota_transporte' => 'Rota 7',
        'tipo_transporte' => 1,
        'utiliza_transporte_rural' => 1,
        'ref_usuario_cad' => $this->user,
        'analfabeto' => 1,
        'emancipado' => 1,
        'tipo_responsavel' => 'm',
        'aluno_estado_id' => 'SC-123',
        'recursos_prova_inep' => '{1,2}',
        'recebe_escolarizacao_em_outro_espaco' => 2,
        'justificativa_falta_documentacao' => 1,
        'url_documento' => '{"rg":"rg.pdf"}',
        'url_laudo_medico' => '{"laudo":"laudo.pdf"}',
        'codigo_sistema' => 'EXT-1',
        'veiculo_transporte_escolar' => '1,2',
        'autorizado_um' => 'Bia Borges',
        'parentesco_um' => 'Tia',
        // "NULL" é como a API manda um autorizado em branco
        'autorizado_dois' => 'NULL',
    ]);

    $row = ($this->row)($id);

    $this->assertSame($this->person, $row->ref_idpes);
    $this->assertSame($religion, $row->ref_cod_religiao);
    $this->assertSame('Rota 7', $row->rota_transporte);
    $this->assertSame(1, $row->tipo_transporte);
    $this->assertTrue($row->utiliza_transporte_rural);
    $this->assertSame($this->user, $row->ref_usuario_cad);
    $this->assertSame(1, $row->analfabeto);
    $this->assertTrue($row->emancipado);
    $this->assertSame('m', $row->tipo_responsavel);
    $this->assertSame('SC-123', $row->aluno_estado_id);
    $this->assertSame('{1,2}', $row->recursos_prova_inep);
    $this->assertSame(2, $row->recebe_escolarizacao_em_outro_espaco);
    $this->assertSame(1, $row->justificativa_falta_documentacao);
    $this->assertSame('{"rg":"rg.pdf"}', $row->url_documento);
    $this->assertSame('{"laudo":"laudo.pdf"}', $row->url_laudo_medico);
    $this->assertSame('EXT-1', $row->codigo_sistema);
    $this->assertSame('{1,2}', $row->veiculo_transporte_escolar);
    $this->assertSame('Bia Borges', $row->autorizado_um);
    $this->assertSame('Tia', $row->parentesco_um);
    $this->assertNull($row->autorizado_dois);
    $this->assertSame(1, $row->ativo);
    $this->assertNotNull($row->data_cadastro);
});

test('cadastra so com a pessoa e ignora os opcionais invalidos', function () {
    $id = $this->service->create([
        'ref_idpes' => (string) $this->person,
        'ref_cod_religiao' => 'nenhuma',
        'rota_transporte' => 7,
        'tipo_transporte' => 'ônibus',
        'analfabeto' => 'não',
        'emancipado' => 0,
        'tipo_responsavel' => 'mãe',
        'aluno_estado_id' => '',
        'url_documento' => '',
        'url_laudo_medico' => '',
        'autorizado_um' => ['não é texto'],
    ]);

    $row = ($this->row)($id);

    $this->assertSame($this->person, $row->ref_idpes);
    $this->assertNull($row->ref_cod_religiao);
    $this->assertNull($row->rota_transporte);
    $this->assertSame(0, $row->tipo_transporte);
    $this->assertFalse($row->utiliza_transporte_rural);
    $this->assertSame(0, $row->analfabeto);
    $this->assertFalse($row->emancipado);
    $this->assertNull($row->tipo_responsavel);
    $this->assertNull($row->aluno_estado_id);
    $this->assertNull($row->url_documento);
    $this->assertNull($row->url_laudo_medico);
    $this->assertNull($row->autorizado_um);
    $this->assertSame(1, $row->recebe_escolarizacao_em_outro_espaco);
    $this->assertSame(1, $row->ativo);
});

test('nao cadastra sem pessoa fisica', function () {
    $this->assertNull($this->service->create([]));
    $this->assertNull($this->service->create(['ref_idpes' => 'abc']));
    $this->assertNull($this->service->create(['ref_idpes' => 999999999]));

    $this->assertDatabaseMissing('pmieducar.aluno', ['ref_idpes' => 999999999]);
});

test('edita cada campo', function () {
    $id = LegacyStudentFactory::new()->create()->getKey();
    $religion = ReligionFactory::new()->create()->getKey();

    $this->assertTrue($this->service->update($id, [
        'ref_cod_religiao' => $religion,
        'ref_usuario_exc' => $this->user,
        'rota_transporte' => 'Rota 9',
        'utiliza_transporte_rural' => 1,
        'tipo_transporte' => 2,
        'ref_usuario_cad' => $this->user,
        'ref_idpes' => $this->person,
        'data_cadastro' => '2020-01-02 03:04:05',
        'analfabeto' => 1,
        'emancipado' => 1,
        'tipo_responsavel' => 'p',
        'aluno_estado_id' => 'SC-9',
        'recursos_prova_inep' => '{3}',
        'recebe_escolarizacao_em_outro_espaco' => 2,
        'justificativa_falta_documentacao' => 2,
        'url_documento' => '{"rg":"novo.pdf"}',
        'url_laudo_medico' => '{"laudo":"novo.pdf"}',
        'codigo_sistema' => 'EXT-2',
        'veiculo_transporte_escolar' => '3',
        'autorizado_um' => 'Caio Costa',
        'parentesco_um' => 'Avô',
    ]));

    $row = ($this->row)($id);

    $this->assertSame($religion, $row->ref_cod_religiao);
    $this->assertSame($this->user, $row->ref_usuario_exc);
    $this->assertSame('Rota 9', $row->rota_transporte);
    $this->assertTrue($row->utiliza_transporte_rural);
    $this->assertSame(2, $row->tipo_transporte);
    $this->assertSame($this->user, $row->ref_usuario_cad);
    $this->assertSame($this->person, $row->ref_idpes);
    $this->assertSame('2020-01-02 03:04:05', $row->data_cadastro);
    $this->assertSame(1, $row->analfabeto);
    $this->assertTrue($row->emancipado);
    $this->assertSame('p', $row->tipo_responsavel);
    $this->assertSame('SC-9', $row->aluno_estado_id);
    $this->assertSame('{3}', $row->recursos_prova_inep);
    $this->assertSame(2, $row->recebe_escolarizacao_em_outro_espaco);
    $this->assertSame(2, $row->justificativa_falta_documentacao);
    $this->assertSame('{"rg":"novo.pdf"}', $row->url_documento);
    $this->assertSame('{"laudo":"novo.pdf"}', $row->url_laudo_medico);
    $this->assertSame('EXT-2', $row->codigo_sistema);
    $this->assertSame('{3}', $row->veiculo_transporte_escolar);
    $this->assertSame('Caio Costa', $row->autorizado_um);
    $this->assertSame('Avô', $row->parentesco_um);
});

test('edita limpando os campos', function () {
    $id = LegacyStudentFactory::new()->create()->getKey();

    DB::table('pmieducar.aluno')->where('cod_aluno', $id)->update([
        'aluno_estado_id' => 'SC-1',
        'recursos_prova_inep' => '{1}',
        'justificativa_falta_documentacao' => 1,
        'veiculo_transporte_escolar' => '{1}',
        'autorizado_um' => 'Bia Borges',
    ]);

    $this->assertTrue($this->service->update($id, [
        'ref_cod_religiao' => 'NULL',
        'tipo_responsavel' => null,
        'recursos_prova_inep' => null,
        'justificativa_falta_documentacao' => null,
        'veiculo_transporte_escolar' => null,
        'autorizado_um' => 'NULL',
    ]));

    $row = ($this->row)($id);

    $this->assertNull($row->ref_cod_religiao);
    $this->assertNull($row->tipo_responsavel);
    $this->assertNull($row->aluno_estado_id);
    $this->assertNull($row->recursos_prova_inep);
    $this->assertNull($row->justificativa_falta_documentacao);
    $this->assertNull($row->veiculo_transporte_escolar);
    $this->assertNull($row->autorizado_um);
});

test('edicao mantem os campos nao enviados ou invalidos', function () {
    $student = LegacyStudentFactory::new()->create();
    $id = $student->getKey();

    DB::table('pmieducar.aluno')->where('cod_aluno', $id)->update([
        'aluno_estado_id' => 'SC-1',
        'recursos_prova_inep' => '{1}',
        'justificativa_falta_documentacao' => 1,
        'veiculo_transporte_escolar' => '{1}',
        'utiliza_transporte_rural' => true,
        'emancipado' => true,
        'url_documento' => '{"rg":"rg.pdf"}',
    ]);

    $this->assertTrue($this->service->update($id, [
        'ref_idpes' => 999999999,
        'ref_cod_religiao' => 'nenhuma',
        'tipo_responsavel' => 'mãe',
        // `false` é o valor que a API usa para "não mexer"
        'recursos_prova_inep' => false,
        'justificativa_falta_documentacao' => false,
        'veiculo_transporte_escolar' => false,
        'url_documento' => '',
    ]));

    $row = ($this->row)($id);

    $this->assertSame($student->ref_idpes, $row->ref_idpes);
    $this->assertSame($student->ref_cod_religiao, $row->ref_cod_religiao);
    $this->assertSame('a', $row->tipo_responsavel);
    $this->assertSame('{1}', $row->recursos_prova_inep);
    $this->assertSame(1, $row->justificativa_falta_documentacao);
    $this->assertSame('{1}', $row->veiculo_transporte_escolar);
    $this->assertTrue($row->utiliza_transporte_rural);
    $this->assertTrue($row->emancipado);
    $this->assertSame('{"rg":"rg.pdf"}', $row->url_documento);

    // A inscrição estadual é a exceção: some sempre que não vem no envio.
    $this->assertNull($row->aluno_estado_id);
});

test('nao edita aluno inexistente', function () {
    $this->assertFalse($this->service->update(999999999, ['rota_transporte' => 'Rota 1']));
});

test('exclui desativando e reativa', function () {
    $id = LegacyStudentFactory::new()->create()->getKey();

    $this->assertTrue($this->service->delete($id, $this->user));

    $row = ($this->row)($id);

    $this->assertSame(0, $row->ativo);
    $this->assertNotNull($row->data_exclusao);
    $this->assertSame($this->user, $row->ref_usuario_exc);

    $this->assertTrue($this->service->enable($id, $this->user));

    $this->assertSame(1, ($this->row)($id)->ativo);
});

test('nao exclui nem reativa aluno inexistente', function () {
    $this->assertFalse($this->service->delete(999999999, $this->user));
    $this->assertFalse($this->service->enable(999999999, $this->user));
});
