<?php

use Tests\TestCase;

uses(TestCase::class);

// O RD Station recebe as chamadas por um socket; aqui um servidor local faz o
// papel dele e devolve o que chegou.
function rdStationLocal(): array
{
    $server = stream_socket_server('tcp://127.0.0.1:0');
    $port = parse_url('tcp://' . stream_socket_get_name($server, false), PHP_URL_PORT);

    $api = new RDStationAPI('privado', 'publico');
    $api->baseURL = "http://127.0.0.1:{$port}/api/";

    return [$api, function () use ($server) {
        $client = stream_socket_accept($server, 1);
        $request = stream_get_contents($client);
        fclose($client);

        return $request;
    }];
}

afterEach(function () {
    unset($_COOKIE['rdtrk'], $_COOKIE['__trf_src']);
});

test('exige o token privado e, para criar leads, o token público', function () {
    expect(fn () => new RDStationAPI)->toThrow(Exception::class, 'Inform RDStationAPI.privateToken as the first argument.')
        ->and(fn () => (new RDStationAPI('privado'))->sendNewLead('ana@escola.br'))->toThrow(Exception::class, 'Inform RDStation.token as the second argument');
});

test('cria e atualiza o lead com o identificador padrão e os dados dos cookies', function () {
    [$api, $received] = rdStationLocal();
    $_COOKIE['rdtrk'] = json_encode(['id' => 'cliente-1']);
    $_COOKIE['__trf_src'] = 'google';

    expect(fn () => $api->sendNewLead(''))->toThrow(Exception::class, 'Inform at least the lead email as the first argument.')
        ->and($api->updateLead('ana@escola.br', ['nome' => 'Ana']))->toBeTrue();

    $request = $received();

    expect($request)->toStartWith('POST /api/1.3/conversions HTTP/1.1')
        ->toContain(trim(json_encode(['identificador' => 'Usuário no produto i-Educar']), '{}'))
        ->toContain('"client_id":"cliente-1"')
        ->toContain('"traffic_source":"google"')
        ->toContain('"token_rdstation":"publico"');
});

test('muda o estágio do lead no funil', function () {
    [$api, $received] = rdStationLocal();

    expect(fn () => $api->updateLeadStage(''))->toThrow(Exception::class, 'Inform lead email as the first argument.')
        ->and($api->updateLeadStage('ana@escola.br', 2))->toBeTrue()
        ->and($received())->toStartWith('PUT /api/1.3/leads/ana@escola.br HTTP/1.1')->toContain('"lifecycle_stage":2');
});

test('marca o lead como ganho ou perdido, pelo e-mail ou pelo identificador', function () {
    [$api, $received] = rdStationLocal();

    expect(fn () => $api->updateLeadStatus('', 'won'))->toThrow(Exception::class, 'Inform lead email or unique custom ID as the first argument.')
        ->and(fn () => $api->updateLeadStatus('ana@escola.br', ''))->toThrow(Exception::class, 'Inform lead status as the second argument.')
        ->and(fn () => $api->updateLeadStatus('ana@escola.br', 'talvez'))->toThrow(Exception::class, "Lead status (second argument) should be 'won' or 'lost'.")
        ->and($api->updateLeadStatus(10, 'lost', null, 'Preço'))->toBeTrue()
        ->and($received())->toStartWith('POST /api/1.3/services/privado/generic HTTP/1.1')->toContain('"lead_id":10');

    [$api, $received] = rdStationLocal();

    expect($api->updateLeadStatus('ana@escola.br', 'won', 100))->toBeTrue()
        ->and($received())->toContain('"email":"ana@escola.br"');
});
