<?php

use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

// O caminho de sucesso desta tela termina em exit(), que encerraria o processo
// de teste; só o desvio anterior a ele é alcançável por requisição.
test('volta para a listagem de matrículas quando a matrícula não existe', function () {
    $this->get('/intranet/educar_matricula_abandono_cad.php?ref_cod_matricula=0&ref_cod_aluno=0')
        ->assertRedirect('educar_matricula_lst.php');
});
