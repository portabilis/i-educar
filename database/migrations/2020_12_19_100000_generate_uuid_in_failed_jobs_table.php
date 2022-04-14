<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateUuidInFailedJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('failed_jobs')->whereNull('uuid')->cursor()->each(function ($job) {
            DB::table('failed_jobs')
                ->where('id', $job->id)
                ->update(['uuid' => (string) Str::uuid()]);
        });
    }
}
