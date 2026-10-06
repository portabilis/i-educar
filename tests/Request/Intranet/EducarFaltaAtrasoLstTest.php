<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyAbsenceDelayFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->employee = EmployeeFactory::new()->create(['id' => LegacyIndividualFactory::new()->create()->idpes]);
    $this->keys = 'ref_cod_servidor=' . $this->employee->getKey() . '&ref_cod_instituicao=' . $this->employee->institution_id;
    $this->absence = LegacyAbsenceDelayFactory::new()->create([
        'ref_cod_servidor' => $this->employee,
        'ref_ref_cod_instituicao' => $this->employee->institution_id,
    ]);
    $this->keys .= '&ref_cod_escola=' . $this->absence->ref_cod_escola;
});

test('lista as faltas e atrasos do servidor', function () {
    $this->get('/intranet/educar_falta_atraso_lst.php?' . $this->keys)
        ->assertOk()
        ->assertSee('educar_falta_atraso_det.php?cod_falta_atraso=' . $this->absence->getKey(), false);
});
