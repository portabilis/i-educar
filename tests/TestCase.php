<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function disableForeignKeys()
    {
        DB::statement('SET session_replication_role = replica;');
    }

    protected function enableForeignKeys()
    {
        DB::statement('SET session_replication_role = DEFAULT;');
    }
}
