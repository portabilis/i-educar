<?php

use App\Models\FileRelation;
use App\Models\LegacyStudent;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->student = LegacyStudentFactory::new()->create();
    $this->url = '/intranet/educar_reconhecimento_facial_cad.php?ref_cod_aluno=' . $this->student->getKey();
});

test('abre o envio de imagens e exige ao menos uma imagem', function () {
    $this->get($this->url)->assertOk()->assertSee('Tipo de Imagem');

    $this->post($this->url, ['tipoacao' => 'Novo', 'ref_cod_aluno' => $this->student->getKey(), 'type' => 'FR'])
        ->assertOk()
        ->assertSee('Nenhuma imagem enviada.');
});

test('grava as imagens enviadas como reconhecimento facial do aluno, trocando a da mesma posição', function () {
    $image = fn (string $name) => json_encode([['url' => "https://arquivos.test/{$name}", 'size' => 10, 'originalName' => $name, 'extension' => 'png']]);
    $form = ['tipoacao' => 'Novo', 'ref_cod_aluno' => $this->student->getKey(), 'type' => 'FR'];

    $this->post($this->url, $form + ['file_url' => $image('frente.png')])
        ->assertRedirect('educar_reconhecimento_facial_det.php?ref_cod_aluno=' . $this->student->getKey());
    $this->post($this->url, $form + ['file_url' => $image('frente-nova.png')]);

    $relations = FileRelation::query()->where('relation_type', LegacyStudent::class)->where('relation_id', $this->student->getKey())->where('type', 'FR')->get();

    expect($relations)->toHaveCount(1)
        ->and($relations->first()->file->original_name)->toBe('frente-nova.png');
});

test('as imagens não são editadas nem excluídas por esta tela', function () {
    $this->post($this->url, ['tipoacao' => 'Editar'])->assertOk()->assertSee('Edição não realizada.');
    $this->post($this->url, ['tipoacao' => 'Excluir'])->assertOk()->assertSee('Exclusão não realizada.');
});
