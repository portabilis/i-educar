<?php

use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('abre os anos escolares do componente curricular', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacyDisciplineAcademicYearFactory::new()->create(['componente_curricular_id' => $discipline->getKey()]);

    $this->get('/module/ComponenteCurricular/ano?cid=' . $discipline->getKey())->assertOk();
});
