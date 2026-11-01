<?php

use App\Models\LegacyGeneralConfiguration;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Reports\Contracts\TeacherReportCard;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->id = $this->user->ref_cod_instituicao;
    $this->url = '/intranet/educar_configuracoes_gerais.php';

    // Os modelos de boletim do professor vêm do pacote de relatórios, que não
    // está instalado aqui.
    $this->app->instance(TeacherReportCard::class, new class implements TeacherReportCard
    {
        public function getOptions(): array
        {
            return [1 => 'Modelo padrão', 2 => 'Modelo resumido'];
        }
    });

    $this->config = fn (array $attributes = []) => LegacyGeneralConfigurationFactory::new()->create($attributes + [
        'ref_cod_instituicao' => $this->id,
        'active_on_ieducar' => true,
        'url_novo_educacao' => 'https://idiario.example',
        'token_novo_educacao' => 'token',
        'ieducar_entity_name' => 'Prefeitura Alfa',
        'tamanho_min_rede_estadual' => 5,
    ]);

    $this->form = fn (array $overrides = []) => LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides);
    $this->row = fn () => LegacyGeneralConfiguration::query()->find($this->id);
});

test('abre o formulário com as configurações da instituição ativa', function () {
    ($this->config)();

    $payload = ($this->form)();

    expect($payload['tipoacao'])->toBe('Editar')
        ->and($payload['url_novo_educacao'])->toBe('https://idiario.example')
        ->and($payload['token_novo_educacao'])->toBe('token')
        ->and($payload['ieducar_entity_name'])->toBe('Prefeitura Alfa')
        ->and($payload['tamanho_min_rede_estadual'])->toBe('5')
        ->and($payload['active_on_ieducar'])->toBe('1');
});

test('só o administrador abre as configurações gerais', function () {
    ($this->config)();
    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(999873)->create());

    $this->get($this->url)->assertRedirect('educar_index.php');
});

test('grava as caixas marcadas, os textos e os números, e limpa o que veio vazio', function () {
    ($this->config)(['facebook_url' => 'https://facebook.com/antigo']);

    $this->post($this->url, ($this->form)([
        'permite_relacionamento_posvendas' => 'on',
        'bloquear_cadastro_aluno' => 'on',
        'situacoes_especificas_atestados' => 'on',
        'emitir_ato_autorizativo' => 'on',
        'emitir_ato_criacao_credenciamento' => 'on',
        'url_novo_educacao' => 'https://novo.example',
        'token_novo_educacao' => 'novo-token',
        'mostrar_codigo_inep_aluno' => '1',
        'justificativa_falta_documentacao_obrigatorio' => '0',
        'tamanho_min_rede_estadual' => '7',
        'url_cadastro_usuario' => 'https://login.example/cliente',
        'active_on_ieducar' => '1',
        'ieducar_suspension_message' => 'Sistema em manutenção',
        'modelo_boletim_professor' => '2',
        'ieducar_image' => 'https://exemplo.test/logo.png',
        'ieducar_entity_name' => 'Prefeitura Beta',
        'ieducar_login_footer' => 'Rodapé do login',
        'ieducar_external_footer' => 'Rodapé externo',
        'ieducar_internal_footer' => 'Rodapé interno',
        'facebook_url' => '',
        'twitter_url' => 'https://twitter.com/beta',
        'linkedin_url' => 'https://www.linkedin.com/company/beta/',
    ]))
        ->assertRedirect('index.php')
        ->assertSessionHas('legacy', 'Edição efetuada com sucesso.<br>');

    expect(($this->row)())
        ->permite_relacionamento_posvendas->toEqual(1)
        ->bloquear_cadastro_aluno->toBeTrue()
        ->situacoes_especificas_atestados->toBeTrue()
        ->emitir_ato_autorizativo->toBeTrue()
        ->emitir_ato_criacao_credenciamento->toBeTrue()
        ->url_novo_educacao->toBe('https://novo.example')
        ->token_novo_educacao->toBe('novo-token')
        ->mostrar_codigo_inep_aluno->toEqual(1)
        ->justificativa_falta_documentacao_obrigatorio->toEqual(0)
        ->tamanho_min_rede_estadual->toEqual(7)
        ->url_cadastro_usuario->toBe('https://login.example/cliente')
        ->ieducar_suspension_message->toBe('Sistema em manutenção')
        ->modelo_boletim_professor->toEqual(2)
        ->ieducar_image->toBe('https://exemplo.test/logo.png')
        ->ieducar_entity_name->toBe('Prefeitura Beta')
        ->ieducar_login_footer->toBe('Rodapé do login')
        ->ieducar_external_footer->toBe('Rodapé externo')
        ->ieducar_internal_footer->toBe('Rodapé interno')
        ->facebook_url->toBeNull()
        ->twitter_url->toBe('https://twitter.com/beta')
        ->linkedin_url->toBe('https://www.linkedin.com/company/beta/');
});

test('desmarcar as caixas e esvaziar os campos limpa o que pode ser limpo, mantendo o resto', function () {
    ($this->config)(['permite_relacionamento_posvendas' => 1, 'bloquear_cadastro_aluno' => true, 'ieducar_image' => 'https://exemplo.test/logo.png']);

    $payload = ($this->form)([
        'token_novo_educacao' => '',
        'tamanho_min_rede_estadual' => 'abc',
        'ieducar_image' => '',
        'url_novo_educacao' => '',
        'ieducar_entity_name' => '',
        'modelo_boletim_professor' => '',
    ]);
    unset($payload['permite_relacionamento_posvendas'], $payload['bloquear_cadastro_aluno']);

    $this->post($this->url, $payload)->assertRedirect('index.php');

    expect(($this->row)())
        ->permite_relacionamento_posvendas->toEqual(0)
        ->bloquear_cadastro_aluno->toBeFalse()
        ->token_novo_educacao->toBeNull()
        ->tamanho_min_rede_estadual->toBeNull()
        ->ieducar_image->toBeNull()
        ->url_novo_educacao->toBe('https://idiario.example')
        ->ieducar_entity_name->toBe('Prefeitura Alfa');
});

test('sem configuração da instituição ativa, avisa que a edição não foi feita', function () {
    ($this->config)();
    $payload = ($this->form)();
    LegacyGeneralConfiguration::query()->whereKey($this->id)->delete();

    $this->post($this->url, $payload)
        ->assertOk()
        ->assertSee('Edição não realizada.');
});
