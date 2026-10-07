<?php

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
