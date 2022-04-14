<?php

namespace App\Services;

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
     * @param LegacyInstitution $institution
     * @param Client            $http
     *
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

    /**
     * @param int $unitId
     * @param int $step
     *
     * @return bool
     */
    public function getStepActivityByUnit(int $unitId, int $year, int $step): bool
    {
        try {
            $response = $this->get('/api/v2/step_activity', [
                'unity_id' => $unitId,
                'year' => $year,
                'step_number' => $step
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

    /**
     * @param int $classroomId
     * @param int $step
     *
     * @return bool
     */
    public function getStepActivityByClassroom(int $classroomId, int $year, int $step): bool
    {
        try {
            $response = $this->get('/api/v2/step_activity', [
                'classroom_id' => $classroomId,
                'year' => $year,
                'step_number' => $step
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
            'discipline' => $disciplineId
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
     * @param string $path
     * @param array  $query
     *
     * @return mixed
     */
    protected function get(string $path, array $query)
    {
        return $this->http->request('GET', $this->apiUrl . $path, [
            'query' => $query,
            'headers' => [
                'token' => $this->apiToken
            ]
        ]);
    }

    public static function hasIdiarioConfigurations()
    {
        return !empty(config('legacy.config.url_novo_educacao'))
            && !empty(config('legacy.config.token_novo_educacao'));
    }
}
