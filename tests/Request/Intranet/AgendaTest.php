<?php

use App\Models\LegacyAgenda;
use App\Models\LegacyAgendaCommitment;
use App\Models\LegacyAgendaResponsible;
use App\Services\AgendaService;
use Database\Factories\LegacyUserFactory;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('abre a agenda do usuário em uma data', function () {
    $this->get('/intranet/agenda.php?time=' . now()->timestamp)->assertOk();
});

test('grava um compromisso na agenda', function () {
    $this->post('/intranet/agenda.php', [
        'novo_titulo' => 'Reunião Alfa',
        'novo_descricao' => 'Pauta da reunião',
        'novo_data' => now()->format('d/m/Y'),
        'novo_hora_inicio' => '10:00',
        'novo_hora_fim' => '11:00',
    ])->assertOk();
});

// Os compromissos são criados pela própria tela; não há factory de agenda.
function agendaCommitment(array $data = []): int
{
    test()->post('/intranet/agenda.php', $data + [
        'novo_titulo' => 'Reunião Alfa',
        'novo_descricao' => 'Pauta da reunião',
        'novo_data' => '10/03/2030',
        'novo_hora_inicio' => '10:00',
        'novo_hora_fim' => '11:00',
    ])->assertOk();

    return (int) LegacyAgendaCommitment::query()->max('cod_agenda_compromisso');
}

function agendaDay(array $query = []): TestResponse
{
    return test()->get('/intranet/agenda.php?' . http_build_query($query + ['time' => strtotime('2030-03-10')]))->assertOk();
}

test('avisa o que falta ao gravar um compromisso', function () {
    $this->post('/intranet/agenda.php', ['novo_hora_inicio' => '10:00'])
        ->assertOk()
        ->assertSee('Você deve preencher o campo Título ou o campo Descrição');

    $this->post('/intranet/agenda.php', ['novo_titulo' => 'Reunião', 'novo_data' => '31/02/2030', 'novo_hora_inicio' => '10:00'])
        ->assertOk()
        ->assertSee('Você deve preencher o campo Data corretamente.');

    $this->post('/intranet/agenda.php', ['novo_titulo' => 'Reunião', 'novo_data' => '10/03/2030', 'novo_hora_inicio' => '25:00'])
        ->assertOk()
        ->assertSee('Você deve preencher o campo Hora de Início corretamente.');
});

test('repete o compromisso pelos dias pedidos e o mostra no dia, com os avisos importantes e o histórico de versões', function () {
    $agenda = LegacyAgenda::query()->whereOwner($this->user->getKey())->first() ?? app(AgendaService::class)->firstOrCreate($this->user->getKey());
    $agenda->update(['publica' => 1]);
    $words = implode(' ', array_fill(0, 25, 'palavra'));

    $commitment = agendaCommitment(['novo_descricao' => $words, 'novo_repetir_dias' => 7, 'novo_repetir_qtd' => 2, 'novo_publico' => 1, 'novo_importante' => 1]);

    expect(LegacyAgendaCommitment::query()->where('ref_cod_agenda', $agenda->getKey())->count())->toBe(2);

    agendaDay()
        ->assertSee('Reunião Alfa')
        ->assertSee('Expandir este compromisso')
        ->assertSee('Evento Publico')
        ->assertSee('Importante')
        ->assertDontSee('Histórico');

    // Editar cria uma versão nova e libera o histórico.
    $this->post('/intranet/agenda.php', [
        'agenda_rap_id' => $commitment,
        'agenda_rap_titulo' => 'Reunião Beta',
        'agenda_rap_conteudo' => 'Pauta nova',
        'agenda_rap_data' => '10/03/2030',
        'agenda_rap_hora' => '14:00',
        'agenda_rap_horafim' => '15:00',
        'agenda_rap_publico' => 1,
    ])->assertOk();

    agendaDay()->assertSee('Reunião Beta')->assertSee('Histórico');

    agendaDay(['versoes' => $commitment])
        ->assertSeeInOrder(['Versão:', '2', 'Reunião Beta', 'Versão:', '1', 'Reunião Alfa'])
        ->assertSee('Clique aqui para reativar esta versão');

    agendaDay(['restaura' => $commitment, 'versao' => 1])
        ->assertSee('Versão 1 restaurada com sucesso.')
        ->assertSee('Reunião Alfa');

    agendaDay(['deletar' => $commitment]);

    expect(LegacyAgendaCommitment::query()->where('cod_agenda_compromisso', $commitment)->where('ativo', 1)->exists())->toBeFalse();
});

test('uma anotação do dia vira compromisso ao receber a hora de fim', function () {
    $note = agendaCommitment(['novo_titulo' => 'Ligar para a escola', 'novo_descricao' => '', 'novo_hora_fim' => '']);

    agendaDay()->assertSee('Salvar como Compromisso');

    $this->post('/intranet/agenda.php', ['grava_compromisso' => $note, 'grava_hora_fim' => 'x'])
        ->assertOk()
        ->assertSee('Você deve preencher o campo Hora de Fim corretamente.');

    $this->post('/intranet/agenda.php', ['grava_compromisso' => $note, 'grava_hora_fim' => '10:30', 'time' => strtotime('2030-03-10')])
        ->assertOk()
        ->assertSee('Ligar para a escola')
        ->assertDontSee('Salvar como Compromisso');
});

test('a agenda de outra pessoa só abre para o responsável', function () {
    $other = LegacyUserFactory::new()->create();
    $agenda = app(AgendaService::class)->firstOrCreate($other->getKey());

    $this->get('/intranet/agenda.php?cod_agenda=' . $agenda->getKey())->assertRedirect('agenda.php');

    LegacyAgendaResponsible::query()->create(['ref_cod_agenda' => $agenda->getKey(), 'ref_ref_cod_pessoa_fj' => $this->user->getKey()]);

    $this->get('/intranet/agenda.php?cod_agenda=' . $agenda->getKey())->assertOk();
});
