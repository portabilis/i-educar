<?php

use App\Models\LegacyInstitution;
use App\Models\LegacyOrganization;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolManagerFactory;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\SchoolFormData;

/**
 * As regras que o formulário de escola só aplica quando a instituição obriga o
 * preenchimento dos campos do Censo. Cada caso parte de uma escola completa e
 * válida, estraga um ponto e confere a mensagem e que nada foi gravado.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->censo = fn (bool $on) => LegacyInstitution::query()
        ->whereKey($this->user->ref_cod_instituicao)
        ->update(['obrigar_campos_censo' => $on]);

    ($this->censo)(true);

    $this->school = LegacySchoolFactory::new()->withName('Escola Alfa')->complete()->withAddress()->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'sigla' => 'EALFA',
    ]);
    SchoolManagerFactory::new()->create(['school_id' => $this->school, 'chief' => true]);

    $this->url = '/intranet/educar_escola_cad.php?cod_escola=' . $this->school->getKey();

    // O código INEP vai preenchido porque a regra da escola sede compara os
    // dois campos, e dois campos vazios também contam como iguais.
    $this->payload = fn (array $overrides = []) => LegacyFields::from(
        $this->get($this->url)->assertOk()->getContent(),
        $overrides + ['sigla' => 'MUDOU', 'escola_inep_id' => '42000001'] + SchoolFormData::MULTIPLE,
    );

    $this->refuse = function (array $overrides, string $message) {
        $this->post($this->url, ($this->payload)($overrides))
            ->assertOk()
            ->assertSee($message, false);

        expect($this->school->refresh()->sigla)->toBe('EALFA');
    };
});

test('grava a escola completa quando o Censo é obrigatório', function () {
    $this->post($this->url, ($this->payload)())->assertRedirect('educar_escola_lst.php');

    expect($this->school->refresh()->sigla)->toBe('MUDOU');
});

test('recusa pelas regras do Censo', function (array $overrides, string $message) {
    ($this->refuse)($overrides, $message);
})->with([
    'escola privada sem categoria' => [
        ['dependencia_administrativa' => 4, 'categoria_escola_privada' => ''],
        'O campo categoria da escola privada é obrigatório para escolas em atividade de administração privada.',
    ],
    'escola privada sem mantenedora' => [
        ['dependencia_administrativa' => 4, 'categoria_escola_privada' => 2, 'mantenedora_escola_privada' => []],
        'O campo mantenedora da escola privada é obrigatório para escolas em atividade de administração privada.',
    ],
    'prédio escolar sem a forma de ocupação' => [
        ['local_funcionamento' => [3], 'condicao' => ''],
        'O campo: Forma de ocupação do prédio, deve ser informado quando o Local de funcionamento for prédio escolar.',
    ],
    'área de assentamento em zona urbana' => [
        ['localizacao_diferenciada' => 1, 'zona_localizacao' => 1],
        'O campo: Localização diferenciada da escola não pode ser preenchido com Área de assentamento quando o campo: Zona localização for Urbana',
    ],
    'sem cidade' => [
        ['city_id' => ''],
        'Cidade não informada',
    ],
    'esfera administrativa incompatível com a dependência' => [
        ['dependencia_administrativa' => 3, 'esfera_administrativa' => 1],
        'O campo: Esfera administrativa do conselho ou órgão responsável pela Regulamentação/Autorização, foi preenchido com um valor incorreto',
    ],
    'escola sede com INEP começando em zero' => [
        ['inep_escola_sede' => '01234567'],
        'O campo: Código escola sede não pode iniciar com 0.',
    ],
    'escola sede com INEP curto' => [
        ['inep_escola_sede' => '1234567'],
        'O campo: Código escola sede deve conter 8 dígitos.',
    ],
    'escola sede igual à própria escola' => [
        ['inep_escola_sede' => '42000001'],
        'O campo: Código da escola sede deve ser diferente do campo: Código INEP',
    ],
    'gestor sem cargo' => [
        ['managers_role_id' => ['']],
        'O campo: <b>Cargo do gestor</b> deve ser preenchido',
    ],
    'prédio compartilhado com a própria escola' => [
        ['codigo_inep_escola_compartilhada' => '42000001'],
        'O campo: Código da escola que compartilha o prédio 1, 2, 3, 4, 5 ou 6, deve ser diferente do Código INEP da escola atual.',
    ],
    'prédio compartilhado com a mesma escola duas vezes' => [
        ['codigo_inep_escola_compartilhada' => '42000002', 'codigo_inep_escola_compartilhada2' => '42000002'],
        'das escolas compartilhadas devem ser diferentes entre si.',
    ],
    'zero salas fora do prédio' => [
        ['numero_salas_utilizadas_fora_predio' => '0'],
        'O campo: <b>Quantidade de salas de aula utilizadas pela escola fora do prédio escolar</b> não pode ser preenchido com 0',
    ],
    'salas climatizadas sem salas em uso' => [
        ['numero_salas_utilizadas_dentro_predio' => '', 'numero_salas_utilizadas_fora_predio' => '', 'numero_salas_climatizadas' => '2'],
        'O campo: <b>Quantidade de salas de aula climatizadas (com ar-condicionado, aquecedor ou climatizador)</b> não pode ser preenchido quando os campos:',
    ],
    'zero salas climatizadas' => [
        ['numero_salas_climatizadas' => '0'],
        'O campo: <b>Quantidade de salas de aula climatizadas (com ar-condicionado, aquecedor ou climatizador)</b> não pode ser preenchido com 0',
    ],
    'mais salas climatizadas do que salas em uso' => [
        ['numero_salas_climatizadas' => '11'],
        'O campo: <b>Quantidade de salas de aula climatizadas (com ar-condicionado, aquecedor ou climatizador)</b> não pode ser maior que a soma dos campos:',
    ],
    'salas acessíveis sem salas em uso' => [
        ['numero_salas_utilizadas_dentro_predio' => '', 'numero_salas_utilizadas_fora_predio' => '', 'numero_salas_climatizadas' => '', 'numero_salas_acessibilidade' => '2'],
        'O campo: <b>Quantidade de salas de aula com acessibilidade para pessoas com deficiência ou mobilidade reduzida</b> não pode ser preenchido quando os campos:',
    ],
    'zero salas acessíveis' => [
        ['numero_salas_acessibilidade' => '0'],
        'O campo: <b>Quantidade de salas de aula com acessibilidade para pessoas com deficiência ou mobilidade reduzida</b> não pode ser preenchido com 0',
    ],
    'mais salas acessíveis do que salas em uso' => [
        ['numero_salas_acessibilidade' => '11'],
        'O campo: <b>Quantidade de salas de aula com acessibilidade para pessoas com deficiência ou mobilidade reduzida</b> não pode ser maior que a soma dos campos:',
    ],
    'cantinho da leitura sem salas em uso' => [
        ['numero_salas_utilizadas_dentro_predio' => '', 'numero_salas_utilizadas_fora_predio' => '', 'numero_salas_climatizadas' => '', 'numero_salas_acessibilidade' => '', 'numero_salas_cantinho_leitura' => '2'],
        'O campo: <b>Quantidade de salas de aula com Cantinho da Leitura para a Educação Infantil e o Ensino fundamental (Anos iniciais)</b> não pode ser preenchido quando os campos:',
    ],
    'zero salas com cantinho da leitura' => [
        ['numero_salas_cantinho_leitura' => '0'],
        'O campo: <b>Quantidade de salas de aula com Cantinho da Leitura para a Educação Infantil e o Ensino fundamental (Anos iniciais)</b> não pode ser preenchido com 0',
    ],
    'mais salas com cantinho da leitura do que salas em uso' => [
        ['numero_salas_cantinho_leitura' => '11'],
        'O campo: <b>Quantidade de salas de aula com Cantinho da Leitura para a Educação Infantil e o Ensino fundamental (Anos iniciais)</b> não pode ser maior que a soma dos campos:',
    ],
    // Só dispara com os dois campos como valor único; ver o defeito registrado abaixo.
    'dispositivos pessoais sem rede sem fio' => [
        ['equipamentos_acesso_internet' => '2', 'rede_local' => '2'],
        'O campo: <b>Rede local de interligação de computadores</b> deve estar preenchido com <b>Wireless</b> ou <b>A cabo e Wireless</b>',
    ],
    'zero computadores de mesa' => [
        ['quantidade_computadores_alunos_mesa' => '0'],
        'O campo: <b>Computadores de mesa</b> não pode ser preenchido com 0',
    ],
    'zero computadores portáteis' => [
        ['quantidade_computadores_alunos_portateis' => '0'],
        'O campo: <b>Computadores portáteis</b> não pode ser preenchido com 0',
    ],
    'zero tablets' => [
        ['quantidade_computadores_alunos_tablets' => '0'],
        'O campo: <b>Tablets</b> não pode ser preenchido com 0',
    ],
    'acesso por computador de mesa sem nenhuma quantidade' => [
        ['equipamentos_acesso_internet' => [1], 'quantidade_computadores_alunos_mesa' => '', 'quantidade_computadores_alunos_portateis' => '', 'quantidade_computadores_alunos_tablets' => ''],
        'Preencha pelo menos um dos campos da seção <b>Quantidade de computadores de uso dos alunos</b>',
    ],
    'zero televisões' => [['televisoes' => '0'], 'O campo: <b>Aparelho de Televisão</b> não pode ser preenchido com 0'],
    'zero aparelhos de DVD' => [['dvds' => '0'], 'O campo: <b>Aparelho de DVD/Blu-ray</b> não pode ser preenchido com 0'],
    'zero aparelhos de som' => [['aparelhos_de_som' => '0'], 'O campo: <b>Aparelho de som</b> não pode ser preenchido com 0'],
    'zero projetores' => [['projetores_digitais' => '0'], 'O campo: <b>Projetor Multimídia (Data show)</b> não pode ser preenchido com 0'],
    'zero lousas digitais' => [['lousas_digitais' => '0'], 'O campo: <b>Lousa digital</b> não pode ser preenchido com 0'],
    'mais de três línguas indígenas' => [
        ['codigo_lingua_indigena' => [1, 2, 3, 390]],
        'O campo: <b>Línguas indígenas</b>, não pode ter mais que 3 opções',
    ],
    'escola pública com contratação pela secretaria estadual' => [
        ['formas_contratacao_parceria_escola_secretaria_estadual' => [1]],
        'O campo <b>Forma(s) de contratação da parceria ou convênio entre a escola e a Secretaria estadual de educação</b> foi preenchido incorretamente.',
    ],
    'escola pública com contratação pela secretaria municipal' => [
        ['formas_contratacao_parceria_escola_secretaria_municipal' => [1]],
        'O campo <b>Forma(s) de contratação da parceria ou convênio entre a escola e a Secretaria municipal de educação</b> foi preenchido incorretamente.',
    ],
]);

/**
 * Escola privada: a categoria decide quais formas de contratação com as
 * secretarias de educação são aceitas.
 */
function escolaPrivada(object $test, array $overrides = []): array
{
    $school = LegacySchoolFactory::new()->withName('Escola Privada')->complete()->private()->withAddress()->create([
        'ref_cod_instituicao' => $test->user->ref_cod_instituicao,
        'sigla' => 'EPRIV',
    ]);
    SchoolManagerFactory::new()->create(['school_id' => $school, 'chief' => true]);
    LegacyOrganization::query()->whereKey($school->ref_idpes)->update(['cnpj' => '11222333000181']);

    $url = '/intranet/educar_escola_cad.php?cod_escola=' . $school->getKey();

    $payload = LegacyFields::from($test->get($url)->assertOk()->getContent(), $overrides + [
        'sigla' => 'MUDOU',
        'escola_inep_id' => '42000001',
        'mantenedora_escola_privada' => [1],
        'categoria_escola_privada' => 1,
        'formas_contratacao_parceria_escola_secretaria_estadual' => [4],
        'formas_contratacao_parceria_escola_secretaria_municipal' => [4],
    ] + SchoolFormData::MULTIPLE);

    return [$school, $url, $payload];
}

test('abre a edição de uma escola privada com o CNPJ formatado e a mantenedora dela', function () {
    [, , $payload] = escolaPrivada($this);

    expect($payload['tipoacao'])->toBe('Editar')
        ->and(urldecode($payload['cnpj_']))->toBe('11.222.333/0001-81')
        ->and($payload['cnpj_mantenedora_principal'])->toBe('11.222.333/0001-81')
        ->and($payload['categoria_escola_privada'])->toBe(1);
});

test('grava a escola privada particular com contrato de prestação de serviço', function () {
    [$school, $url, $payload] = escolaPrivada($this);

    $this->post($url, $payload)->assertRedirect('educar_escola_lst.php');

    expect($school->refresh())
        ->sigla->toBe('MUDOU')
        ->formas_contratacao_parceria_escola_secretaria_estadual->toBe('{4}')
        ->formas_contratacao_parceria_escola_secretaria_municipal->toBe('{4}');
});

test('recusa forma de contratação incompatível com a categoria da escola privada', function (array $overrides, string $message) {
    [$school, $url, $payload] = escolaPrivada($this, $overrides);

    $this->post($url, $payload)->assertOk()->assertSee($message, false);

    expect($school->refresh()->sigla)->toBe('EPRIV');
})->with([
    'secretaria estadual: comunitária com termo de colaboração' => [
        ['categoria_escola_privada' => 2, 'formas_contratacao_parceria_escola_secretaria_estadual' => [5]],
        'O campo <b>Forma(s) de contratação da parceria ou convênio entre a escola e a Secretaria estadual de educação</b> foi preenchido incorretamente.',
    ],
    'secretaria estadual: particular sem contrato de prestação de serviço' => [
        ['formas_contratacao_parceria_escola_secretaria_estadual' => [1]],
        'Quando o campo "Categoria da escola privada" for igual à "Particular" só é possível cadastrar "Contrato de prestação de serviço"',
    ],
    'secretaria estadual: particular com mais de uma forma' => [
        ['formas_contratacao_parceria_escola_secretaria_estadual' => [4, 1]],
        'Quando o campo "Categoria da escola privada" for igual à "Particular" só é possível cadastrar "Contrato de prestação de serviço"',
    ],
    'secretaria municipal: comunitária com termo de colaboração' => [
        ['categoria_escola_privada' => 2, 'formas_contratacao_parceria_escola_secretaria_estadual' => [1], 'formas_contratacao_parceria_escola_secretaria_municipal' => [5]],
        'O campo <b>Forma(s) de contratação da parceria ou convênio entre a escola e a Secretaria municipal de educação</b> foi preenchido incorretamente.',
    ],
    'secretaria municipal: particular sem contrato de prestação de serviço' => [
        ['formas_contratacao_parceria_escola_secretaria_municipal' => [1]],
        'Quando o campo "Categoria da escola privada" for igual à "Particular" só é possível cadastrar "Contrato de prestação de serviço"',
    ],
    'secretaria municipal: particular com mais de uma forma' => [
        ['formas_contratacao_parceria_escola_secretaria_municipal' => [4, 1]],
        'Quando o campo "Categoria da escola privada" for igual à "Particular" só é possível cadastrar "Contrato de prestação de serviço"',
    ],
]);

// Defeito conhecido: a regra converte os dois campos para inteiro, mas o
// formulário os envia como lista, e toda lista não vazia vira 1. Num envio
// normal a regra da rede sem fio nunca dispara.
test('defeito: a regra da rede sem fio não vale quando os campos chegam como lista', function () {
    $payload = ($this->payload)(['equipamentos_acesso_internet' => [2], 'rede_local' => [2]]);

    $this->post($this->url, $payload)->assertRedirect('educar_escola_lst.php');

    expect($this->school->refresh())
        ->equipamentos_acesso_internet->toBe('{2}')
        ->rede_local->toBe('{2}');
});

test('sem a obrigatoriedade do Censo, nenhuma dessas regras é aplicada', function () {
    ($this->censo)(false);
    cache()->flush();

    $payload = ($this->payload)(['televisoes' => '0', 'inep_escola_sede' => '42000001', 'numero_salas_climatizadas' => '99']);

    $this->post($this->url, $payload)->assertRedirect('educar_escola_lst.php');

    expect($this->school->refresh()->sigla)->toBe('MUDOU');
});
