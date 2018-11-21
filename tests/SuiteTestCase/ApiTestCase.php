<?php

namespace Tests\SuiteTestCase;

use GuzzleHttp\Client;

class ApiTestCase extends TestCase
{
    /**
     * @var Client
     */
    private $http;

    public function setUp(): void
    {
        parent::setUp();
        $this->http = new Client();
    }

    public function doAuthenticatedRequest($resource, $params, $method = 'GET')
    {
        $params['access_key'] = $this->getApiKey();
        $params['secret_key'] = $this->getApiSecret();

        return $this->doRequest($resource, $params, $method);
    }

    public function doRequest($resource, $params, $method = 'GET')
    {
        if (!in_array($method, ['GET', 'POST'])) {
            throw new \Exception('Método não implementado');
        }

        $params['resource'] = $resource;
        $params['oper'] = 'get';

        $response = $this->http->request(
            'GET',
            $this->getApiUri(),
            [\GuzzleHttp\RequestOptions::QUERY => $params]
        );

        return $response->getBody()->getContents();
    }

    private function getApiUri()
    {
        return getenv('API_URI');
    }

    private function getApiKey()
    {
        return getenv('API_ACCESS_KEY');
    }

    private function getApiSecret()
    {
        return getenv('API_SECRET_KEY');
    }

    public function getJsonFile($fileName)
    {
        return __DIR__ . '/../Unit/assets/' . $fileName;
    }
}
