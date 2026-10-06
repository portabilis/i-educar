<?php

use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->key = DB::table('portal.funcionario_vinculo')->insertGetId([
        'nm_vinculo' => 'Vínculo Alfa',
        'abreviatura' => 'VA',
    ], 'cod_funcionario_vinculo');
});

test('lista vínculo de funcionário', function () {
    $link = 'funcionario_vinculo_det.php?cod_func=' . $this->key;

    $this->get('/intranet/funcionario_vinculo_lst.php')
        ->assertOk()
        ->assertSee($link, false);
});
