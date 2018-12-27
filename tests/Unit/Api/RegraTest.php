<?php

namespace Tests\Unit\Api;

use Tests\SuiteTestCase\ApiTestCase;

class RegraTest extends ApiTestCase
{
    public function testRegression()
    {
        $query = [
            'access_key' => $this->getApiKey(),
            'secret_key' => $this->getApiSecret(),
            'resource' => 'regras',
            'oper' => 'get',
            'instituicao_id' => 1,
            'ano' => '2018',
        ];

        $this->loadDump('regraavaliacao.sql');

        $response = $this->getJson('/module/Api/Regra?' . http_build_query($query));

        $this->assertJsonStringEqualsJsonFile(
            $this->getJsonFile('regra_json_valid.json'), $response->getContent()
        );
    }
}
