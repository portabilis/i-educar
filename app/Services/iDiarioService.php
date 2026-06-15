<?php

namespace App\Services;

use App\Models\LegacyGeneralConfiguration;
use App\Models\LegacyInstitution;
use Exception;
use GuzzleHttp\Client;
use RuntimeException;

class iDiarioService
{
    /**
     * @var Client
     */
    protected $http;

    /**
     * @var LegacyInstitution
     */
    protected $institution;

    /**
     * @var string
     */
    protected $apiUrl;

    /**
     * @var string
     */
    protected $apiToken;

    /**
     * @return void
     */
    public function __construct(LegacyInstitution $institution, Client $http)
    {
        $this->institution = $institution;
        $configs = $institution->generalConfiguration;

        if (empty($configs->url_novo_educacao) || empty($configs->token_novo_educacao)) {
            throw new RuntimeException('É necessário configurar a URL e Token de integração com o i-Diário.');
        }

        $this->http = $http;
        $this->apiUrl = trim($configs->url_novo_educacao, '/');
        $this->apiToken = trim($configs->token_novo_educacao);
    }

    public function getStepActivityByUnit(int $unitId, int $year, int $step): bool
    {
        try {
            $response = $this->get('/api/v2/step_activity', [
                'unity_id' => $unitId,
                'year' => $year,
                'step_number' => $step,
            ]);
            $body = trim((string) $response->getBody());

            if ($body === 'true') {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function getStepActivityByClassroom(int $classroomId, int $year, int $step): bool
    {
        try {
            $response = $this->get('/api/v2/step_activity', [
                'classroom_id' => $classroomId,
                'year' => $year,
                'step_number' => $step,
            ]);
            $body = trim((string) $response->getBody());

            if ($body === 'true') {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function getTeacherClassroomsActivity(int $teacherId, int $classroomId): bool
    {
        try {
            $response = $this->get('/api/v2/teacher_classrooms/has_activities', ['teacher_id' => $teacherId, 'classroom_id' => $classroomId]);
            $body = trim((string) $response->getBody());

            if ($body === 'true') {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function getClassroomsActivityByDiscipline(array $classroomId, int $disciplineId): bool
    {
        $data = [
            'classrooms' => implode(',', $classroomId),
            'discipline' => $disciplineId,
        ];

        try {
            $response = $this->get('/api/v2/discipline_activity', $data);
            $body = trim((string) $response->getBody());

            if ($body === 'true') {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * @return mixed
     */
    protected function get(string $path, array $query)
    {
        return $this->http->request('GET', $this->apiUrl . $path, [
            'query' => $query,
            'headers' => [
                'token' => $this->apiToken,
            ],
        ]);
    }

    /**
     * @return mixed
     */
    protected function delete(string $path, array $query = [])
    {
        return $this->http->request('DELETE', $this->apiUrl . $path, [
            'query' => $query,
            'headers' => [
                'token' => $this->apiToken,
            ],
        ]);
    }

    /**
     * @return mixed
     */
    protected function post(string $path, array $json)
    {
        return $this->http->request('POST', $this->apiUrl . $path, [
            'json' => $json,
            'headers' => [
                'token' => $this->apiToken,
            ],
        ]);
    }

    public function getDisciplineRecordsCount(array $params): array
    {
        try {
            $response = $this->post('/api/v2/discipline_records/count', [
                'year' => $params['year'],
                'unities' => $params['school_ids'] ?? [],
                'courses' => $params['course_ids'] ?? [],
                'grades' => $params['grade_ids'] ?? [],
                'disciplines' => $params['discipline_ids'] ?? [],
                'user' => $params['user_id'] ?? null,
            ]);

            return (array) json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new RuntimeException('Erro ao consultar i-Diário: ' . $e->getMessage());
        }
    }

    public function deleteDisciplineRecords(array $params): array
    {
        try {
            $payload = [
                'year' => $params['year'],
                'unities' => $params['school_ids'] ?? [],
                'courses' => $params['course_ids'] ?? [],
                'grades' => $params['grade_ids'] ?? [],
                'disciplines' => $params['discipline_ids'] ?? [],
                'user' => $params['user_id'] ?? null,
            ];

            if (!empty($params['operation_id'])) {
                $payload['operation_id'] = $params['operation_id'];
            }

            $response = $this->http->request('POST', $this->apiUrl . '/api/v2/discipline_records/destroy_batch', [
                'json' => $payload,
                'headers' => [
                    'token' => $this->apiToken,
                ],
                'timeout' => 600,
            ]);

            $body = $response->getBody()->getContents();
            $result = (array) json_decode($body, true);
            $result['_status_code'] = $response->getStatusCode();
            $result['_body'] = mb_substr($body, 0, 2000);

            return $result;
        } catch (Exception $e) {
            $detail = ['exception' => get_class($e), 'message' => $e->getMessage()];

            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $detail['status_code'] = $e->getResponse()->getStatusCode();
                $detail['body'] = mb_substr((string) $e->getResponse()->getBody(), 0, 2000);
            }

            throw new RuntimeException('Erro ao excluir do i-Diário: ' . json_encode($detail, JSON_UNESCAPED_UNICODE));
        }
    }

    public static function hasIdiarioConfigurations()
    {
        if (config('legacy.config.url_novo_educacao') && config('legacy.config.token_novo_educacao')) {
            return true;
        }

        // Fallback para contexto de Job (worker), onde o middleware LoadSettings não roda
        $config = LegacyGeneralConfiguration::query()
            ->whereHas('institution', fn ($q) => $q->where('ativo', 1))
            ->select('url_novo_educacao', 'token_novo_educacao')
            ->first();

        return $config->url_novo_educacao && $config->token_novo_educacao;
    }

    /**
     * @doc https://github.com/portabilis/novo-educacao/pull/3626#issue-577687036
     */
    public function getStudentActivity(int $studentId, \DateTime $exitDate): array
    {
        $data = [
            'student_id' => $studentId,
            'exit_date' => $exitDate->format('Y-m-d'),
        ];

        try {
            $response = $this->get('/api/v2/student_activity', $data);
            $body = (array) json_decode($response->getBody()->getContents());
        } catch (Exception $e) {
            throw new RuntimeException('Ocorreu um erro de integração com o i-Diário: '.$e->getMessage());
        }

        return $body;
    }
}
