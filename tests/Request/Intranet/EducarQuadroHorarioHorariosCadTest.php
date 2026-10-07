<?php

use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Alfa']);
    $this->filters = [
        'ano' => $this->schoolClass->ano,
        'ref_cod_instituicao' => $this->schoolClass->ref_cod_instituicao,
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ref_cod_curso' => $this->schoolClass->ref_cod_curso,
        'ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
        'ref_cod_turma' => $this->schoolClass->getKey(),
    ];
    $this->schedule = DB::table('pmieducar.quadro_horario')->insertGetId([
        'ref_usuario_cad' => $this->user->getKey(),
        'ref_cod_turma' => $this->schoolClass->getKey(),
        'data_cadastro' => now(),
        'ativo' => 1,
        'ano' => $this->schoolClass->ano,
    ], 'cod_quadro_horario');
});

test('abre a edição dos horários de um dia do quadro', function () {
    $query = http_build_query($this->filters + [
        'ref_cod_quadro_horario' => $this->schedule,
        'dia_semana' => 2,
        'identificador' => 'teste' . $this->schedule,
    ]);

    $this->get('/intranet/educar_quadro_horario_horarios_cad.php?' . $query)->assertOk();
});
