<?php

use App\Models\File;
use App\Models\FileRelation;
use App\Models\LegacyStudent;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->student = LegacyStudentFactory::new()->create();
});

test('mostra o reconhecimento facial do aluno', function () {
    $this->get('/intranet/educar_reconhecimento_facial_det.php?ref_cod_aluno=' . $this->student->getKey())->assertOk();
});

test('mostra a imagem de cada posição enviada e volta à listagem para aluno inexistente', function () {
    config(['filesystems.default' => 'local']);
    $file = File::query()->create(['url' => 'https://arquivos.test/frente.png', 'size' => 10, 'original_name' => 'frente.png', 'extension' => 'png']);
    FileRelation::query()->create(['relation_type' => LegacyStudent::class, 'relation_id' => $this->student->getKey(), 'type' => 'FR', 'file_id' => $file->getKey()]);

    $this->get('/intranet/educar_reconhecimento_facial_det.php?ref_cod_aluno=' . $this->student->getKey())
        ->assertOk()
        ->assertSee("<img src='https://arquivos.test/frente.png'", false);

    $this->get('/intranet/educar_reconhecimento_facial_det.php?ref_cod_aluno=0')->assertRedirect('educar_aluno_lst.php');
});
