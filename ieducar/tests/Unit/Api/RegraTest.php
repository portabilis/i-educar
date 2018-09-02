<?php

namespace Tests\unit\Api;

use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use Tests\SuiteTestCase\ApiTestCase;
use Tests\SuiteTestCase\TestCase;

require_once __DIR__ . '/../../../modules/Api/Views/RegraController.php';

class RegraTest extends ApiTestCase
{
    public function getDataSet()
    {
        $this->setupDump('regraavaliacao.sql');
        return new DefaultDataSet();
    }

    public function _testRegression()
    {
        $responseBody = $this->doAuthenticatedRequest('regras', ['instituicao_id' => 1, 'ano' => '2018']);
        $this->assertJsonStringEqualsJsonFile($this->getJsonFile('regra_json_valid.json'), $responseBody);
    }
}