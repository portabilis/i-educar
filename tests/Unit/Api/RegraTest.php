<?php

namespace Tests\unit\Api;

use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use Tests\SuiteTestCase\ApiTestCase;

class RegraTest extends ApiTestCase
{
    public function getDataSet()
    {
        $this->setupDump('regraavaliacao.sql');
        return new DefaultDataSet();
    }

    public function testRegression()
    {
        $this->markTestSkipped();

        $responseBody = $this->doAuthenticatedRequest('regras', ['instituicao_id' => 1, 'ano' => '2018']);

        $this->assertJsonStringEqualsJsonFile($this->getJsonFile('regra_json_valid.json'), $responseBody);
    }
}
