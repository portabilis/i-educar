<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = LegacyInstitutionFactory::new()->create([
        'nm_instituicao' => 'Instituição Zeta',
        'nm_responsavel' => 'Responsável Zeta',
        'cidade' => 'Cidade Zeta',
        'bairro' => 'Bairro Zeta',
        'logradouro' => 'Rua Zeta',
        'numero' => 742,
        'complemento' => 'Sala Zeta',
        'ddd_telefone' => 48,
        'telefone' => 34221000,
    ]);

    $this->url = '/intranet/educar_instituicao_det.php?cod_instituicao=' . $this->institution->getKey();
});

test('mostra os dados da instituição', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSee('Instituição - Detalhe')
        ->assertSee('Instituição Zeta')
        ->assertSee('Responsável Zeta')
        ->assertSee('Cidade Zeta')
        ->assertSee('Bairro Zeta')
        ->assertSee('Rua Zeta')
        ->assertSee('Sala Zeta');
});

test('oferece editar e a documentação padrão a quem pode alterar instituição', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSee('educar_instituicao_cad.php?cod_instituicao=' . $this->institution->getKey(), false)
        ->assertSee('Documentação padrão');
});

test('não oferece editar a quem só pode visualizar instituição', function () {
    LegacyGeneralConfigurationFactory::new()->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'active_on_ieducar' => true,
    ]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(559, modify: false, remove: false)->create());

    $this->get($this->url)
        ->assertOk()
        ->assertDontSee('educar_instituicao_cad.php?cod_instituicao=' . $this->institution->getKey(), false)
        ->assertSee('Documentação padrão');
});

// Defeito conhecido: o detalhe não confere se a instituição existe e desenha a
// tela com os campos vazios, em vez de voltar para a listagem.
test('defeito: abre o detalhe vazio para uma instituição inexistente', function () {
    $this->get('/intranet/educar_instituicao_det.php?cod_instituicao=0')
        ->assertOk()
        ->assertSee('Instituição - Detalhe')
        ->assertDontSee('Instituição Zeta');
});
