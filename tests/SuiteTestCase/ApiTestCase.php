<?php

namespace Tests\SuiteTestCase;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ApiTestCase extends TestCase
{
    /**
     * Return access key for API use.
     *
     * @return string
     */
    protected function getApiKey()
    {
        return env('API_ACCESS_KEY');
    }

    /**
     * Return secret key for API use.
     *
     * @return string
     */
    protected function getApiSecret()
    {
        return env('API_SECRET_KEY');
    }

    /**
     * Return JSON filename.
     *
     * @param string $filename
     *
     * @return string
     */
    public function getJsonFile($filename)
    {
        return __DIR__ . '/../Unit/assets/' . $filename;
    }

    /**
     * Load a SQL dump file.
     *
     * @param string $filename
     *
     * @return void
     */
    public function loadDump($filename)
    {
        DB::unprepared('SET session_replication_role = replica;');
        DB::unprepared(file_get_contents(__DIR__ . '/../Unit/dumps/' . $filename));
        DB::unprepared('SET session_replication_role = DEFAULT;');
    }
}
