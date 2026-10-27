<?php

use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    // Uma segunda instituição, com valores conhecidos em todos os campos que a
    // listagem aceita como filtro.
    $this->institution = LegacyInstitutionFactory::new()->create([
        'nm_instituicao' => 'Instituição Zeta',
        'nm_responsavel' => 'Aaa Responsável Zeta',
        'ref_sigla_uf' => 'ZZ',
        'cep' => 88900123,
        'cidade' => 'Cidade Zeta',
        'bairro' => 'Bairro Zeta',
        'logradouro' => 'Rua Zeta',
        'numero' => 742,
        'complemento' => 'Sala Zeta',
        'ddd_telefone' => 48,
        'telefone' => 34221000,
    ]);

    $this->link = 'educar_instituicao_det.php?cod_instituicao=' . $this->institution->getKey();
});

test('lista as instituições ativas com o link para o detalhe', function () {
    $this->get('/intranet/educar_instituicao_lst.php')
        ->assertOk()
        ->assertSee('Instituição - Listagem')
        ->assertSee($this->link, false)
        ->assertSee('educar_instituicao_det.php?cod_instituicao=' . $this->user->ref_cod_instituicao, false);
});

test('não lista instituição inativa', function () {
    $this->institution->update(['ativo' => 0]);

    $this->get('/intranet/educar_instituicao_lst.php')
        ->assertOk()
        ->assertDontSee($this->link, false);
});

test('ordena pelo nome do responsável', function () {
    $content = $this->get('/intranet/educar_instituicao_lst.php')->assertOk()->getContent();

    $other = 'educar_instituicao_det.php?cod_instituicao=' . $this->user->ref_cod_instituicao;

    // O responsável desta instituição começa com "Aaa", então ela vem antes.
    expect(strpos($content, $this->link))->toBeLessThan(strpos($content, $other));
});

test('filtra por cada parâmetro aceito', function (string $param, string $match, string $miss) {
    $match = $match === ':id' ? (string) $this->institution->getKey() : $match;

    $this->get("/intranet/educar_instituicao_lst.php?{$param}=" . urlencode($match))
        ->assertOk()
        ->assertSee($this->link, false);

    $this->get("/intranet/educar_instituicao_lst.php?{$param}=" . urlencode($miss))
        ->assertOk()
        ->assertDontSee($this->link, false);
})->with([
    'nome' => ['nm_instituicao', 'Zeta', 'nome-que-nao-existe'],
    'código' => ['cod_instituicao', ':id', '0'],
    'UF' => ['ref_sigla_uf', 'ZZ', 'YY'],
    'CEP' => ['cep', '88900123', '11111111'],
    'cidade' => ['cidade', 'Cidade Zeta', 'cidade-que-nao-existe'],
    'bairro' => ['bairro', 'Bairro Zeta', 'bairro-que-nao-existe'],
    'logradouro' => ['logradouro', 'Rua Zeta', 'rua-que-nao-existe'],
    'número' => ['numero', '742', '1'],
    'complemento' => ['complemento', 'Sala Zeta', 'sala-que-nao-existe'],
    'responsável' => ['nm_responsavel', 'Responsável Zeta', 'ninguem'],
    'DDD' => ['ddd_telefone', '48', '11'],
    'telefone' => ['telefone', '34221000', '1'],
]);
