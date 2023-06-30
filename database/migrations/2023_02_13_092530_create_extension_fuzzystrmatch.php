<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('CREATE EXTENSION IF NOT EXISTS fuzzystrmatch');
    }
};
