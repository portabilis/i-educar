<?php

use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('pesquisa responsáveis pela agenda', function () {
    $this->get('/intranet/agenda_responsavel.php?pesquisa=nome-que-nao-existe')->assertOk();
});

test('lista as agendas do usuário e as que ele responde, filtrando pelo nome', function () {
    $other = LegacyUserFactory::new()->admin()->create();
    $agenda = fn (string $name, int $owner) => DB::table('portal.agenda')->insertGetId([
        'nm_agenda' => $name,
        'ref_ref_cod_pessoa_cad' => $owner,
        'ref_ref_cod_pessoa_own' => $owner,
        'data_cad' => now(),
    ], 'cod_agenda');

    $agenda('Agenda Alfa', $this->user->getKey());
    $shared = $agenda('Agenda Beta', $other->getKey());
    $agenda('Agenda Gama', $other->getKey());
    DB::table('portal.agenda_responsavel')->insert(['ref_cod_agenda' => $shared, 'ref_ref_cod_pessoa_fj' => $this->user->getKey()]);

    $this->get('/intranet/agenda_responsavel.php')
        ->assertOk()
        ->assertSeeInOrder(['Agenda Alfa', 'Agenda Beta'])
        ->assertDontSee('Agenda Gama');

    $this->get('/intranet/agenda_responsavel.php?pesquisa=Alfa')
        ->assertOk()
        ->assertSee('Agenda Alfa')
        ->assertDontSee('Agenda Beta');
});
