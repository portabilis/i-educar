<?php

use App\Models\LegacyIndividualPicture;
use Database\Factories\LegacyIndividualPictureFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

// A gravação com sucesso termina em exit(), que encerraria o processo de teste;
// só as recusas anteriores a ele são alcançáveis por requisição.
test('recusa a troca de senha quando a confirmação não confere', function () {
    $payload = LegacyFields::from($this->get('/intranet/meusdados.php')->assertOk()->getContent(), [
        'senha' => 'Senha@Nova123',
        'senha_confirma' => 'Outra@Senha123',
    ]);

    $this->post('/intranet/meusdados.php', $payload)
        ->assertOk()
        ->assertSee('confirmação de senha');
});

test('mostra a foto atual e deixa trocá-la ou excluí-la', function () {
    LegacyIndividualPictureFactory::new()->create(['idpes' => $this->user->getKey(), 'caminho' => 'https://exemplo.test/foto.png']);

    $this->get('/intranet/meusdados.php')
        ->assertOk()
        ->assertSee('Foto atual')
        ->assertSee('https://exemplo.test/foto.png')
        ->assertSee('Excluir a foto');
});

test('recusa a matrícula que já é de outro usuário e a senha fraca', function () {
    $other = LegacyUserFactory::new()->create();
    $form = fn (array $overrides) => LegacyFields::from($this->get('/intranet/meusdados.php')->assertOk()->getContent(), $overrides);

    $this->post('/intranet/meusdados.php', $form(['matricula' => $other->employee->matricula]))
        ->assertOk()
        ->assertSee('A matrícula informada já pertence a outro usuário.');

    $this->post('/intranet/meusdados.php', $form(['senha' => 'fraca', 'senha_confirma' => 'fraca']))
        ->assertOk()
        ->assertSee('A senha deve conter pelo menos 8 caracteres');
});

// Nos testes o LegacyController monta $_FILES com os objetos de upload do
// Laravel, e a tela espera o array cru do PHP; a validação é chamada direto.
test('recusa a foto num formato que não é imagem antes de gravar qualquer coisa e aceita um PNG', function () {
    // O arquivo devolve a tela a cada inclusão; com require_once a segunda chamada receberia true.
    $page = require base_path('ieducar/intranet/meusdados.php'); // NOSONAR
    $arquivo = tempnam(sys_get_temp_dir(), 'foto');
    file_put_contents($arquivo, 'não é imagem');
    $_FILES = ['file' => ['name' => 'FOTO.TXT', 'type' => 'text/plain', 'tmp_name' => $arquivo, 'error' => 0, 'size' => 12]];

    try {
        expect($page->Editar())->toBeFalse()
            ->and(session('legacy'))->toContain('a foto possui um formato diferente daqueles permitidos');

        $imagem = imagecreatetruecolor(2, 2);
        imagepng($imagem, $arquivo);
        $_FILES = ['file' => ['name' => 'foto.png', 'type' => 'image/png', 'tmp_name' => $arquivo, 'error' => 0, 'size' => filesize($arquivo)]];

        expect($page->validatePhoto())->toBeTrue()
            ->and($page->objPhoto)->not->toBeNull();
    } finally {
        $_FILES = [];
        unlink($arquivo);
    }
});

// A gravação da foto roda antes do exit() do sucesso; aqui ela é chamada direto
// na tela, que o arquivo devolve pronta para uso.
test('grava a foto enviada, avisa quando o envio falha e apaga a foto quando pedido', function () {
    $page = require base_path('ieducar/intranet/meusdados.php'); // NOSONAR
    $id = $this->user->getKey();
    $envio = fn (string $caminho) => new class($caminho)
    {
        public function __construct(private string $caminho) {}

        public function sendPicture()
        {
            return $this->caminho;
        }
    };

    $page->objPhoto = $envio('');

    ob_start();
    $falhou = $page->savePhoto($id);

    expect(ob_get_clean())->toContain('Foto não salva.')
        ->and($falhou)->toBeFalse();

    $page->objPhoto = $envio('https://exemplo.test/nova.png');

    expect($page->savePhoto($id))->toBeTrue()
        ->and(LegacyIndividualPicture::query()->whereKey($id)->value('caminho'))->toBe('https://exemplo.test/nova.png')
        ->and(session('logged_user_picture'))->toBe('https://exemplo.test/nova.png');

    $page->objPhoto = null;
    $page->file_delete = 'on';

    expect($page->savePhoto($id))->toBeTrue()
        ->and(LegacyIndividualPicture::query()->whereKey($id)->exists())->toBeFalse();
});
