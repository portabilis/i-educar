<?php

use App\Rules\CanChangeExitDate;
use App\Services\iDiarioService as IdiarioService;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->bindIdiarioService = function (array $responsePayload, array &$history): void {
        config([
            'legacy.config.url_novo_educacao' => 'https://idiario.example/',
            'legacy.config.token_novo_educacao' => 'idiario-token',
        ]);

        $institution = LegacyInstitutionFactory::new()->create();

        LegacyGeneralConfigurationFactory::new()->create([
            'ref_cod_instituicao' => $institution->getKey(),
            'url_novo_educacao' => 'https://idiario.example/',
            'token_novo_educacao' => 'idiario-token',
        ]);

        $client = ($this->createHttpClient)([
            new Response(200, [], json_encode($responsePayload)),
        ], $history);

        $this->app->instance(IdiarioService::class, new IdiarioService(
            $institution->load('generalConfiguration'),
            $client
        ));
    };

    $this->buildStudentActivityPayload = function (string $scenario): array {
        $disciplines = collect([
            LegacyDisciplineFactory::new()->create(['name' => 'Matemática']),
            LegacyDisciplineFactory::new()->create(['name' => 'História']),
            LegacyDisciplineFactory::new()->create(['name' => 'Geografia']),
            LegacyDisciplineFactory::new()->create(['name' => 'Ciências']),
            LegacyDisciplineFactory::new()->create(['name' => 'Português']),
            LegacyDisciplineFactory::new()->create(['name' => 'Artes']),
        ]);

        return match ($scenario) {
            'ignored_only' => [
                'student_activity' => [
                    ['type' => 'descriptive_exam'],
                    ['type' => 'general_descriptive_exam'],
                ],
            ],
            'with_disciplines' => [
                'student_activity' => [
                    ['type' => 'general_descriptive_exam'],
                    [
                        'type' => 'daily_note',
                        'disciplines' => $disciplines->map->getKey()->all(),
                    ],
                ],
            ],
            'without_disciplines' => [
                'student_activity' => [
                    ['type' => 'transfer_note'],
                ],
            ],
        };
    };

    $this->createHttpClient = function (array $queue, array &$history): Client {
        $mockHandlerClass = 'GuzzleHttp\\Handler\\MockHandler';
        $handlerStackClass = 'GuzzleHttp\\HandlerStack';
        $middlewareClass = 'GuzzleHttp\\Middleware';

        $mockHandler = new $mockHandlerClass($queue);
        $handlerStack = $handlerStackClass::create($mockHandler);
        $handlerStack->push($middlewareClass::history($history));

        return new Client([
            'handler' => $handlerStack,
        ]);
    };
});

test('can change exit date uses real student activity rules', function (string $scenario,
    bool $expectedResult,
    array $expectedMessageParts,
    array $unexpectedMessageParts) {
    $history = [];
    $responsePayload = ($this->buildStudentActivityPayload)($scenario);

    ($this->bindIdiarioService)($responsePayload, $history);

    $rule = new CanChangeExitDate;
    $exitDate = new DateTime('2024-12-15');

    $this->assertSame($expectedResult, $rule->passes('exit_date', [
        'student_id' => 42,
        'exit_date' => $exitDate,
    ]));

    $this->assertCount(1, $history);

    $request = $history[0]['request'];
    parse_str($request->getUri()->getQuery(), $actualQuery);

    $this->assertSame('GET', $request->getMethod());
    $this->assertStringStartsWith(rtrim('https://idiario.example/', '/') . '/api/v2/student_activity', (string) $request->getUri());
    $this->assertEquals([
        'student_id' => '42',
        'exit_date' => '2024-12-15',
    ], $actualQuery);
    $this->assertSame('idiario-token', $request->getHeaderLine('token'));

    if (!$expectedResult) {
        $message = $rule->message();

        foreach ($expectedMessageParts as $messagePart) {
            $this->assertStringContainsString($messagePart, $message);
        }

        foreach ($unexpectedMessageParts as $messagePart) {
            $this->assertStringNotContainsString($messagePart, $message);
        }
    }
})->with([
    'ignored activity types do not block the exit date' => [
        'ignored_only',
        true,
        [],
        [],
    ],
    'discipline activities summarize only the first five disciplines' => [
        'with_disciplines',
        false,
        ['Diário de avaliações', 'Matemática', 'História', 'Geografia', 'Ciências', 'Português', 'e outras'],
        ['general_descriptive_exam', 'Artes'],
    ],
    'activities without disciplines still block the exit date' => [
        'without_disciplines',
        false,
        ['Notas de transferência', 'Sim'],
        [],
    ],
]);
