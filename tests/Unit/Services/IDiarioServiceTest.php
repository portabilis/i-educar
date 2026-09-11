<?php

namespace Tests\Unit\Services;

use App\Services\iDiarioService as IdiarioService;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class IDiarioServiceTest extends TestCase
{
    use DatabaseTransactions;

    private const API_URL = 'https://idiario.example/';

    private const API_TOKEN = 'token-123';

    public function test_constructor_requires_url_and_token_configuration(): void
    {
        $institution = LegacyInstitutionFactory::new()->create();

        LegacyGeneralConfigurationFactory::new()->create([
            'ref_cod_instituicao' => $institution->getKey(),
            'url_novo_educacao' => null,
            'token_novo_educacao' => null,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('É necessário configurar a URL e Token de integração com o i-Diário.');

        $client = $this->createHttpClient();

        $this->instantiateService($institution->load('generalConfiguration'), $client);
    }

    #[DataProvider('activityQueriesProvider')]
    public function test_activity_queries_send_expected_parameters_and_interpret_response(
        string $method,
        array $arguments,
        string $path,
        array $query,
        string $responseBody,
        bool $expectedResult
    ): void {
        $history = [];
        $client = $this->createHttpClient([
            new Response(200, [], $responseBody),
        ], $history);

        $service = $this->createService($client);

        $this->assertSame($expectedResult, $service->{$method}(...$arguments));
        $this->assertCount(1, $history);

        $request = $history[0]['request'];
        parse_str($request->getUri()->getQuery(), $actualQuery);

        $this->assertEquals($this->normalizeQuery($query), $actualQuery);
        $this->assertEquals(self::API_TOKEN, $request->getHeaderLine('token'));
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringStartsWith(rtrim(self::API_URL, '/') . $path, (string) $request->getUri());
    }

    #[DataProvider('activityFailuresProvider')]
    public function test_activity_queries_return_false_when_integration_fails(
        string $method,
        array $arguments,
        string $path,
        array $query
    ): void {
        $history = [];
        $client = $this->createHttpClient([
            new RuntimeException('Timeout'),
        ], $history);

        $service = $this->createService($client);

        $this->assertFalse($service->{$method}(...$arguments));
        $this->assertCount(1, $history);

        $request = $history[0]['request'];
        parse_str($request->getUri()->getQuery(), $actualQuery);

        $this->assertEquals($this->normalizeQuery($query), $actualQuery);
        $this->assertEquals(self::API_TOKEN, $request->getHeaderLine('token'));
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringStartsWith(rtrim(self::API_URL, '/') . $path, (string) $request->getUri());
    }

    public function test_get_discipline_records_count_returns_integration_summary(): void
    {
        $params = [
            'year' => now()->year,
            'school_ids' => [1, 2],
            'course_ids' => [3],
            'grade_ids' => [4],
            'discipline_ids' => [5],
            'user_id' => 6,
        ];

        $expectedPayload = [
            'year' => $params['year'],
            'unities' => [1, 2],
            'courses' => [3],
            'grades' => [4],
            'disciplines' => [5],
            'user' => 6,
        ];

        $history = [];
        $client = $this->createHttpClient([
            new Response(200, [], json_encode([
                'total' => 12,
                'pending' => 3,
            ])),
        ], $history);

        $service = $this->createService($client);

        $this->assertEquals([
            'total' => 12,
            'pending' => 3,
        ], $service->getDisciplineRecordsCount($params));

        $this->assertCount(1, $history);

        $request = $history[0]['request'];

        $this->assertSame('POST', $request->getMethod());
        $this->assertStringStartsWith(rtrim(self::API_URL, '/') . '/api/v2/discipline_records/count', (string) $request->getUri());
        $this->assertEquals($expectedPayload, json_decode((string) $request->getBody(), true));
        $this->assertEquals(self::API_TOKEN, $request->getHeaderLine('token'));
    }

    public function test_delete_discipline_records_sends_operation_id_and_exposes_response_metadata(): void
    {
        $params = [
            'year' => now()->year,
            'school_ids' => [1],
            'course_ids' => [2],
            'grade_ids' => [3],
            'discipline_ids' => [4],
            'user_id' => 5,
            'operation_id' => 'operation-123',
        ];

        $expectedPayload = [
            'year' => $params['year'],
            'unities' => [1],
            'courses' => [2],
            'grades' => [3],
            'disciplines' => [4],
            'user' => 5,
            'operation_id' => 'operation-123',
        ];

        $responseBody = json_encode([
            'deleted' => 2,
            'queued' => false,
        ]);

        $history = [];
        $client = $this->createHttpClient([
            new Response(202, [], $responseBody),
        ], $history);

        $service = $this->createService($client);
        $result = $service->deleteDisciplineRecords($params);

        $this->assertEquals(2, $result['deleted']);
        $this->assertFalse($result['queued']);
        $this->assertEquals(202, $result['_status_code']);
        $this->assertStringContainsString('"deleted":2', $result['_body']);
        $this->assertCount(1, $history);

        $request = $history[0]['request'];

        $this->assertSame('POST', $request->getMethod());
        $this->assertStringStartsWith(rtrim(self::API_URL, '/') . '/api/v2/discipline_records/destroy_batch', (string) $request->getUri());
        $this->assertEquals($expectedPayload, json_decode((string) $request->getBody(), true));
        $this->assertEquals(self::API_TOKEN, $request->getHeaderLine('token'));
    }

    #[DataProvider('disciplineRecordFailureProvider')]
    public function test_discipline_record_requests_surface_integration_failures(
        string $method,
        array $params,
        \Throwable $failure,
        string $path,
        array $expectedPayload,
        array $expectedMessageParts
    ): void {
        $history = [];
        $client = $this->createHttpClient([$failure], $history);

        $service = $this->createService($client);

        try {
            $service->{$method}($params);

            $this->fail('Expected RuntimeException to be thrown.');
        } catch (RuntimeException $e) {
            foreach ($expectedMessageParts as $messagePart) {
                $this->assertStringContainsString($messagePart, $e->getMessage());
            }
        }

        $this->assertCount(1, $history);

        $request = $history[0]['request'];

        $this->assertSame('POST', $request->getMethod());
        $this->assertStringStartsWith(rtrim(self::API_URL, '/') . $path, (string) $request->getUri());
        $this->assertEquals($expectedPayload, json_decode((string) $request->getBody(), true));
        $this->assertEquals(self::API_TOKEN, $request->getHeaderLine('token'));
    }

    public static function disciplineRecordFailureProvider(): array
    {
        $year = now()->year;
        $requestExceptionClass = 'GuzzleHttp\\Exception\\RequestException';
        $baseParams = [
            'year' => $year,
            'school_ids' => [1],
            'course_ids' => [2],
            'grade_ids' => [3],
            'discipline_ids' => [4],
            'user_id' => 5,
        ];

        return [
            'count wraps upstream timeout' => [
                'getDisciplineRecordsCount',
                $baseParams,
                new RuntimeException('Timeout'),
                '/api/v2/discipline_records/count',
                [
                    'year' => $year,
                    'unities' => [1],
                    'courses' => [2],
                    'grades' => [3],
                    'disciplines' => [4],
                    'user' => 5,
                ],
                ['Erro ao consultar i-Diário: Timeout'],
            ],
            'delete exposes upstream response details' => [
                'deleteDisciplineRecords',
                array_merge($baseParams, ['operation_id' => 'operation-123']),
                new $requestExceptionClass(
                    'Service unavailable',
                    new Request('POST', 'https://idiario.example/api/v2/discipline_records/destroy_batch'),
                    new Response(503, [], json_encode(['error' => 'down']))
                ),
                '/api/v2/discipline_records/destroy_batch',
                [
                    'year' => $year,
                    'unities' => [1],
                    'courses' => [2],
                    'grades' => [3],
                    'disciplines' => [4],
                    'user' => 5,
                    'operation_id' => 'operation-123',
                ],
                [
                    'Erro ao excluir do i-Diário:',
                    'RequestException',
                    '"status_code":503',
                    '"body":"{\\"error\\":\\"down\\"}"',
                ],
            ],
        ];
    }

    public static function activityQueriesProvider(): array
    {
        $year = now()->year;

        return [
            'step activity by unit returns true' => [
                'getStepActivityByUnit',
                [10, $year, 2],
                '/api/v2/step_activity',
                [
                    'unity_id' => 10,
                    'year' => $year,
                    'step_number' => 2,
                ],
                'true',
                true,
            ],
            'step activity by classroom returns false' => [
                'getStepActivityByClassroom',
                [12, $year, 1],
                '/api/v2/step_activity',
                [
                    'classroom_id' => 12,
                    'year' => $year,
                    'step_number' => 1,
                ],
                'false',
                false,
            ],
            'teacher classroom activity returns true' => [
                'getTeacherClassroomsActivity',
                [8, 15],
                '/api/v2/teacher_classrooms/has_activities',
                [
                    'teacher_id' => 8,
                    'classroom_id' => 15,
                ],
                'true',
                true,
            ],
            'discipline classroom activity returns false' => [
                'getClassroomsActivityByDiscipline',
                [[3, 4], 9],
                '/api/v2/discipline_activity',
                [
                    'classrooms' => '3,4',
                    'discipline' => 9,
                ],
                'false',
                false,
            ],
        ];
    }

    public static function activityFailuresProvider(): array
    {
        $scenarios = [];

        foreach (self::activityQueriesProvider() as $name => [$method, $arguments, $path, $query]) {
            $scenarios[$name] = [$method, $arguments, $path, $query];
        }

        return $scenarios;
    }

    private function createService($client): IdiarioService
    {
        $institution = LegacyInstitutionFactory::new()->create();

        LegacyGeneralConfigurationFactory::new()->create([
            'ref_cod_instituicao' => $institution->getKey(),
            'url_novo_educacao' => self::API_URL,
            'token_novo_educacao' => self::API_TOKEN,
        ]);

        return $this->instantiateService($institution->load('generalConfiguration'), $client);
    }

    private function instantiateService($institution, $client): IdiarioService
    {
        return new IdiarioService($institution, $client);
    }

    private function createHttpClient(array $queue = [], array &$history = [])
    {
        $mockHandlerClass = 'GuzzleHttp\\Handler\\MockHandler';
        $handlerStackClass = 'GuzzleHttp\\HandlerStack';
        $middlewareClass = 'GuzzleHttp\\Middleware';
        $clientClass = 'GuzzleHttp\\Client';

        $mockHandler = new $mockHandlerClass($queue);
        $handlerStack = $handlerStackClass::create($mockHandler);
        $handlerStack->push($middlewareClass::history($history));

        return new $clientClass([
            'handler' => $handlerStack,
        ]);
    }

    private function normalizeQuery(array $query): array
    {
        return array_map(static function ($value) {
            if (is_array($value)) {
                return array_map('strval', $value);
            }

            return (string) $value;
        }, $query);
    }
}
