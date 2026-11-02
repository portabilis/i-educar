<?php

use App\Listeners\LoginLegacySession;
use App\Process;
use App\Services\FinalStatusImportService;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->login = function ($user) {
        $this->user = $user;
        $this->actingAs($user);
        app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($user->getKey()), false));
    };
    ($this->login)(LegacyUserFactory::new()->admin()->create());
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->registration = LegacyRegistrationFactory::new()->create(['ano' => now()->year, 'aprovado' => 3]);
    $this->csv = fn (string $rows) => UploadedFile::fake()->createWithContent('situacoes.csv', "ID Matrícula,Situação Final,Data de Saída\n{$rows}");
    $this->analysis = fn () => (new FinalStatusImportService)->analyzeUploadedFile(($this->csv)("{$this->registration->getKey()},Aprovado,"));
    $this->mapping = ['registration_id' => 'ID Matrícula', 'final_status' => 'Situação Final', 'exit_date' => 'Data de Saída'];
});

test('a tela abre para administradores e institucionais, com as situações e as colunas esperadas', function () {
    $this->get(route('final-status-import.index'))
        ->assertOk()
        ->assertViewHas('situations')
        ->assertViewHas('expectedColumns', (new FinalStatusImportService)->getExpectedColumns());

    ($this->login)(LegacyUserFactory::new()->school()->withAccess(Process::FINAL_STATUS_IMPORT)->create());

    $this->get(route('final-status-import.index'))->assertForbidden();
});

test('o envio exige um arquivo csv', function () {
    $this->from(route('final-status-import.index'))
        ->post(route('final-status-import.upload'), [])
        ->assertRedirect(route('final-status-import.index'))
        ->assertSessionHasErrors('file');

    $this->from(route('final-status-import.index'))
        ->post(route('final-status-import.upload'), ['file' => UploadedFile::fake()->create('foto.png', 1, 'image/png')])
        ->assertSessionHasErrors('file');
});

test('o envio analisa o arquivo, guarda a análise na sessão e segue para a análise', function () {
    $this->post(route('final-status-import.upload'), ['file' => ($this->csv)("{$this->registration->getKey()},Aprovado,")])
        ->assertRedirect(route('final-status-import.analysis'))
        ->assertSessionHas('success', 'Arquivo enviado e analisado com sucesso!')
        ->assertSessionHas('import_original_name', 'situacoes.csv')
        ->assertSessionHas('import_analysis', fn ($analysis) => $analysis['total_rows'] === 1);

    $this->withSession(['import_analysis' => ($this->analysis)()])
        ->get(route('final-status-import.analysis'))
        ->assertOk()
        ->assertViewHas('analysis');
});

test('um arquivo que não pode ser analisado volta com o erro', function () {
    $this->mock(FinalStatusImportService::class)->shouldReceive('analyzeUploadedFile')->andThrow(new RuntimeException('Arquivo vazio ou formato inválido.'));

    $this->from(route('final-status-import.index'))
        ->post(route('final-status-import.upload'), ['file' => ($this->csv)('')])
        ->assertRedirect(route('final-status-import.index'))
        ->assertSessionHasErrors(['file' => 'Erro ao processar o arquivo: Arquivo vazio ou formato inválido.']);
});

test('sem a análise na sessão, as etapas seguintes voltam ao início', function () {
    $this->get(route('final-status-import.analysis'))
        ->assertRedirect(route('final-status-import.index'))
        ->assertSessionHasErrors(['error' => 'Nenhum arquivo foi enviado. Por favor, faça o upload novamente.']);

    $this->get(route('final-status-import.mapping'))
        ->assertRedirect(route('final-status-import.index'))
        ->assertSessionHasErrors(['error' => 'Sessão expirada. Por favor, faça o upload do arquivo novamente.']);

    $this->post(route('final-status-import.import'), ['column_mapping' => $this->mapping])
        ->assertRedirect(route('final-status-import.index'))
        ->assertSessionHasErrors(['error' => 'Sessão expirada. Por favor, faça o upload do arquivo novamente.']);

    $this->get(route('final-status-import.status'))
        ->assertRedirect(route('final-status-import.index'))
        ->assertSessionHasErrors(['error' => 'Nenhuma importação foi processada.']);
});

test('o mapeamento sugere as colunas a partir dos cabeçalhos', function () {
    $this->withSession(['import_analysis' => ($this->analysis)()])
        ->get(route('final-status-import.mapping'))
        ->assertOk()
        ->assertViewHas('headers', ['ID Matrícula', 'Situação Final', 'Data de Saída'])
        ->assertViewHas('autoMapping', ['registration_id' => 0, 'final_status' => 1, 'exit_date' => 2]);
});

test('a importação exige o mapeamento com as colunas obrigatórias', function () {
    $session = ['import_analysis' => ($this->analysis)()];

    $this->withSession($session)->from(route('final-status-import.mapping'))
        ->post(route('final-status-import.import'), [])
        ->assertRedirect(route('final-status-import.mapping'))
        ->assertSessionHasErrors('column_mapping');

    $this->withSession($session)->from(route('final-status-import.mapping'))
        ->post(route('final-status-import.import'), ['column_mapping' => ['exit_date' => 'Data de Saída']])
        ->assertRedirect(route('final-status-import.mapping'))
        ->assertSessionHasErrors(['column_mapping' => 'Campos obrigatórios não mapeados: ID da Matrícula, Situação Final']);
});

test('a importação processa o arquivo, guarda o resultado e o status o mostra e esquece', function () {
    $this->withSession(['import_analysis' => ($this->analysis)()])
        ->post(route('final-status-import.import'), ['column_mapping' => $this->mapping])
        ->assertRedirect(route('final-status-import.status'))
        ->assertSessionHas('success', 'Importação concluída!')
        ->assertSessionHas('import_result', fn ($result) => $result['status'] === 'completed' && $result['processed'] === 1);

    expect($this->registration->refresh()->aprovado)->toBe(1);

    $this->withSession(['import_analysis' => [], 'import_result' => ['status' => 'completed', 'total' => 1, 'processed' => 1, 'ignored' => 0, 'errors' => [], 'warnings' => []]])
        ->get(route('final-status-import.status'))
        ->assertOk()
        ->assertViewHas('result')
        ->assertSessionMissing('import_result')
        ->assertSessionMissing('import_analysis');
});

test('um erro na importação volta com a mensagem', function () {
    $this->mock(FinalStatusImportService::class, function ($mock) {
        $mock->shouldReceive('getRequiredColumns')->andReturn(['registration_id', 'final_status']);
        $mock->shouldReceive('processImport')->andThrow(new RuntimeException('banco indisponível'));
    });

    $this->withSession(['import_analysis' => ['headers' => [], 'file_data' => []]])->from(route('final-status-import.mapping'))
        ->post(route('final-status-import.import'), ['column_mapping' => $this->mapping, 'ignore_approved' => 1])
        ->assertRedirect(route('final-status-import.mapping'))
        ->assertSessionHasErrors(['error' => 'Erro ao processar importação: banco indisponível']);
});
