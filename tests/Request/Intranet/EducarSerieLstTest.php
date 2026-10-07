<?php

use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->grade = LegacyGradeFactory::new()->create(['nm_serie' => 'Série Alfa']);
});

test('lista as séries e filtra pelo nome', function () {
    $link = 'educar_serie_det.php?cod_serie=' . $this->grade->getKey();

    $this->get('/intranet/educar_serie_lst.php?nm_serie=Série Alfa')->assertOk()->assertSee($link, false);
    $this->get('/intranet/educar_serie_lst.php?nm_serie=nome-que-nao-existe')->assertOk()->assertDontSee($link, false);
});
